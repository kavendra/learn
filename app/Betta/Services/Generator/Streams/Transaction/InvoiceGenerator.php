<?php

namespace Betta\Services\Generator\Streams\Conference;

use Betta\Models\Conference;
use Betta\Models\ConferenceInvoice;
use Betta\Models\ConferenceInvoiceHistory;
use Betta\Models\CostItem;
use Illuminate\Support\MessageBag;
use Betta\Services\Generator\Drivers\WordTemplate;

class InvoiceGenerator
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
    protected $template = 'app/templates/conference/conferenceinvoice.docx';


    /**
     * Location to store the file
     *
     * @var string
     */
    protected $storagePath = 'app/export/conference_invoice';



     protected $itemCostsList = [
        CostItem::CONVENIENCE_FEE,
        CostItem::CHANGE_FEE,
        CostItem::CHECK_PROCESSING,
        CostItem::EXPEDITING_FEE,
        CostItem::EXPEDITING_FEE_CONFERENCES,
        CostItem::CANCELLATION_FEE_CONFERENCES,
        CostItem::LATE_CANCELLATION_CONFERENCES,
        CostItem::CHECK_PROCESSING_CONFERENCES,
    ];


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

        //ConferenceInvoiceHistory::
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
                         ->convertToPdf(true, storage_path($this->storagePath));

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
        //dd(storage_path( "{$this->storagePath}/". $this->getFileName($conference) ));
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
        $date = date('m-d-Y').'@'.time();
        //$date = time();
        return "Conference {$conference->id} Invoice ".$date.".docx";
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
                
                $conference_notes .= $notess->created_at->format('M d, Y') or ''." \t".$notess->createdBy->preferred_name or ''.": ".$notess->content or ''."\n";
            }
        }


        $previous_invoice = $conference->ConferenceInvoice
                                ->where('invoice_id', '<>', $conference->lastInvoiceNumber->invoice_id)
                                ->sum('cost');


        $conference_dates = '';
        if($conference->exibitor_start_date){
            $conference_dates = $conference->exibitor_start_date ? $conference->exibitor_start_date->format('F j, Y') : '';
        }

        if($conference->exibitor_end_date){
            if($conference_dates){
                $conference_dates .= $conference->exibitor_end_date ? ' - '.$conference->exibitor_end_date->format('F j, Y') : '';
            }

        }




        $conference_brands_array = $conference->brands->pluck('label')->toArray();
        $conference_managers = '';
        if(in_array('KRYSTEXXA', $conference_brands_array)){
            $conference_managers = 'Laura Jerzyk and Hays Watkins';
        }elseif(in_array('RAYOS', $conference_brands_array)){
            $conference_managers = 'Laura Jerzyk and Hays Watkins';
        }elseif(in_array('DUXIS', $conference_brands_array)){
            $conference_managers = 'Grant Anderson';
        }elseif(in_array('VIMOVO', $conference_brands_array)){
            $conference_managers = 'Grant Anderson';
        }elseif(in_array('PENNSAID 2%', $conference_brands_array)){
            $conference_managers = 'Grant Anderson';
        }elseif(in_array('ACTIMMUNE', $conference_brands_array)){
            $conference_managers = 'Denelle Robinson';
        }elseif(in_array('RAVICTI', $conference_brands_array)){
            $conference_managers = 'Justin Hays';
        }elseif(in_array('PROCYSBI', $conference_brands_array)){
            $conference_managers = 'James Inglis';
        }



        $data = array(
          'INV_DATE' => date('m/d/Y', strtotime($conference->lastInvoiceNumber->generate_date)),
          //'INV_DATE' => date('m/d/Y', strtotime('2017-04-15')),
          'INV_NUM' => $conference->id.'-'.$conference->lastInvoiceNumber->invoice_id,
          'JOB_NO' => '',
          'CONFERENCE_GL' => '6203500',
          'COST_CENTER' => '200140100',
          'CURRENT_DATE' => date('F j, Y'),
          'SUPPORT_PHONE' => config('fls.support_phone'),
          'EXHIBITOR_START_DATE'        => $conference->exibitor_start_date ? $conference->exibitor_start_date->format('F j, Y') : '',
          'EXHIBITOR_END_DATE'          => $conference->exibitor_end_date ? $conference->exibitor_end_date->format('F j, Y') : '',
          'BRANDS_LIST'                 => $conference->brands->implode('label', ', '),
          'PRIMARY_AUDIENCE_DESCRIPTION'=> $conference->audienceTypes->implode('label', ', '),
          'START_DATE'                  => $conference->start_date ? $conference->start_date->format('F j, Y') : '',
          'END_DATE'                    => $conference->end_date ? $conference->end_date->format('F j, Y') : '',
          'SETUP_DATE'                  => $conference->set_up_date ? $conference->set_up_date->format('m/d/Y') : '',
          'DISMANTLE_DATE'              => $conference->dismantle_date ? $conference->dismantle_date->format('m/d/Y') : '',
          'CONFERENCE_LOCATION'         => $conference->ConferenceAddress ? $conference->ConferenceAddress->line_1 : '',
          'CONFERENCE_CITY_STATE'       => $conference->ConferenceAddress ? $conference->ConferenceAddress->city.', '.$conference->ConferenceAddress->state_province : '',
          'CONFERENCE_NAME'             => $conference->associated_conference,
          'CONFERENCE_DATES' => $conference_dates,

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
          'TOTAL_INVOICE_LABEL' => 'Total Invoice',
          'TOTAL_INVOICE'     => number_format($conference->costs->sum('calculated'), 2),
          'PREVIOUS_INVOICE_LABEL' => 'Previous Invoice',
          'PREVIOUS_INVOICE'     => $previous_invoice,
          'CURRENT_INVOICE_LABEL' => 'Current Invoice',
          'CURRENT_INVOICE'     => number_format(($conference->costs->sum('calculated') - $previous_invoice), 2),
          'CONFERENCE_MANAGERS' => $conference_managers,
        );

       $mergeData = array_merge($mergeData, $data);


        $itemcosts = $conference->lastInvoiceNumber
                                ->ConferenceInvoiceHistory()
                                ->whereNotIn('cost_item_id', $this->itemCostsList)
                                ->get()
                                ->map( function($costs){
                                    $costs->calculated = $costs->cost;
                                    $costs->label_cost = $costs->conferenceCostItem->label or '';
                                    return $costs->makeHidden('conferenceCostItem')->toArray();
                                })->toArray();


        $itemcostsfee = $conference->lastInvoiceNumber
                                ->ConferenceInvoiceHistory()
                                ->whereIn('cost_item_id', $this->itemCostsList)
                                ->get()
                                ->map( function($costs){
                                    $costs->calculated = $costs->cost;
                                    $costs->label_cost = $costs->conferenceCostItem->label or '';
                                    $costs->makeHidden('conferenceCostItem');
                                    return $costs->toArray();
                                })->toArray();





        //dd($contacts);
        return array_merge($mergeData, compact('itemcosts', 'itemcostsfee'));


    }

}
