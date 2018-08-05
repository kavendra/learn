<?php

namespace Betta\Services\Generator\Streams\Conference;

use Maatwebsite\Excel\Excel;
use Betta\Models\CostCenter;
use Betta\Models\InvoiceHistory;
use Betta\Models\Brand;
use Betta\Models\Conference;
use Betta\Models\ConferenceStatus;
use Betta\Services\Generator\Foundation\AbstractReport;
use Carbon\Carbon;
use Auth;

class InvoiceReport extends AbstractReport
{
    /**
     * Bind the implementation
     *
     * @var Model
     */
    protected $conference;
    protected $costcenter;

    protected $conferencemanagementregistrationfee;
    protected $conferencemanagementmaterials;
    protected $conferencemanagementattendeelists;
    protected $conferencefee;
    protected $exhibitorfees;
    protected $candyfees;
    protected $additionalsponsorshipfees;




    /**
     * Title of the Report
     *
     * @var string
     */
    protected $title = 'Conference Invoice Report';


    /**
     * Value to populate in the Report
     *
     * @var string
     */
    protected $description = 'List all Conference Invoice information';


    /**
     * Include SQL tab
     *
     * @var boolean
     */
    protected $includeSql = false;


    /**
     * Always fetch these relations for the Resource
     *
     * @var array
     */
    protected $relations = [
        'ConferenceSTatus',
        'createdBy',
        'addresses',
        'costs',
        'budgetJars',
    ];


    protected $excludeStatuses = [
        ConferenceSTatus::DRAFT
    ];


    /**
     * Create new Instance of Conference List Report

     * @param  Conference Conference $conference
     * @return Void
     */
    public function __construct(Excel $excel, Conference $conference, CostCenter $costcenter, InvoiceHistory $invoicehistory)
    {
        $this->excel = $excel;
        $this->conference = $conference;
        $this->costcenter = $costcenter;
        $this->invoicehistory = $invoicehistory;
    }


    protected function process()
    {
        return $this->excel->create($this->getReportName(), function($excel){

                    # Set standard properties on the file
                    $this->setProperties($excel);



                    # Produce the tab
                    # @todo exctract tabs into their own classes
                    $excel->sheet('Summary', function ($sheet) {
                        $sheet->loadView('reports.conference.invoice.report')
                              ->with('conferences',  $this->candidates )
                              ->with('costcenter',  $this->getCostCenter() )
                              ->with('invoicehistory',  $this->getInvoicehistory() )
                              ->with('conferencemanagementregistrationfee',  $this->getConferenceManagementRegistration() )
                              ->with('conferencemanagementmaterials',  $this->getConferenceManagementMaterials() )
                              ->with('conferencemanagementattendeelists',  $this->getConferenceManagementAttendeeLists() )
                              ->with('conferencefee',  $this->getConferenceFee() )
                              ->with('exhibitorfees',  $this->candidates->sum('exhibitor_fee') )
                              ->with('additionalsponsorshipfees',   $this->getAdditionalSponsorshipFees() )
                              ->with('candyfees',   $this->getCandyFees() )
                              ->with('totalsummarycost',  $this->getTotalSummaryCost() )
                              ->with('brandname',  $this->getSubmitbrand() )
                              ->setColumnFormat( $this->getSummaryFormats() );
                    });

                    $excel->sheet('Reconciled', function ($sheet) {
                         $sheet->loadView('reports.conference.invoice.reconciled')
                              ->with('conferences',  $this->getReconciled() )
                              ->setAutoFilter()
                              ->freezeFirstRow()
                              ->setColumnFormat( $this->getFormats() );
                    });
                    $excel->sheet('Unreconciled', function ($sheet) {
                         $sheet->loadView('reports.conference.invoice.unreconciled')
                              ->with('conferences',  $this->getUnreconciled() )
                              ->setAutoFilter()
                              ->freezeFirstRow()
                              ->setColumnFormat( $this->getFormats() );
                    });

                    # Set the includeSql = true to have SQL Printout tab
                    # includeSql should NEVER be true for production reports
                    $this->includeSqlTab($excel);

                    # Make the first sheet active
                    $excel->setActiveSheetIndex(0);
					$excel->setActiveSheetIndex(0);

                })->store('xlsx', $this->getReportPath(), true);
    }


    /**
     * Return merge data for the report
     *
     * @param  array $arguments
     * @return Array
     */
    protected function loadMergeData($arguments)
    {


        $inBrand = array_get($arguments, 'inBrand' );

        $inBrand = count($inBrand) > 1 ? $inBrand->first()->id : $inBrand;
        $filterFrom = array_get($arguments , 'from', $this->getDefaultFrom() );
        $filterTo = array_get($arguments , 'to', $this->getDefaultTo() );

        return $this->conference
                    ->byBrand( $inBrand )
                    ->betweenDates( $filterFrom, $filterTo )
                    ->with($this->relations)
                    ->notInStatus($this->excludeStatuses)
                    ->latest()
                    ->get();
    }


    protected function getReconciled()
    {
        return $this->candidates->filter(function($item) {
            return $item->is_reconciled == 1;
        });
    }



    protected function getUnreconciled()
    {
        return $this->candidates->filter(function($item) {
                 return ($item->is_reconciled == 0);
        });
    }


    /**
     * [getConferenceManagementRegistration description]
     * @return [type] [description]
     */
    protected function getConferenceManagementRegistration()
    {
        return $this->conferencemanagementregistrationfee = $this->candidates->sum(function($item) {
             if($item->is_reconciled){
                $management_fee =  $item->management_registration_costs ? $item->management_registration_costs->sum('real') : 0;
             }else{
                $management_fee =  $item->management_registration_costs ? $item->management_registration_costs->sum('calculated') : 0;
             }
            return $management_fee;
        });
    }

     /**
     * [getConferenceManagementRegistration description]
     * @return [type] [description]
     */
    protected function getConferenceManagementMaterials()
    {
        return $this->conferencemanagementmaterials = $this->candidates->sum(function($item) {
             if($item->is_reconciled){
                $management_fee =  $item->management_materials_costs ? $item->management_materials_costs->sum('real') : 0;
             }else{
                $management_fee =  $item->management_materials_costs ? $item->management_materials_costs->sum('calculated') : 0;
             }
            return $management_fee;
        });
    }

    /**
     * [getConferenceManagementRegistration description]
     * @return [type] [description]
     */
    protected function getConferenceManagementAttendeeLists()
    {
        return $this->conferencemanagementattendeelists = $this->candidates->sum(function($item) {
             if($item->is_reconciled){
                $management_fee =  $item->management_attendee_costs ? $item->management_attendee_costs->sum('real') : 0;
             }else{
                $management_fee =  $item->management_attendee_costs ? $item->management_attendee_costs->sum('calculated') : 0;
             }
            return $management_fee;
        });
    }


     /**
     * [getConferenceManagementRegistration description]
     * @return [type] [description]
     */
    protected function getAdditionalSponsorshipFees()
    {
        return $this->additionalsponsorshipfees = $this->candidates->sum(function($item) {
             if($item->is_reconciled){
                $management_fee =  $item->sponsorship_fee_additional ? $item->sponsorship_fee_additional->sum('real') : 0;
             }else{
                $management_fee =  $item->sponsorship_fee_additional ? $item->sponsorship_fee_additional->sum('calculated') : 0;
             }
            return $management_fee;
        });
    }

    /**
     * [getConferenceManagementRegistration description]
     * @return [type] [description]
     */
    protected function getCandyFees()
    {
        return $this->candyfees = $this->candidates->sum(function($item) {
             if($item->is_reconciled){
                $management_fee =  $item->other_costs ? $item->other_costs->sum('real') : 0;
             }else{
                $management_fee =  $item->other_costs ? $item->other_costs->sum('calculated') : 0;
             }
            return $management_fee;
        });
    }


    /**
     * [getConferenceManagementRegistration description]
     * @return [type] [description]
     */
    protected function getConferenceFee()
    {
        return  $this->conferencefee = $this->candidates->sum(function($item) {
             if($item->is_reconciled){
                $check_processing_costs =  $item->check_processing_costs ? $item->check_processing_costs->sum('real') : 0;
                $expediting_fee =  $item->expediting_fee ? $item->expediting_fee->sum('real') : 0;
                $convenience_fee =  $item->convenience_fee ? $item->convenience_fee->sum('real') : 0;
                $change_fee_costs =  $item->change_fee_costs ? $item->change_fee_costs->sum('real') : 0;
                $cancellation_fee_costs =  $item->cancellation_fee_costs ? $item->cancellation_fee_costs->sum('real') : 0;
                $late_cancellation_costs =  $item->late_cancellation_costs ? $item->late_cancellation_costs->sum('real') : 0;
             }else{
                $check_processing_costs =  $item->check_processing_costs ? $item->check_processing_costs->sum('calculated') : 0;
                $expediting_fee =  $item->expediting_fee ? $item->expediting_fee->sum('calculated') : 0;
                $convenience_fee =  $item->convenience_fee ? $item->convenience_fee->sum('calculated') : 0;
                $change_fee_costs =  $item->change_fee_costs ? $item->change_fee_costs->sum('calculated') : 0;
                $cancellation_fee_costs =  $item->cancellation_fee_costs ? $item->cancellation_fee_costs->sum('calculated') : 0;
                $late_cancellation_costs =  $item->late_cancellation_costs ? $item->late_cancellation_costs->sum('calculated') : 0;

             }
            return $management_fee = $check_processing_costs+$expediting_fee+$convenience_fee+$change_fee_costs+$cancellation_fee_costs+$late_cancellation_costs;
        });
    }

    protected function getTotalSummaryCost()
    {
        return      $this->conferencemanagementregistrationfee
                +   $this->conferencemanagementmaterials
                +   $this->conferencemanagementattendeelists
                +   $this->conferencefee
                +   $this->candyfees
                +   $this->candidates->sum('exhibitor_fee')
                +   $this->additionalsponsorshipfees;

    }



    protected function getCostCenter()
    {
        $inBrand = array_get($this->arguments, 'inBrand' );
        $inBrand = count($inBrand) > 1 ? $inBrand->first()->id : $inBrand;

        $From = array_get($this->arguments , 'from', $this->getDefaultFromYear() );

        return $this->costcenter->Bybrand($inBrand)->year($From)->get();
    }


    protected function getInvoicehistory()
    {
        $inBrand = array_get($this->arguments, 'inBrand' );
        $inBrand = count($inBrand) > 1 ? $inBrand->first()->id : $inBrand;
        return $this->invoicehistory->Bybrand($inBrand)->Where('invoice_type', 'Conference')->OrderLatestFirst()->get();
    }


    protected function getDefaultFrom()
    {
        return Carbon::parse('January 1')->format('Y-m-d');
    }


    /**
     * Default To Date
     *
     * @return string
     */
    protected function getDefaultTo()
    {
        return Carbon::parse('December 31')->format('Y-m-d');
    }


    protected function getDefaultFromYear()
    {
        return Carbon::parse('January 1')->format('Y');
    }


    /**
     * Column Formats
     *
     * @see Betta\Services\Generator\Interfaces\ExcelFormatsInterface
     * @return array
     */
    public function getFormats()
    {
        return [
            'L:AB' => static::AS_CURRENCY,
            'C:D' => self::AS_DATE,
        ];
    }

    public function getSummaryFormats()
    {
        return [
            'C27:C50' => static::AS_CURRENCY,
            'D9:D23' => static::AS_CURRENCY,

        ];
    }


    protected function getSubmitbrand()
    {
        $inBrand = array_get($this->arguments, 'inBrand' );
        $inBrand = count($inBrand) > 1 ? $inBrand->first()->id : $inBrand;

        $Brand = Brand::find($inBrand);
        return $Brand->label;
    }
}
