<?php

namespace Betta\Services\Generator\Streams\Speaker;

use Betta\Models\ProgramSpeaker;
use Illuminate\Support\MessageBag;
use Betta\Services\Generator\Drivers\WordTemplate;
use Betta\Services\Generator\Foundation\SanitizesStrings;

class ExpenseFormGenerator
{
    use SanitizesStrings;

    /**
     * Bind the Implementation
     *
     * @var Betta\Models\ProgramSpeaker
     */
    protected $speaker;

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
    protected $template = 'app/templates/speaker/expense_form_2016.docx';

    /**
     * Location to store the file
     *
     * @var string
     */
    protected $storagePath = 'app/temp';

    /**
     * Create New Instance of the class
     *
     * @param ProgramSpeaker $speaker
     */
    public function __construct(ProgramSpeaker $speaker)
    {
        $this->speaker = $speaker;
    }

    /**
     * Handle document generation
     *
     * @param  array  $arguments
     * @return array
     */
    public function handle($arguments = [])
    {
        # If we can can get the Speaker from arguments, return the handling result
        if ($speaker = $this->getSpeaker($arguments)) {
            return $this->process($speaker);
        }

        # return errors
        return $this->getErrors();
    }

    /**
     * Return ProgramSpeaker from DB
     *
     * @param  array $arguments
     * @return ProgramSpeaker | Exception: ModelNotFound
     */
    protected function getSpeaker($arguments = [])
    {
        if ($speaker = array_get($arguments, 'speaker') AND $speaker instanceOf ProgramSpeaker) {
            return $speaker->load($this->relations);
        }

        if (!$id = array_get($arguments, 'id')) {
            # Add an error
            $this->errors->push('No Program Speaker provided');

            return false;
        }

        # Resolve Speaker from DB
        return $this->speaker->with($this->relations)->findOrFail($id);
    }

    /**
     * Process the ProgramSpeaker into File
     *
     * @param  Betta\Models\ProgramSpeaker $speaker
     * @return Array
     */
    protected function process(ProgramSpeaker $speaker)
    {
        # make new template
        $template = new WordTemplate( storage_path($this->template) );

        # return new merged template as a steam
        $file = $template->merge($this->getMergeData($speaker))
                         ->save($this->getSavePath($speaker))
                         ->convertToPdf();

        return $file;
    }

    /**
     * The path where to save the file to
     *
     * @param  ProgramSpeaker $speaker
     * @return string
     */
    protected function getSavePath(ProgramSpeaker $speaker)
    {
        return storage_path( "{$this->storagePath}/". $this->getFileName($speaker) );
    }

    /**
     * Compile a Name for the Resulting document
     *
     * @param  ProgramSpeaker $speaker
     * @return string
     */
    protected function getFileName(ProgramSpeaker $speaker)
    {
        # Compile
        $fileName = $this->sanitizeFileName("Program ID {$speaker->program_id} - {$speaker->preferred_name_degree} Expense Form");
        # Sanitize
        return "{$fileName}.docx";
    }

    /**
     * Merge the data
     *
     * @param  ProgramSpeaker $speaker
     * @return Array
     */
    protected function getMergeData(ProgramSpeaker $speaker)
    {
        # Init the Data array
        $data = array();
        # tune the definitions aagainst the model
        foreach ($this->getDefinitions() as $mergeKey => $definition) {
            array_set($data, $mergeKey, object_get($speaker, $definition));
        }
        # Add supplemental data
        $data = $this->injectAdditionalData($data, $speaker);
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
            'SPEAKER_NAME_DEGREE' => 'preferred_name_degree',
            'PROGRAM_ID' => 'program.id',
            'PROGRAM_DATE_FULL' => 'program.full_date',
            'LOCATION_NAME' => 'program.address.name',
            'LOCATION_CITY_STATE' => 'program.address.city_state',
            'PM_NAME' => 'program.primary_pm.preferred_name',
        ];
    }

    /**
     * Inject additional Data into the MergeData array
     *
     * @param  array  $mergeData
     * @param  ProgramSpeaker $speaker
     * @return array
     */
    protected function injectAdditionalData($mergeData, ProgramSpeaker $speaker)
    {
        $data = array(
          'CURRENT_DATE' => date('F j, Y'),
          'SUPPORT_PHONE' => config('fls.support_phone'),
          'SUPPORT_FAX' => config('fls.support_fax'),
          'PROGRAM_DATE_ADD_7DAYS' => $speaker->program->start_date->copy()->addDays(7)->format('F j, Y'),
          'USD' => '$',
        );

        return array_merge($mergeData, $data);
    }
}
