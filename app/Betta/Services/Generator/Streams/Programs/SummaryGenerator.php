<?php

namespace Betta\Services\Generator\Streams\Programs;

use Betta\Models\Program;
use Illuminate\Support\MessageBag;
use Betta\Services\Generator\Drivers\WordTemplate;

class SummaryGenerator
{
    /**
     * Bind the Implementation
     *
     * @var Betta\Models\Program
     */
    protected $program;

    /**
     * Keep the Errors in a MessageBag
     *
     * @var Illuminate\SUpport\MessageBag
     */
    protected $errors;

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
    protected $template = 'app/templates/program/summary.docx';

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
        if (!$id = array_get($arguments, 'id')) {
            # Add an error
            $this->errors->push('No Program ID provided');

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
        $template = new WordTemplate( storage_path($this->template) );
        # return new merged template as a steam
        return $template->merge($this->getMergeData($program))
                        ->save($this->getSavePath($program))
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
        return storage_path("{$this->storagePath}/". $this->getFileName($program));
    }

    /**
     * Compile a Name for the Resulting document
     *
     * @param  Program $program
     * @return string
     */
    protected function getFileName(Program $program)
    {
        return "Program ID {$program->id} Summary.docx";
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
            'LABEL' => 'label',
            'PROGRAM_ID' => 'id',
            'FULL_DATE' => 'full_date',
            'FULL_LABEL' => 'full_label',
            'PRIMARY_BRAND' => 'primary_brand.label',
            'PROGRAM_TYPE' => 'programType.label',
            'PRIMARY_PRESENTATION_TITLE' => 'title',
            'PRIMARY_FIELD' => 'primary_field.preferred_name',
            'TERRITORY' => 'primary_field.territory.account_territory_id',
            'FIELD_PHONE' => 'primary_field.primary_phone',
            'PRIMARY_PM' => 'primary_pm.preferred_name',
            'PM_PHONE' => 'primary_pm.primary_phone',
            'PRIMARY_VC' => 'primary_vc.preferred_name',
            'VC_PHONE' => 'primary_vc.primary_phone',
            'LOCATION_NAME' => 'address.name',
            'LOCATION_ADDRESS' => 'address.first_line',
            'LOCATION_CITY_STATE_ZIP' => 'address.city_state_zip',
            'LOCATION_PHONE' => 'primaryLocation.phone',
            'LOCATION_EMAIL' => 'primaryLocation.email',
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
        $data = array(
            'USD' => '$',
            'CURRENT_DATE' => date('F j, Y'),
            'SUPPORT_PHONE' => config('fls.support_phone'),
            # Registration Data
            'AUDIENCE_TYPE' => $program->audienceTypes->implode('abbreviated_label', ', '),
            'RE_HCP'     => $program->attendee_count_hcp,
            'RE_FIELD'   => $program->attendee_count_field,
            'RE_SPEAKER' => 1,
            'RA_HCP'     => $program->hcp_registrations->where('attended', true)->count() ?: '',
            'RA_FIELD'   => $program->field_registrations->where('attended', true)->count() ?: '',
            'RA_SPEAKER' => $program->speaker_registrations->where('attended', true)->count() ?: '',
            'RF_HCP'     => $program->hcp_registrations->where('has_consumed_meal', true)->count() ?: '',
            'RF_FIELD'   => $program->field_registrations->where('has_consumed_meal', true)->count() ?: '',
            'RF_SPEAKER' => $program->speaker_registrations->where('has_consumed_meal', true)->count() ?: '',

            'RE_TOTAL' => $program->total_estimated_attendees,
            'RA_TOTAL' => $program->registrations->where('attended', true)->count() ?: '',
            'RF_TOTAL' => $program->registrations->where('has_consumed_meal', true)->count() ?: '',

            # Speaker
            'SPEAKER'   => data_get($program->primary_speakers->first(), 'preferred_name_degree', ''),
            # Costs
            'E_TOTAL' => $this->format($this->getCosts($program)->sum('estimate')),
            'A_TOTAL' => $this->format($this->getCosts($program)->sum('real')),
            'FB_PP'   => $this->format($program->fb_per_person),
            'COSTS'   => $this->getCostList($program),
        );

        return array_merge($mergeData, $data);
    }

    /**
     * Get Cost List
     *
     * @param  Program $program
     * @return Array
     */
    protected function getCostList(Program $program)
    {
        return $this->getCosts($program)->values()->transform(function($cost){
            return [
                'COST_LABEL' => $cost->label,
                'ESTIMATE' => $this->format($cost->estimate),
                'ACTUAL' => $this->format($cost->real),
            ];
        })->all();
    }

    /**
     * List the visible Costs
     *
     * @param  Program $program
     * @return Array
     */
    protected function getCosts(Program $program)
    {
        return $program->costs->where('costItem.is_honorarium', false);
    }

    /**
     * Format the Number
     *
     * @param  mixed $value
     * @param  boolean $showEmpty
     * @return string
     */
    protected function format($value, $showEmpty = true)
    {
        return number_format($value, 2);
    }
}
