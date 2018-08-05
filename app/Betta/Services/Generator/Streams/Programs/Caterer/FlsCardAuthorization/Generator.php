<?php

namespace Betta\Services\Generator\Streams\Programs\Caterer\FlsCardAuthorization;

use Betta\Models\Program;
use Betta\Models\ProgramCaterer;
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
        'program.pms',
        'program.fields',
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
     * @var Betta\Models\ProgramCaterer
     */
    protected $catere;

    /**
     * Create new instance of the class
     *
     * @param ProgramCaterer $caterer
     */
    public function __construct(ProgramCaterer $caterer)
    {
        $this->caterer = $caterer;
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
        if($caterer = $this->getProgramCaterer($arguments)){
            return $this->process($caterer);
        }
        # alternative
        return null;
    }

    /**
     * Fetch Program Location
     *
     * @param  array $arguments
     * @return ProgramCaterer | null
     */
    protected function getProgramCaterer($arguments)
    {
        # is the value provided to us?
        if($caterer = array_get($arguments, 'caterer') AND $caterer instanceOf ProgramCaterer){
            return $caterer->load($this->relations);
        }
        # If program is provided to us then load primary location
        if($program = array_get($arguments, 'program') AND $program instanceOf Program AND $caterer = $program->primaryCaterer){
            return $caterer->load($this->relations);
        }
        # is the ID provided to us?
        if ($id = array_get($arguments, 'id')) {
            return $this->caterer->with($this->relations)->findOrFail($id);
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
        return storage_path($this->defaultTemplate);
    }

    /**
     * Produce the File
     *
     * @param  ProgramCaterer $caterer
     * @return array
     */
    protected function process(ProgramCaterer $caterer)
    {
        # make new template
        $template = new WordTemplate($this->getTemplatePath($caterer->program));
        # return new merged template as a steam
        # The methods will handle all the exceptions
        return $template->merge($this->getMergeData($caterer)->toArray())
                        ->save($this->getSavePath($caterer))
                        ->convertToPdf();
    }

    /**
     * The path where to save the file to
     *
     * @param  ProgramCaterer $caterer
     * @return string
     */
    protected function getSavePath(ProgramCaterer $caterer)
    {
        return storage_path("{$this->storagePath}/". $this->getFileName($caterer));
    }

    /**
     * Compile a Name for the Resulting document
     *
     * @param  ProgramCaterer $caterer
     * @return string
     */
    protected function getFileName(ProgramCaterer $caterer)
    {
        return vsprintf('%s - Tracy B. AmEx CC Auth (002) %s.docx', [md5(microtime()), $caterer->program->label]);
    }

    /**
     * Return merge data from the Caterer
     *
     * @param  ProgramCaterer $caterer
     * @return Array
     */
    protected function getMergeData(ProgramCaterer $caterer)
    {
        return (new MergeData($caterer))->fill();
    }
}
