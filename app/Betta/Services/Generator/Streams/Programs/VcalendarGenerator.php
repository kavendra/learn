<?php

namespace Betta\Services\Generator\Streams\Programs;

use Betta\Models\Program;
use Betta\Services\Generator\Drivers\PlainTextTemplate;
use Betta\Services\Generator\Foundation\SanitizesStrings;
use Betta\Services\Generator\Foundation\AbstractGenerator;

class VcalendarGenerator extends AbstractGenerator
{
    use SanitizesStrings;

    /**
     * Bind the Implementation
     *
     * @var Betta\Models\Program
     */
    protected $program;

    /**
     * List the relations to load with program
     *
     * @var array
     */
    protected $relations = [
        'programLocations',
    ];

    /**
     * Locate the Tempalte to User
     *
     * @var string
     */
    protected $template = 'calendar.vcalendar.show';

    /**
     * Location to store the file
     *
     * @var string
     */
    protected $storagePath = 'app/temp';

    /**
     * Create New Instance of the class
     *
     * @param Program $program
     */
    public function __construct(Program $program)
    {
        $this->program = $program;
    }

    /**
     * Handle document generation
     *
     * @param  array  $arguments
     * @return array
     */
    public function handle($arguments = [])
    {
        # If we can can get the Program from arguments, return the handling result
        if ($program = $this->getProgram($arguments)) {
            return $this->process($program);
        }

        # return errors
        return $this->getErrors();
    }

    /**
     * Return program from DB
     *
     * @param  array $arguments
     * @return Program | Exception: ModelNotFound
     */
    protected function getProgram($arguments = [])
    {
        if ($program = array_get($arguments, 'program') AND $program instanceOf Program) {
            return $program->load($this->relations);
        }

        if (!$id = array_get($arguments, 'id')) {
            # Add an error
            $this->getErrors()->add('errors', 'No Program ID provided');

            return false;
        }

        # Resolve Program from DB
        return $this->program->with($this->relations)->findOrFail($id);
    }

    /**
     * Process the Program into File
     *
     * @param  Betta\Models\Program $program
     * @return Array
     */
    protected function process(Program $program)
    {
        # make new template
        $template = new PlainTextTemplate( $this->template );

        # return new merged template as a steam
        $file = $template->merge($this->getMergeData($program))
                         ->save($this->getSavePath($program));

        return $file;
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
        return $this->sanitizeFileName("{$program->label} ID {$program->id}.ics");
    }

    /**
     * Merge the data
     *
     * @param  Program $program
     * @return Array
     */
    protected function getMergeData(Program $program)
    {
        # Init the Data array
        $data = array();

        # tune the definitions aagainst the model
        foreach ($this->getDefinitions() as $mergeKey => $definition) {
            array_set($data, $mergeKey, object_get($program, $definition));
        }

        # Add supplemental data
        $data = $this->injectAdditionalData($data, $program);

        # Retrun
        return $data;
    }

    /**
     * Map the definitions to properties
     *
     * @return Array
     */
    protected function getDefinitions()
    {
        return [];
    }

    /**
     * Inject additional Data into the MergeData array
     *
     * @param  array  $mergeData
     * @param  Program $program
     * @return array
     */
    protected function injectAdditionalData($mergeData, Program $program)
    {
        return array_merge($mergeData, compact('program'));
    }
}
