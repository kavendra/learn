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
use Betta\Models\Interfaces\CostItemInterface;
use Auth;

class InvoicePreparationReport extends AbstractReport
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
    protected $sponsorshipfees;
    protected $consultantfees;




    /**
     * Title of the Report
     *
     * @var string
     */
    protected $title = 'Invoice Preparation Report';


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




    protected $costTypes = [
        CostItemInterface::SPONSORSHIP_FEE_CONFERENCES,
        CostItemInterface::ADDITIONAL_SPONSORSHIP_FEE_CONFERENCES,
        CostItemInterface::PROGRAM_MANAGEMENT_CONFERENCES,
        CostItemInterface::CONFERENCE_MATERIALS_FULFILLMENT_CONFERENCES,
        CostItemInterface::BOOTH_AMENITIES_CONFERENCES,
        CostItemInterface::CANDY_SHIPPING,
        CostItemInterface::CONSULTANT_PAYMENT,
        CostItemInterface::FREIGHT_CHARGES_CONFERENCES,
        CostItemInterface::OTHER_CONFERENCES,
        CostItemInterface::CANCELLATION_FEE_CONFERENCES,
        CostItemInterface::CHECK_PROCESSING_CONFERENCES,
        CostItemInterface::CHANGE_FEE,
        CostItemInterface::CONVENIENCE_FEE,
        CostItemInterface::EXPEDITING_FEE_CONFERENCES,

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
                        $sheet->loadView('reports.conference.invoice.invoice-preparation-summary')
                              ->with('conferences',  $this->getReportData() )
                              ->with('costcenter',  $this->getCostCenter() )
                              ->with('invoicehistory',  $this->getInvoicehistory() )
                              ->with('conferencemanagementregistrationfee',  $this->getConferenceManagementRegistration() )
                              ->with('conferencemanagementmaterials',  $this->getConferenceManagementMaterials() )
                              ->with('conferencemanagementattendeelists',  $this->getConferenceManagementAttendeeLists() )
                              ->with('conferencefee',  $this->getConferenceFee() )
                              ->with('exhibitorfees',  $this->getsponsorshipFees() )
                              ->with('additionalsponsorshipfees',   $this->getAdditionalSponsorshipFees() )
                              ->with('candyfees',   $this->getCandyFees() )
                              ->with('consultantfees',   $this->getConsultantFees () )
                              ->with('totalsummarycost',  $this->getTotalSummaryCost() )
                              ->with('brandnames',  $this->getSubmitbrand() )
                              ->setColumnFormat( $this->getSummaryFormats() );
                    });

                    $excel->sheet('Invoice Report', function ($sheet) {
                         $sheet->loadView('reports.conference.invoice.invoice-preparation-report')
                              ->with('conferences',  $this->getReportData() )
                              ->freezeFirstRow()
                              ->setColumnFormat( $this->getFormats() );
                    });
                    $excel->sheet('Non Program Ralated', function ($sheet) {
                         $sheet->loadView('reports.conference.invoice.non-program-related')
                              ->with('conferences',  $this->getReportData() )
                              ->freezeFirstRow()
                              ->setColumnFormat( $this->getFormats() );
                    });

                    # Set the includeSql = true to have SQL Printout tab
                    # includeSql should NEVER be true for production reports
                    //$this->includeSqlTab($excel);

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

        $inBrand = is_array($inBrand) ? $inBrand : $inBrand->pluck('id')->toArray();

        $filterFrom = array_get($arguments , 'from', $this->getDefaultFrom() );
        $filterTo = array_get($arguments , 'to', $this->getDefaultTo() );

        return $this->conference
                    ->has('costs')
                    ->byBrand( $inBrand )
                    ->betweenDates( $filterFrom, $filterTo )
                    ->with($this->relations)
                    ->latest()
                    ->get();


/*      $costs = $this->cost->with('conference')
                            ->where('context_type', 'Like','%Conference%')
                            ->notInvoiced()
                            ->get()
                            ->filter(function($cost){
                                return $cost->calculated>0 && $cost->conference;
                            }); $conference->other_costs->sum('calculated')*/
    }


    protected function getReportData()
    {

        foreach($this->candidates as $candidate)
        {
            $costs = $candidate->costs->filter(function($item) {
                        return (!$item->is_invoice && in_array($item->cost_item_id, $this->costTypes));
                    });
            $candidate->costs = $costs;
        }

        return $this->candidates->filter(function($item) {
                            return ($item->costs->sum('calculated') != 0);
                                });

    }

    /**
     * [getConferenceManagementRegistration description]
     * @return [type] [description]
     */
    protected function getConferenceManagementRegistration()
    {

        return $this->conferencemanagementregistrationfee = $this->getReportData()->sum(function($item) {
             return $item->management_registration_costs ? $item->management_registration_costs->sum('calculated') : 0;
        });
    }

     /**
     * [getConferenceManagementMaterials description]
     * @return [type] [description]
     */
    protected function getConferenceManagementMaterials()
    {
        return $this->conferencemanagementmaterials = $this->getReportData()->sum(function($item) {
             return $management_fee =  $item->management_materials_costs ? $item->management_materials_costs->sum('calculated') : 0;
        });
    }

    /**
     * [getConferenceManagementAttendeeLists description]
     * @return [type] [description]
     */
    protected function getConferenceManagementAttendeeLists()
    {
        return $this->conferencemanagementattendeelists = $this->getReportData()->sum(function($item) {
            return $management_fee =  $item->management_attendee_costs ? $item->management_attendee_costs->sum('calculated') : 0;
        });
    }


     /**
     * [getAdditionalSponsorshipFees description]
     * @return [type] [description]
     */
    protected function getAdditionalSponsorshipFees()
    {
        return $this->additionalsponsorshipfees = $this->getReportData()->sum(function($item) {
             return $management_fee =  $item->sponsorship_fee_additional ? $item->sponsorship_fee_additional->sum('calculated') : 0;
        });
    }

    /**
     * [getCandyFees description]
     * @return [type] [description]
     */
    protected function getCandyFees()
    {
        return $this->candyfees = $this->getReportData()->sum(function($item) {
             return $management_fee =  $item->candy_shipping ? $item->candy_shipping->sum('calculated') : 0;
        });
    }

    /**
     * [getsponsorshipFees description]
     * @return [type] [description]
     */
    protected function getsponsorshipFees()
    {
        return $this->sponsorshipfees = $this->getReportData()->sum(function($item) {
             return $management_fee =  $item->sponsorship_fee ? $item->sponsorship_fee->sum('calculated') : 0;
        });
    }

    /**
     * [getConsultantFees description]
     * @return [type] [description]
     */
    protected function getConsultantFees()
    {
        return $this->consultantfees = $this->getReportData()->sum(function($item) {
             return $management_fee =  $item->consultant_payment ? $item->consultant_payment->sum('calculated') : 0;
        });
    }

    /**
     * [getConferenceManagementRegistration description]
     * @return [type] [description]
     */
    protected function getConferenceFee()
    {
        return  $this->conferencefee = $this->getReportData()->sum(function($item) {

                $cancellation_fee_costs =  $item->cancellation_fee_costs ? $item->cancellation_fee_costs->sum('calculated') : 0;
                $check_processing_costs =  $item->check_processing_costs ? $item->check_processing_costs->sum('calculated') : 0;
                $change_fee_costs =  $item->change_fee_costs ? $item->change_fee_costs->sum('calculated') : 0;
                $convenience_fee =  $item->convenience_fee ? $item->convenience_fee->sum('calculated') : 0;
                $expediting_fee =  $item->expediting_fee ? $item->expediting_fee->sum('calculated') : 0;
                $freight_charges_costs =  $item->freight_charges_costs ? $item->freight_charges_costs->sum('calculated') : 0;
                //$late_cancellation_costs =  $item->late_cancellation_costs ? $item->late_cancellation_costs->sum('calculated') : 0;

            return $management_fee = $cancellation_fee_costs + $check_processing_costs + $change_fee_costs + $convenience_fee + $expediting_fee + $freight_charges_costs;
        });
    }

    protected function getTotalSummaryCost()
    {

        return      $this->conferencemanagementregistrationfee
                +   $this->conferencemanagementmaterials
                +   $this->conferencemanagementattendeelists
                +   $this->conferencefee
                +   $this->candyfees
                +   $this->sponsorshipfees
                +   $this->additionalsponsorshipfees
                +   $this->consultantfees;

    }

    protected function getCostCenter()
    {
        $inBrand = array_get($this->arguments, 'inBrand' );
        $inBrand = is_array($inBrand) ? $inBrand : $inBrand->pluck('id')->toArray();

        $From = array_get($this->arguments , 'from', $this->getDefaultFromYear() );

        return $this->costcenter->Bybrand($inBrand)->year($From)->get();
    }

    protected function getInvoicehistory()
    {
        $inBrand = array_get($this->arguments, 'inBrand' );
        $inBrand = is_array($inBrand) ? $inBrand : $inBrand->pluck('id')->toArray();
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
            'j:AB' => static::AS_CURRENCY,
            'C:D' => self::AS_DATE,
        ];
    }

    public function getSummaryFormats()
    {
        return [
            'C:D' => static::AS_CURRENCY,
            ];
    }

    protected function getSubmitbrand()
    {
        $inBrand = array_get($this->arguments, 'inBrand' );
        $inBrand = is_array($inBrand) ? $inBrand : $inBrand->pluck('id')->toArray();
        $Brands = Brand::whereIn('id', $inBrand);
        return $Brands->pluck('label')->toArray();
    }
}
