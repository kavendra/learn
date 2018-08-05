<?php

namespace Betta\Services\Generator\Streams\Profile\Debarment;

use Betta\Models\BackgroundCheck;
use Betta\Foundation\HasMessageBag;
use Betta\Models\BackgroundCheckProvider;
use Betta\Services\Generator\Drivers\WordTemplate;

class StatusGenerator
{
    use HasMessageBag;

    /**
     * Bind the implementation
     *
     * @var Betta\Models\BackgroundCheck
     */
    protected $backgroundCheck;

    /**
     * Bind the implementation
     *
     * @var Betta\Models\BackgroundCheckProvider
     */
    protected $provider;

    /**
     * Template Path
     *
     * @var string
     */
    protected $template = 'app/templates/profile/debarment/status.docx';

    /**
     * Location to store the file
     *
     * @var string
     */
    protected $storagePath = 'app/temp';

    /**
     * Relations to get along the document
     *
     * @var array
     */
    protected $relations = [
        'profile',
        'certifications.createdBy',
        'notes.createdBy',
    ];

    /**
     * Class constructor
     *
     * @param BackgroundCheck $backgroundCheck
     */
    public function __construct(BackgroundCheck $bgc, BackgroundCheckProvider $provider)
    {
        $this->provider = $provider;
        $this->backgroundCheck = $bgc->with($this->relations);
    }

    /**
     * Handling should be simple and should return instance of Document
     *
     * @param  array  $arguments
     * @return stObject
     */
    public function handle($arguments = array())
    {
        # If we can can get the profielContract from arguments, return the handling result
        if (!$backgroundCheck = $this->getBackgroundCheck($arguments)) {
            # return errors
            return $this->getErrors();
        }

        # set the value to internal pointer
        return $this->process($backgroundCheck);
    }

    /**
     * Return errors
     *
     * @return Collection
     */
    public function getErrors()
    {
        return $this->getMessageBag()->get('errors');
    }

    /**
     * Obtain the data for populating the WordDocument
     *
     * @param  array $arguments
     * @return BackgroundCheck
     * @throws NoModelProvided if not model is provided
     */
    protected function getBackgroundCheck($arguments)
    {
        if ($model = array_get($arguments, 'backgroundCheck') AND $model instanceOf BackgroundCheck) {
            return $model->load($this->relations);
        }

        if ($id = array_get($arguments, 'id')) {
            return $this->backgroundCheck->findOrFail($id);
        }

        throw new Exceptions\NoModelProvided();
    }

    /**
     * Get the Template Path
     *
     * @return String
     */
    protected function getTempaltePath()
    {
        return storage_path( $this->template );
    }

    /**
     * Produce the File
     *
     * @param  BackgroundCheck $backgroundCheck
     * @return strObject
     */
    protected function process(BackgroundCheck $backgroundCheck)
    {
        # make new template
        $template = new WordTemplate( $this->getTempaltePath() );

        # return new merged template as a steam
        # The methods will handle all the exceptions
        return $template->merge( $this->getMergeData($backgroundCheck) )
                        ->save( $this->getSavePath($backgroundCheck) )
                        ->convertToPdf();
    }

    /**
     * The path where to save the file to
     *
     * @param  BackgroundCheck $backgroundCheck
     * @return string
     */
    protected function getSavePath(BackgroundCheck $backgroundCheck)
    {
        return storage_path( "{$this->storagePath}/". $this->getFileName($backgroundCheck) );
    }

    /**
     * Compile a Name for the Resulting document
     *
     * @param  BackgroundCheck $backgroundCheck
     * @return string
     */
    protected function getFileName(BackgroundCheck $backgroundCheck)
    {
        $arguments = [
            'id' => md5(microtime()),
            'name' => "{$backgroundCheck->profile->preferred_name}",
        ];

        return vsprintf( '%s - Debarment Check for %s.docx', $arguments);
    }

    /**
     * Return merge data from the Contract
     *
     * @param  BackgroundCheck $backgroundCheck
     * @param  array  $mergeData
     * @return Array
     */
    protected function getMergeData(BackgroundCheck $backgroundCheck, $mergeData = array())
    {
        foreach ($this->getDefinitions() as $key => $definition ){
            array_set($mergeData, $key, object_get($backgroundCheck, $definition, ''));
        }

        return $this->getAdditionalMerges($backgroundCheck, $mergeData);
    }


    /**
     * Get the items that simply definable
     *
     * @return array
     */
    protected function getDefinitions()
    {
        return[
            'id'     => 'id',
            'by'     => 'createdBy.preferred_name',
            'of'     => 'createdBy.userProfile.company',
            'name'   => 'profile.preferred_name_degree',
            'cmid'   => 'profile.customer_master_id',
            'status' => 'status_label',
        ];
    }

    /**
     * Some items cannot be simply defined and need to be merged in additionally
     *
     * @param  BackgroundCheck $backgroundCheck
     * @param  array   $mergeData
     * @return array
     */
    protected function getAdditionalMerges(BackgroundCheck $bgc, $mergeData = array())
    {
        $injected = [
            'at' => empty($bgc->completed_at) ? '' : $bgc->completed_at->format(config('betta.full_date')),
            'date' => date(config('betta.full_date')),
            'notes' => $this->notes($bgc)->toArray(),
            'checks' => $this->certifications($bgc)->toArray(),
        ];

        return array_merge($mergeData, $injected);
    }

    /**
     * Get the list of Providers, match them to certifications
     *
     * @param  BackgroundCheck $bgc
     * @return Illuminate\Support\Collection
     */
    protected function certifications(BackgroundCheck $bgc)
    {
        return $this->getProviders()->transform(function($provider) use($bgc) {
            return (new Handlers\CertificationHandler($provider, $bgc))->fill();
        })->values();
    }

    /**
     * List of Providers
     *
     * @return Illuminate\Support\Collection
     */
    protected function getProviders()
    {
        return $this->provider->get();
    }

    /**
     * Get the formatted notes for the BG Check
     *
     * @param  BackgroundCheck $bgc
     * @return Illuminate\Support\Collection
     */
    protected function notes(BackgroundCheck $bgc)
    {
        return $bgc->notes->transform(function($note){
            return (new Handlers\NoteHandler($note))->fill();
        })->values();
    }
}
