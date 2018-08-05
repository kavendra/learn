<?php

namespace Betta\Services\Generator\Streams\Programs;

use Exception;
use Betta\Models\Program;
use Illuminate\Support\MessageBag;
use Betta\Services\Generator\Drivers\WordTemplate;

class SignInSheetGenerator
{
    /**
     * Share Errors
     *
     * @var Collection
     */
    protected $errors;

    /**
     * Template
     *
     * @var string
     */
    protected $template = 'app/templates/program/sign_in_sheet.docx';

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
        'brands',
        'speakers',
        'programLocations.address.owner',
    ];

    /**
     * Class constructor
     *
     * @param Engagement $engagement
     * @param MessageBag $bag
     */
    public function __construct(Program $program, MessageBag $bag)
    {
        # implementations
        $this->errors  = $bag;
        $this->program = $program;
    }

    /**
     * Handling should be simple and should return instance of Document
     *
     * @param  array  $arguments
     * @return Instance
     */
    public function handle($arguments = array())
    {
        # If we can can get the profielContract from arguments, return the handling result
        if (!$program = $this->getProgram($arguments)) {
            # return errors
            return $this->getErrors();
        }
        # set the value to internal pointer
        return $this->process($program);
    }

    /**
     * Return errors
     *
     * @return Collection
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Obtain the data for populating the WordDocument
     *
     * @param  array $arguments
     * @return Program
     */
    protected function getProgram($arguments)
    {
        if ($program = array_get($arguments, 'program') AND $program instanceOf Program) {
            return $program->load($this->relations);
        }

        if ($id = array_get($arguments, 'id')) {
            return $this->program->with($this->relations)->findOrFail($id);
        }

        throw new Exception("No Program Provided for generator", 500);
    }

    /**
     * Get the Template Path
     *
     * @return String
     */
    protected function getTemplatePath(Program $program)
    {
        return data_get($program->primary_brand, 'sign_in_sheet_template.uri') ?: storage_path( $this->template );
    }


    /**
     * Produce the File
     *
     * @param  Program $program
     * @return File
     */
    protected function process(Program $program)
    {
        # make new template
        $template = new WordTemplate( $this->getTemplatePath($program) );
        # return new merged template as a steam
        # The methods will handle all the exceptions
        return $template->merge( $this->getMergeData($program) )
                        ->save( $this->getSavePath($program) )
                        ->convertToPdf();
    }

    /**
     * The path where to save the file to
     *
     * @param  Program $program
     * @return [type]
     */
    protected function getSavePath(Program $program)
    {
        return storage_path( "{$this->storagePath}/". $this->getFileName($program) );
    }

    /**
     * Compile a Name for the Resulting document
     *
     * @param  Program $program
     * @return string
     */
    protected function getFileName(Program $program)
    {
        return vsprintf( '%s - Sign In Sheet for %s.docx', [ md5(microtime()), $program->label ] );
    }

    /**
     * Return merge data from the Contract
     *
     * @param  Program $program
     * @param  array  $mergeData
     * @return Array
     */
    protected function getMergeData(Program $program, $mergeData = array())
    {
        foreach ($this->getDefinitions() as $key => $definition ){
            array_set($mergeData, $key, object_get($program, $definition, '') );
        }
        return $this->getAdditionalMerges($program, $mergeData);
    }

    /**
     * Get the items that simply definable
     *
     * @return array
     */
    protected function getDefinitions()
    {
        return[
            'program_id'        => 'id',
            'program_date_full' => 'full_start_date',
            'program_time_full' => 'full_start_time',
            'primary_rep'       => 'primary_field.preferred_name',
            'location_name'     => 'primaryLocation.name',
            'location_city'     => 'primaryLocation.address.city',
            'location_state'    => 'primaryLocation.address.state_province',
            'pm_name'           => 'primary_pm.preferred_name',
        ];
    }

    /**
     * Some items cannot be simply defined and need to be merged in additionally
     *
     * @param  Program $program
     * @param  array   $mergeData
     * @return array
     */
    protected function getAdditionalMerges(Program $program, $mergeData = array())
    {
        $injected = [
            'speakers_degrees'    => $program->primary_speakers->implode('preferred_name_degree', ', '),
            'brands'              => $this->getBrands($program),
            'presentation_topics' => $program->presentations->sortByDesc('is_primary')->implode('title', ', '),
            'hcp_registered'      => $program->hcp_registrations->count(),
            'pm_fax'              => config('fls.support_fax'),
            'support_fax'         => config('fls.support_fax'),
            'support_phone'       => config('fls.support_phone'),
            'support_email'       => config('fls.support_email'),
            'approval_code'       => '',
        ];

        # Get the Speaker Registrations
        # Get the Rep Registrations
        # Get the HCP Registrations
        # Filter only Registered (once resolved, we will not be able to see them)
        # Get the registration as array
        # If any property needs to be included, it has to be an accessor in the model
        $registrations = $program->speaker_registrations->sortBy('last_name')
                                 ->merge( $program->field_registrations->sortBy('last_name') )
                                 ->merge( $program->hcp_registrations->sortBy('last_name') )
                                 ->filter( function($registration){
                                        return $registration->is_registered;
                                    })
                                 ->makeHidden(['program', 'profile', 'registrationStatus', 'audienceType'])
                                 ->map( function($registration){
                                        return $registration->toArray();
                                    })
                                 ->toArray();

        # Push into array
        # Preserve the list
        array_set($injected, 'registrations', array_values($registrations) );

        return  array_merge($mergeData, $injected);
    }

    /**
     * Resolve Brand of the program by the Presentation
     *
     * @param  Program $program
     * @return string
     */
    protected function getBrands(Program $program)
    {
        if ($program->presentations->isEmpty()){
            return $program->brands->implode('label', ', ');
        }

        return $program->presentations->pluck('brand')->unique()->implode('label', ', ');
    }
}
