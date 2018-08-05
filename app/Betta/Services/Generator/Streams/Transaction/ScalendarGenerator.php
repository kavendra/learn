<?php

namespace Betta\Services\Generator\Streams\Conference;

use Betta\Models\ConferenceSchedule;
use Betta\Services\Generator\Drivers\PlainTextTemplate;
use Betta\Services\Generator\Foundation\SanitizesStrings;
use Betta\Services\Generator\Foundation\AbstractGenerator;

class ScalendarGenerator extends AbstractGenerator
{
    use SanitizesStrings;

    /**
     * Bind the Implementation
     *
     * @var Betta\Models\Program
     */
    protected $conferenceschedule;

   
    /**
     * Locate the Tempalte to User
     *
     * @var string
     */
    protected $template = 'calendar.scalendar.show';

    /**
     * Location to store the file
     *
     * @var string
     */
    protected $storagePath = 'app/temp';

    /**
     * Create New Instance of the class
     *
     * @param ConferenceSchedule $conferenceschedule
     */
    public function __construct(ConferenceSchedule $conferenceschedule)
    {
        $this->conferenceschedule = $conferenceschedule;
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
        if ($conferenceschedule = $this->getConferenceSchedule($arguments)) {
            return $this->process($conferenceschedule);
        }

        # return errors
        return $this->getErrors();
    }

    /**
     * Return conferenceschedule from DB
     *
     * @param  array $arguments
     * @return ConferenceSchedule | Exception: ModelNotFound
     */
    protected function getConferenceSchedule($arguments = [])
    {
        if ($conferenceschedule = array_get($arguments, 'schedule') AND $conferenceschedule instanceOf ConferenceSchedule) {
            return $conferenceschedule;
        }

        if (!$id = array_get($arguments, 'id')) {
            # Add an error
            $this->getErrors()->add('errors', 'No Program ID provided');

            return false;
        }

        # Resolve Program from DB
        return $this->conferenceschedule->findOrFail($id);
    }

    /**
     * Process the Program into File
     *
     * @param  Betta\Models\Program $conferenceschedule
     * @return Array
     */
    protected function process(ConferenceSchedule $conferenceschedule)
    {
       
        # make new template
        $template = new PlainTextTemplate( $this->template );
        
        # return new merged template as a steam
        $file = $template->merge($this->getMergeData($conferenceschedule))
                         ->save($this->getSavePath($conferenceschedule));

        return $file;
    }

    /**
     * The path where to save the file to
     *
     * @param  Program $conferenceschedule
     * @return [type]
     */
    protected function getSavePath(ConferenceSchedule $conferenceschedule)
    {
        return storage_path( "{$this->storagePath}/". $this->getFileName($conferenceschedule) );
    }

    /**
     * Compile a Name for the Resulting document
     *
     * @param  ConferenceSchedule $conferenceschedule
     * @return string
     */
    protected function getFileName(ConferenceSchedule $conferenceschedule)
    {
        return $this->sanitizeFileName("Reserved Schedule").'.ics';
    }

    /**
     * Merge the data
     *
     * @param  Program $conferenceschedule
     * @return Array
     */
    protected function getMergeData(ConferenceSchedule $conferenceschedule)
    {
        # Init the Data array
        $data = array();

        # tune the definitions aagainst the model
        foreach ($this->getDefinitions() as $mergeKey => $definition) {
            array_set($data, $mergeKey, object_get($program, $definition));
        }

        # Add supplemental data
        $data = $this->injectAdditionalData($data, $conferenceschedule);

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
     * @param  ConferenceSchedule $conferenceschedule
     * @return array
     */
    protected function injectAdditionalData($mergeData, ConferenceSchedule $conferenceschedule)
    {
        return array_merge($mergeData, compact('conferenceschedule'));
    }
}
