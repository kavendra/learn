<?php

namespace Betta\Services\Generator\Streams\Programs;

use Betta\Models\Program;
use Betta\Services\Generator\Drivers\PdfTemplate;
use Betta\Services\Generator\Foundation\AbstractGenerator;

class InvitationGenerator extends AbstractGenerator
{
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
    protected $relations = [];

    /**
     * Locate the Tempalte to User
     *
     * @var string
     */
    protected $template;

    /**
     * Transform (or not) the merge keys?
     *
     * @var boolean
     */
    protected $transformKeys = false;

    /**
     * Indicate type of invitation to produce
     *
     * @var string
     */
    protected $invitationType = 'electronic';

    /**
     * Default Handler
     *
     * @var string
     */
    protected $handler = PdfTemplate::class;

    /**
     * Location to store the file
     *
     * @var string
     */
    protected $storagePath = 'app/temp';

    /**
     * FIle name template
     *
     * @var string
     */
    protected $nameTemplate = 'Program ID %ID% Invitation';

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
     * Return Document representing an Invitation
     *
     * @param  Program $program
     * @return Document | null
     */
    protected function getInvitation(Program $program)
    {
        return object_get($program, "{$this->invitationType}_invitation");
    }

    /**
     * Return Invitaiton map record
     *
     * @param  Program $program
     * @return Document | null
     */
    protected function getInvitationMap(Program $program)
    {
        return object_get($program, "last_{$this->invitationType}_invitation");
    }

    /**
     * Return Invitaiton map record
     *
     * @param  Program $program
     * @return string
     */
    protected function getHandler(Program $program)
    {
        return object_get($this->getInvitationMap($program), "driver", $this->handler);
    }

    /**
     * Process the Program into File
     *
     * @param  Betta\Models\Program $program
     * @return Array
     */
    protected function process(Program $program)
    {
        # get template
        $template = $this->getTemplate($program);

        # if Template created convert to PDF
        if($template){
            return $template
                    ->merge($this->getMergeData($program), true, $this->transformKeys)
                    ->save($this->getSavePath($program))
                    ->convertToPdf();
        } else{
            return false;
        }

    }

    /**
     * Produce Template from handling driver
     *
     * @return TemplateInterface
     */
    protected function getTemplate(Program $program)
    {
        $invitation = $this->getInvitation($program);

        # Check if Invitation is present
        if(empty($invitation)){
            $this->getErrors()->add('errors', "No {$this->invitationType} invitation found");

            return false;
        }

        # handler is stored either in Invitation OR is default
        $handler = $this->getHandler($program);

        return app()->make($handler, ['path' => $invitation->uri]);
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
        # get the template
        $template = object_get($this->getInvitationMap($program), 'name_template', $this->nameTemplate);

        foreach($this->getMergeData($program) as $key => $value){
            $template = str_replace("%{$key}%", $value, $template);
        }

        return $template;
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
        return [
            'ID' => 'id',

            'LOCATION_NAME' => 'primaryLocation.name',
            'LOCATION_ADDRESS_LINE'   => 'primaryLocation.address.address_line',
            'LOCATION_CITY_STATE_ZIP' => 'primaryLocation.address.city_state_zip',
            'LOCATION_URL' => 'primaryLocation.address.owner.url',

            'REP_NAME'  => 'primary_field.preferred_name',
            'REP_PHONE' => 'primary_field.repProfile.primary_phone',
            'REP_EMAIL' => 'primary_field.repProfile.primary_email',

            'PROGRAM_TYPE' => 'program_type_label',
        ];
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
        $speaker = $program->primary_speakers->first();

        # get Location Map of the desired size
        $locationMap = object_get($program, 'primaryLocation.address')
                        ? $program->primaryLocation->address->getMapImageUrlSize([640, 350], true)
                        : null;

        $data = array(
            'CURRENT_DATE' => date('F j, Y'),

            'SUPPORT_PHONE' => config('fls.support_phone'),
            'SPEAKER_NAME' => object_get($speaker, 'profile.speakerProfile.preferred_name_degree', ''),
            'SPEAKER_TITLE' => object_get($speaker, 'profile.speakerProfile.preferred_signature', ''),
            'SPEAKER_COMPANY' => object_get($speaker, 'profile.speakerProfile.affiliated_practice', ''),
            'SPEAKER_CITY_STATE' => object_get($speaker, 'profile.preferred_address.city', '').' '.object_get($speaker, 'profile.preferred_address.state_province', ''),
            'LOCATION_MAP' => $locationMap,

            'PROGRAM_FULL_DATE' => $program->full_date,
            'PROGRAM_FULL_START_DATE' => $program->full_start_date,
            'SHORT_DATE' => $program->start_date->format('m-d-Y'),
            'PROGRAM_SHORT_DATE' => $program->start_date->format('F j, Y'),
            'PROGRAM_TIME' => $program->start_date->format( 'g:i A' ),
            'PROGRAM_TIME_SUB_30' => $program->start_date->copy()->subMinutes(30)->format('g:i A'),
            'START_DATE_SUB_5_WEEKDAYS' => $program->start_date->copy()->subWeekdays(5)->format('F j, Y'),
            'PROGRAM_DATE_TIME' => $program->start_date->format('F j, Y g:i A')
        );

        return array_merge($mergeData, $data);
    }
}
