<?php

namespace Betta\Services\Generator\Streams\Programs\Location\FlsCardAuthorization;

use Betta\Models\Program;
use Betta\Models\ProgramLocation;
use Betta\Services\Generator\Drivers\WordTemplate;

class Generator
{
    /**
     * Default Template
     *
     * @var string
     */
    protected $defaultTemplate = 'app/templates/program/credit_card_authorization.docx';

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
        'address',
        'program.fields',
        'program.pms',
        'program.programType',
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
     * @var Betta\Models\ProgramLocation
     */
    protected $location;

    /**
     * Create new instance of the class
     *
     * @param ProgramLocation $location
     */
    public function __construct(ProgramLocation $location)
    {
        $this->location = $location;
    }

    /**
     * Handling should be simple and should return instance of Document
     *
     * @param  array  $arguments
     * @return Instance
     */
    public function handle($arguments = array())
    {
        # Obtain the Program Location
        if($location = $this->getProgramLocation($arguments)){
            return $this->process($location);
        }
        # alternative
        return null;
    }

    /**
     * Fetch Program Location
     *
     * @param  array $arguments
     * @return ProgramLocation
     */
    protected function getProgramLocation($arguments)
    {
        # is the value provided to us?
        if ($location = array_get($arguments, 'location') AND $location instanceOf ProgramLocation) {
            return $location->load($this->relations);
        }

        # If program is provided to us then load primary location
        if ($program = array_get($arguments, 'program') AND $program instanceOf Program AND $location = $program->primaryLocation ) {
            return $location->load($this->relations);
        }

        # is the ID provided to us?
        if ($id = array_get($arguments, 'id')) {
            return $this->location->with($this->relations)->findOrFail($id);
        }

        return null;
    }

    /**
     * Get the Template Path
     * We would rely on the ProgramType to store the template; if not, rely on the default
     *
     * @return String
     */
    protected function getTemplatePath(Program $program)
    {
        return storage_path( $this->defaultTemplate );
    }

    /**
     * Produce the File
     *
     * @param  ProgramLocation $location
     * @return array
     */
    protected function process(ProgramLocation $location)
    {
        # make new template
        $template = new WordTemplate($this->getTemplatePath($location->program));
        # return new merged template as a steam
        # The methods will handle all the exceptions
        return $template->merge($this->getMergeData($location)->toArray())
                        ->save($this->getSavePath($location))
                        ->convertToPdf();
    }

    /**
     * The path where to save the file to
     *
     * @param  ProgramLocation $location
     * @return string
     */
    protected function getSavePath(ProgramLocation $location)
    {
        return storage_path("{$this->storagePath}/". $this->getFileName($location));
    }

    /**
     * Compile a Name for the Resulting document
     *
     * @param  ProgramLocation $location
     * @return string
     */
    protected function getFileName(ProgramLocation $location)
    {
        return vsprintf('%s - Tracy AMEX with CC Auth (002) %s.docx', [md5(microtime()), $location->program->label]);
    }

    /**
     * Return merge data from the Contract
     *
     * @param  ProgramLocation $location
     * @return Array
     */
    protected function getMergeData(ProgramLocation $location)
    {
        return (new MergeData($location))->fill();
    }
}
