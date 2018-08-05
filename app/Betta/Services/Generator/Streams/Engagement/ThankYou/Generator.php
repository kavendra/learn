<?php

namespace Betta\Services\Generator\Streams\Engagement\ThankYou;

use Betta\Models\Engagement;
use Betta\Services\Generator\Drivers\WordTemplate;
use Betta\Services\Generator\Foundation\AbstractGenerator;

class Generator extends AbstractGenerator
{
    /**
     * Bind the implementation
     *
     * @var Betta\Models\Engagement
     */
    protected $engagement;

    /**
     * Default Template
     *
     * @var string
     */
    protected $defaultTemplate = 'app/templates/engagement/thank_you_letter.docx';

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
    ];

    /**
     * Store the arguments
     *
     * @var array
     */
    protected $arguments = [];

    /**
     * Create new instance of the class
     *
     * @param Engagement $engagement
     */
    public function __construct(Engagement $engagement)
    {
        $this->engagement = $engagement;
    }

    /**
     * Handling should be simple and should return instance of Document
     *
     * @param  array  $arguments
     * @return Instance
     */
    public function handle($arguments = array())
    {
        # Obrain the model isntance
        $engagement = $this->engagement($arguments);
        # Process the generateor
        return $this->process($engagement);
    }

    /**
     * Fetch Engagement
     *
     * @param  array $arguments
     * @return Program
     */
    protected function engagement($arguments)
    {
        # is the value provided to us?
        if ($engagement = array_get($arguments, 'engagement') AND $engagement instanceOf Engagement) {
            return $engagement->load($this->relations);
        }
        # is the ID provided to us?
        if ($id = array_get($arguments, 'id')) {
            return $this->engagement->with($this->relations)->findOrFail($id);
        }

        throw new GeneratorException("No Engagement model passed to generator", 500);
    }

    /**
     * Produce the File
     *
     * @param  Engagement $engagement
     * @return array
     */
    protected function process(Engagement $engagement)
    {
        # make new template
        $template = new WordTemplate($this->getTemplatePath($engagement));

        # return new merged template as a steam
        # The methods will handle all the exceptions
        return $template->merge($this->getMergeData($engagement)->toArray())
                        ->save($this->getSavePath($engagement))
                        ->convertToPdf();
    }

    /**
     * Get real template path
     *
     * @param  Engagement $engagement
     * @return String
     */
    protected function getTemplatePath(Engagement $engagement)
    {
        return storage_path($this->defaultTemplate);
    }

    /**
     * The path where to save the file to
     *
     * @param  Engagement $engagement
     * @return string
     */
    protected function getSavePath(Engagement $engagement)
    {
        return storage_path("{$this->storagePath}/". $this->getFileName($engagement));
    }

    /**
     * Compile a Name for the Resulting document
     *
     * @param  Engagement $engagement
     * @return string
     */
    protected function getFileName(Engagement $engagement)
    {
        # Compile
        $filename = sprintf('%s - Thank you Letter for %s.docx', md5(microtime()), $engagement->label);
        # Sanitize
        $filename = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $filename);
        # Remove any runs of periods
        return mb_ereg_replace("([\.]{2,})", '', $filename);
    }

    /**
     * Return merge data from the Contract
     *
     * @param  Engagement $engagement
     * @return Array
     */
    protected function getMergeData(Engagement $engagement)
    {
        return (new MergeDataTransformer($engagement))->fill();
    }
}
