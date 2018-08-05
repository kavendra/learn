<?php

namespace Betta\Services\Generator\Streams\Speaker\ThankYou;

use Exception;
use Betta\Models\Program;
use Betta\Models\ProgramSpeaker;
use Betta\Services\Generator\Drivers\WordTemplate;

class Generator
{
    /**
     * Default Template
     *
     * @var string
     */
    protected $defaultTemplate = 'app/templates/speaker/thank_you_letter.docx';

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
        'program.pms',
        'program.fields',
        'program.brands.programTypes',
        'profile.addresses',
        'speakerProfile',
    ];

    /**
     * Store the arguments
     *
     * @var array
     */
    protected $arguments = [];

    /**
     * Bind the implementation
     *
     * @var Betta\Models\ProgramSpeaker
     */
    protected $programSpeaker;

    /**
     * Create new instance of the class
     *
     * @param ProgramSpeaker $programSpeaker
     */
    public function __construct(ProgramSpeaker $programSpeaker)
    {
        $this->programSpeaker = $programSpeaker;
    }

    /**
     * Handling should be simple and should return instance of Document
     *
     * @param  array  $arguments
     * @return Instance
     */
    public function handle($arguments = array())
    {
        # Obrain the Program Speaker
        $programSpeaker = $this->getProgramSpeaker($arguments);
        # Process the generateor
        return $this->process($programSpeaker);
    }


    /**
     * Fetch Program Speaker
     *
     * @param  array $arguments
     * @return Program
     */
    protected function getProgramSpeaker($arguments)
    {
        # is the value provided to us?
        if ($programSpeaker = array_get($arguments, 'programSpeaker') AND $programSpeaker instanceOf ProgramSpeaker) {
            return $programSpeaker->load($this->relations);
        }
        # is the ID provided to us?
        if ($id = array_get($arguments, 'id')) {
            return $this->programSpeaker->with($this->relations)->findOrFail($id);
        }

        throw new Exception("No Program Speaker for generator", 500);
    }

    /**
     * Get the Template Path
     * We would rely on the ProgramType to store the template; if not, rely on the default
     *
     * @return String
     */
    protected function getTemplatePath(Program $program)
    {
        return data_get($program, 'programType.thank_you_template.uri') ?: storage_path( $this->defaultTemplate );
    }

    /**
     * Produce the File
     *
     * @param  ProgramSpeaker $programSpeaker
     * @return array
     */
    protected function process(ProgramSpeaker $programSpeaker)
    {
        # make new template
        $template = new WordTemplate($this->getTemplatePath($programSpeaker->program));

        # return new merged template as a steam
        # The methods will handle all the exceptions
        return $template->merge($this->getMergeData($programSpeaker)->toArray())
                        ->save($this->getSavePath($programSpeaker))
                        ->convertToPdf();
    }

    /**
     * The path where to save the file to
     *
     * @param  ProgramSpeaker $programSpeaker
     * @return string
     */
    protected function getSavePath(ProgramSpeaker $programSpeaker)
    {
        return storage_path("{$this->storagePath}/". $this->getFileName($programSpeaker));
    }

    /**
     * Compile a Name for the Resulting document
     *
     * @param  ProgramSpeaker $programSpeaker
     * @return string
     */
    protected function getFileName(ProgramSpeaker $programSpeaker)
    {
        return vsprintf('%s - Thank you Letter for %s.docx', [md5(microtime()), $programSpeaker->program->label]);
    }

    /**
     * Return merge data from the Contract
     *
     * @param  ProgramSpeaker $programSpeaker
     * @return Array
     */
    protected function getMergeData(ProgramSpeaker $programSpeaker)
    {
        return (new MergeData($programSpeaker))->fill();
    }
}
