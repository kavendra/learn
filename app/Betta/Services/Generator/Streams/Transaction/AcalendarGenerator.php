<?php

namespace Betta\Services\Generator\Streams\Conference;

use Betta\Models\ConferenceToAffiliateMeeting;
use Betta\Services\Generator\Drivers\PlainTextTemplate;
use Betta\Services\Generator\Foundation\SanitizesStrings;
use Betta\Services\Generator\Foundation\AbstractGenerator;

class AcalendarGenerator extends AbstractGenerator
{
    use SanitizesStrings;

    /**
     * Bind the Implementation
     *
     * @var Betta\Models\ConferenceToAffiliateMeeting
     */
    protected $conferenceToAffiliate;


    /**
     * Locate the Tempalte to User
     *
     * @var string
     */
    protected $template = 'calendar.acalendar.show';

    /**
     * Location to store the file
     *
     * @var string
     */
    protected $storagePath = 'app/temp';

    /**
     * Create New Instance of the class
     *
     * @param ConferenceToAffiliateMeeting $conferenceToAffiliate
     */
    public function __construct(ConferenceToAffiliateMeeting $conferenceToAffiliate)
    {
        $this->conferenceToAffiliate = $conferenceToAffiliate;
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
        if ($conferenceToAffiliate = $this->getConferenceToAffiliate($arguments)) {
            return $this->process($conferenceToAffiliate);
        }

        # return errors
        return $this->getErrors();
    }

    /**
     * Return conferenceToAffiliate from DB
     *
     * @param  array $arguments
     * @return ConferenceToAffiliateMeeting | Exception: ModelNotFound
     */
    protected function getConferenceToAffiliate($arguments = [])
    {
        if ($conferenceToAffiliate = array_get($arguments, 'conferenceToAffiliate') AND $conferenceToAffiliate instanceOf ConferenceToAffiliateMeeting) {
            return $conferenceToAffiliate;
        }

        if (!$id = array_get($arguments, 'id')) {
            # Add an error
            $this->getErrors()->add('errors', 'No ConferenceToAffiliateMeeting ID provided');

            return false;
        }

        # Resolve Program from DB
        return $this->conferenceToAffiliate->findOrFail($id);
    }

    /**
     * Process the Program into File
     *
     * @param  Betta\Models\ConferenceToAffiliateMeeting $conferenceToAffiliate
     * @return Array
     */
    protected function process(ConferenceToAffiliateMeeting $conferenceToAffiliate)
    {

        # make new template
        $template = new PlainTextTemplate( $this->template );

        # return new merged template as a steam
        $file = $template->merge($this->getMergeData($conferenceToAffiliate))
                         ->save($this->getSavePath($conferenceToAffiliate));

        return $file;
    }

    /**
     * The path where to save the file to
     *
     * @param  ConferenceToAffiliateMeeting $conferenceToAffiliate
     * @return [type]
     */
    protected function getSavePath(ConferenceToAffiliateMeeting $conferenceToAffiliate)
    {
        return storage_path( "{$this->storagePath}/". $this->getFileName($conferenceToAffiliate) );
    }

    /**
     * Compile a Name for the Resulting document
     *
     * @param  ConferenceToAffiliateMeeting $conferenceToAffiliate
     * @return string
     */
    protected function getFileName(ConferenceToAffiliateMeeting $conferenceToAffiliate)
    {
        return $this->sanitizeFileName("Reserved Affiliate Meeting").'.ics';
    }

    /**
     * Merge the data
     *
     * @param  ConferenceToAffiliateMeeting $conferenceToAffiliate
     * @return Array
     */
    protected function getMergeData(ConferenceToAffiliateMeeting $conferenceToAffiliate)
    {
        # Init the Data array
        $data = array();

        # tune the definitions aagainst the model
        foreach ($this->getDefinitions() as $mergeKey => $definition) {
            array_set($data, $mergeKey, object_get($program, $definition));
        }

        # Add supplemental data
        $data = $this->injectAdditionalData($data, $conferenceToAffiliate);

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
     * @param  ConferenceToAffiliateMeeting $conferenceToAffiliate
     * @return array
     */
    protected function injectAdditionalData($mergeData, ConferenceToAffiliateMeeting $conferenceToAffiliate)
    {
        return array_merge($mergeData, compact('conferenceToAffiliate'));
    }
}
