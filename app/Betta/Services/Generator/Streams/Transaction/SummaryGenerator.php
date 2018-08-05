<?php

namespace Betta\Services\Generator\Streams\Conference;

use Betta\Models\Conference;
use Illuminate\Support\MessageBag;
use Betta\Services\Generator\Drivers\WordTemplate;

class SummaryGenerator
{
    /**
     * Bind the Implementation
     *
     * @var Betta\Models\Conference
     */
    protected $conference;


    /**
     * Keep the Errors in a MessageBag
     *
     * @var Illuminate\SUpport\MessageBag
     */
    protected $errors;


    /**
     * List the relations to load with conference
     *
     * @var array
     */
    protected $relations = ['brands'];


    /**
     * Locate the Tempalte to User
     *
     * @var string
     */
    protected $template = 'app/templates/conference/summary.docx';


    /**
     * Location to store the file
     *
     * @var string
     */
    protected $storagePath = 'app/export';


    /**
     * Create New Instance of the class
     *
     * @param Conference $conference
     */
    public function __construct(Conference $conference)
    {
        $this->conference = $conference;
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
        if ($conference = $this->getConference($arguments)) {
            return $this->process($conference);
        }

        # return errors
        return $this->getErrors();
    }


    /**
     * Return conference from DB
     *
     * @param  array $arguments
     * @return Program | Exception: ModelNotFound
     */
    protected function getConference($arguments = [])
    {
        if (!$id = array_get($arguments, 'id')) {
            # Add an error
            $this->errors->push('No Conference ID provided');

            return false;
        }

        # Resolve Conference from DB
        return $this->conference->with($this->relations)->findOrFail($id);
    }


    /**
     * Process the Conference into File
     *
     * @param  Betta\Models\Conference $conference
     * @return Array
     */
    protected function process(Conference $conference)
    {
        # make new template
        $template = new WordTemplate( storage_path($this->template) );

        # return new merged template as a steam
        $file = $template->merge($this->getMergeData($conference))
                         ->save($this->getSavePath($conference))
                         ->convertToPdf();

        return $file;
    }


    /**
     * The path where to save the file to
     *
     * @param  Conference $conference
     * @return [type]
     */
    protected function getSavePath(Conference $conference)
    {
        //dd($this->storagePath);
        return storage_path( "{$this->storagePath}/". $this->getFileName($conference) );
    }


    /**
     * Compile a Name for the Resulting document
     *
     * @param  Conference $conference
     * @return string
     */
    protected function getFileName(Conference $conference)
    {
        return "Conference ID {$conference->id} Summary.docx";
    }


    /**
     * Merge the data
     *
     * @param  Conference $conference
     * @return Array
     */
    protected function getMergeData(Conference $conference)
    {
        # Init the Data array
        $data = array();

        # tune the definitions aagainst the model
        foreach ($this->getDefinitions() as $mergeKey => $definition) {
            array_set($data, $mergeKey, object_get($conference, $definition));
        }

        # Add supplemental data
        $data = $this->injectAdditionalData($data, $conference);

        //dd($data);
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
            'CONFERENCE_ID'            => 'id',
            'LABEL'                    => 'label',
            'TARGET_AUDIENCE_SIZE'     => 'expected_attendee_count',
        ];
    }


    /**
     * Inject additional Data into the MergeData array
     *
     * @param  array  $mergeData
     * @param  Conference $conference
     * @return array
     */
    protected function injectAdditionalData($mergeData, Conference $conference)
    {
        $conference_travel = '';
        if($conference->travel == 1){
            $conference_travel = 'I will book my own travel';
        }elseif($conference->travel == 2){
            $conference_travel = 'No travel needed';
        }



        $conference_pms = '';
        if($conference->pms->count() > 0){
            foreach($conference->pms as $user){
                $conference_pms .= $user->PreferredName."\n";
            }
        }


        $conference_materilas = '';
        if($conference->promotional_materials == 1){
            $conference_materilas = 'I will not need literature for this program and I will use my own personal inventory';
        }elseif($conference->promotional_materials == 2){
            $conference_materilas = 'Promotional Literature';
        }

        $conference_literatures = '';
        if($conference->literatures){
            foreach($conference->literatures as $literature){
                $conference_literatures .= $literature->literature_label." \t".$literature->lit_number."\n";
            }
        }


        $conference_agenda_documents = '';
        foreach($conference->agenda_documents as $agenda){
            $conference_agenda_documents .= ucwords($agenda->pivot->reference_name)." \t".$agenda->original_name."\n";
        }

        $conference_prospectus_documents = '';
        foreach($conference->prospectus_documents as $prospectus){
            $conference_prospectus_documents .= ucwords($prospectus->pivot->reference_name)." \t".$prospectus->original_name."\n";
        }

        $conference_supplemental_documents = '';
        foreach($conference->supplemental_documents as $supplemental){
            $conference_supplemental_documents .= ucwords($supplemental->pivot->reference_name)." \t".$supplemental->original_name."\n";
        }

        $conference_notes = '';
        if($conference->notes){
            foreach($conference->notes as $notess){
                $conference_notes .= $notess->created_at->format('M d, Y')." \t".$notess->createdBy->preferred_name.": ".$notess->content."\n";
            }
        }



        $data = array(
          'CURRENT_DATE' => date('F j, Y'),
          'SUPPORT_PHONE' => config('fls.support_phone'),
          'EXHIBITOR_START_DATE'        => $conference->exibitor_start_date ? $conference->exibitor_start_date->format('m/d/Y') : '',
          'EXHIBITOR_END_DATE'          => $conference->exibitor_end_date ? $conference->exibitor_end_date->format('m/d/Y') : '',
          'BRANDS_LIST'                 => $conference->brands->implode('label', ', '),
          'PRIMARY_AUDIENCE_DESCRIPTION'=> $conference->audienceTypes->implode('label', ', '),
          'START_DATE'                  => $conference->start_date ? $conference->start_date->format('m/d/Y') : '',
          'END_DATE'                    => $conference->end_date ? $conference->end_date->format('m/d/Y') : '',
          'SETUP_DATE'                  => $conference->set_up_date ? $conference->set_up_date->format('m/d/Y') : '',
          'DISMANTLE_DATE'              => $conference->dismantle_date ? $conference->dismantle_date->format('m/d/Y') : '',
          'CONFERENCE_STATUS'           => $conference->conferenceStatus->label,
          'CONFERENCE_CREATED_NAME'     => $conference->createdBy->PreferredName,
          'CONFERENCE_TRAVEL_NAME'      => $conference_travel,
          'CONFERENCE_TIER_LEVEL'       => $conference->tier_level,
          'CONFERENCE_ASSOCIATION_NAME' => $conference->association_name,
          'CONFERENCE_ACRONYM'          => $conference->acronym,
          'CONFERENCE_WEBSITE'          => $conference->website,
          'CONFERENCE_SPONSORSHIP_LEVEL'  => $conference->sponsorship_level,
          'CONFERENCE_EXHIBITOR_FEE'  => $conference->exhibitor_fee,
          'CONFERENCE_BOOTH_MAXIMUN_REPRESENTATIVE'  => $conference->booth_maximun_representative,
          'CONFERENCE_COST_PER_BADGE'  => $conference->cost_per_badge,
          'CONFERENCE_BOOTH_NUMBER'     => $conference->booth_number,
          'CONFERENCE_BOOTHSIZE_LEVEL'     => object_get($conference->boothSize, 'label', ''),
          'CONFERENCE_PARKING_INFORMATION'     => $conference->parking_information,
          'CONFERENCE_RECEPTION_DETAIL'     => $conference->reception_detail,
          'CONFERENCE_SPONSORSHIP_DESCRIPTION'     => $conference->sponsorship_description,

          'CONFERENCE_OFFICEADDRESS_LOCATION_NAME'  => object_get($conference->ConferenceAddress, 'location_name', ''),
          'CONFERENCE_OFFICEADDRESS_LINE_1'     => object_get($conference->ConferenceAddress, 'line_1', ''),
          'CONFERENCE_OFFICEADDRESS_LINE_2'     => object_get($conference->ConferenceAddress, 'line_2', ''),
          'CONFERENCE_OFFICEADDRESS_CITY'     => object_get($conference->ConferenceAddress, 'city', ''),
          'CONFERENCE_OFFICEADDRESS_STATE_PROVINCE'  => object_get($conference->ConferenceAddress, 'state_province', ''),
          'CONFERENCE_OFFICEADDRESS_POSTAL_CODE'     => object_get($conference->ConferenceAddress, 'postal_code', ''),

          'CONFERENCE_SHIPPINGADDRESS_LOCATION_NAME' => object_get($conference->ShippingAddress, 'location_name', ''),
          'CONFERENCE_SHIPPINGADDRESS_LINE_1'  => object_get($conference->ShippingAddress, 'line_1', ''),
          'CONFERENCE_SHIPPINGADDRESS_LINE_2'  => object_get($conference->ShippingAddress, 'line_2', ''),
          'CONFERENCE_SHIPPINGADDRESS_CITY' => object_get($conference->ShippingAddress, 'city', ''),
          'CONFERENCE_SHIPPINGADDRESS_STATE_PROVINCE' => object_get($conference->ShippingAddress, 'state_province', ''),
          'CONFERENCE_SHIPPINGADDRESS_POSTAL_CODE'   =>  object_get($conference->ShippingAddress, 'postal_code', ''),

          'CONFERENCE_PAYMENTADDRESS_LOCATION_NAME'  => object_get($conference->PaymentAddress, 'location_name', ''),
          'CONFERENCE_PAYMENTADDRESS_LINE_1' => object_get($conference->PaymentAddress, 'line_1', ''),
          'CONFERENCE_PAYMENTADDRESS_LINE_2'  => object_get($conference->PaymentAddress, 'line_2', ''),
          'CONFERENCE_PAYMENTADDRESS_CITY'    => object_get($conference->PaymentAddress, 'city', ''),
          'CONFERENCE_PAYMENTADDRESS_STATE_PROVINCE' => object_get($conference->PaymentAddress, 'state_province', ''),
          'CONFERENCE_PAYMENTADDRESS_POSTAL_CODE'   => object_get($conference->PaymentAddress, 'postal_code', ''),


          'CONFERENCE_BOOTH_MAXIMUN_REPRESENTATIVE'     => $conference->booth_maximun_representative,
          'CONFERENCE_PROMOTIONAL_MATERIALS'     => $conference_materilas,
          'CONFERENCE_LITERATURES_LIST'     => $conference_literatures,
          'CONFERENCE_IS_CANDY'     => ($conference->is_candy == 1) ? 'Yes' : 'No',

          'CONFERENCE_PMS_PREFERREDNAME_LIST'     => $conference_pms,

          'CONFERENCE_AGENDA_DOCUMENTS_LIST'     => $conference_agenda_documents,
          'CONFERENCE_PROSPECTUS_DOCUMENTS_LIST'     => $conference_prospectus_documents,
          'CONFERENCE_SUPPLEMENTAL_DOCUMENTS_LIST'     => $conference_supplemental_documents,
          'CONFERENCE_NOTES_LIST'     => $conference_notes,
          'P_AMOUNT_LAB' => 'Total',
          'T_COST_LABEL' => 'Total',
          'P_AMOUNT'     => number_format($conference->payments->sum('payment_amount'), 2),
          'T_ESTIMATED'     => number_format($conference->costs->sum('estimate'), 2),
          'T_ACTUAL'     => number_format($conference->costs->sum('actual'), 2),
        );

       $mergeData = array_merge($mergeData, $data);




       $badges = $conference->reps
                                 ->makeHidden(['pivot', 'preferred_address','addresses','primaryAddress'])
                                 ->map( function($reps){
                                        //dd($reps->toArray());
                                        return $reps->toArray();
                                    })
                                 ->toArray();

        

        
       
        //dd($contacts);


        return array_merge($mergeData);


    }

}
