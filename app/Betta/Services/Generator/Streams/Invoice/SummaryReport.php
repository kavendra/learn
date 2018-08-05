<?php

namespace Betta\Services\Generator\Streams\Invoice;

use Carbon\Carbon;
use Betta\Models\Brand;
use Betta\Models\Program;
use Maatwebsite\Excel\Excel;
use Betta\Models\CostCenter;
use Betta\Models\ProgramType;
use Betta\Models\InvoiceHistory;
use Betta\Models\ProgramStatus;
use Betta\Models\InvoiceMemo;
use Betta\Models\Contract;
use Betta\Models\Nomination;
use Betta\Models\NprCost;
use Betta\Models\NprCostCategory;
use Betta\Services\Generator\Foundation\AbstractReport;
use App\Http\Controllers\Program\Scopes\AbstractScopesController;

class SummaryReport extends AbstractReport
{
    /**
     * Bind the implementation
     *
     * @var
     */
    protected $excel;

    /**
     * Bind the implementation
     *
     * @var Betta\Models\Program
     */
    protected $program;

    /**
     * Bind the implementation
     *
     * @var Betta\Models\CostCenter
     */
    protected $costcenter;

    /**
     * Bind the implementation
     *
     * @var Betta\Models\InvoiceHistory
     */
    protected $invoicehistory;

    /**
     * Bind the implementation
     *
     * @var Betta\Models\Contract
     */
    protected $contract;

    /**
     * Bind the implementation
     *
     * @var Betta\Models\Nomination
     */
    protected $nomination;

    /**
     * Contain tabs
     *
     * @var mixed
     */
    protected $programfee;
    protected $programshonorarium;
    protected $programsoops;
    protected $theaterhonorarium;
    protected $theateroops;
    protected $theaterfees;
    protected $trainanexperthonorarium;
    protected $trainanexpertoops;
    protected $trainanexpertfees;
    protected $traininghonorarium;
    protected $trainingfee;
    protected $conversationshonorarium;
    protected $conversationsoops;
    protected $programtravelfee;
    protected $programmealfee;

    /**
     * Title of the Report
     *
     * @var string
     */
    protected $title = 'Invoice';

    /**
     * Value to populate in the Report
     *
     * @var string
     */
    protected $description = 'Invoice';

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
        'programStatus',
        'programType',
        'fields',
        'programSpeakers',
        'costs',
        'budgetJars',
    ];


     /**
     * Always fetch these relations for the Resource
     *
     * @var array
     */
    protected $memorelations = [
        'accounts',
    ];


    /**
     * Exclude the statuses
     *
     * @var Array
     */
    protected $excludeStatuses = [
        ProgramStatus::DRAFT,
        ProgramStatus::SUBMITTED,
        ProgramStatus::DENIED,
        ProgramStatus::CANCELLED,
    ];


    /**
     * Include Program Types
     *
     * @var array
     */
    protected $includeProgramtype = [
        ProgramType::BREAKFAST,
        ProgramType::LUNCH,
        ProgramType::DINNER,
        ProgramType::AAE,
        ProgramType::AUDIO,
        ProgramType::IMPACT_BREAKFAST,
        ProgramType::IMPACT_LUNCH,
        ProgramType::IMPACT_DINNER,
        ProgramType::UNBRANDED_BREAKFAST,
        ProgramType::UNBRANDED_LUNCH,
        ProgramType::UNBRANDED_DINNER,
        ProgramType::BRANDED_BREAKFAST,
        ProgramType::BRANDED_LUNCH,
        ProgramType::BRANDED_DINNER,
    ];

    /**
     * Include Program Types
     *
     * @var array
     */
    protected $includeProgramTheatertype = [
        ProgramType::PRODUCT_THEATER_BREAKFAST,
        ProgramType::PRODUCT_THEATER_LUNCH,
        ProgramType::PRODUCT_THEATER_DINNER,
    ];

    /**
     * Program Training Types
     *
     * @var array
     */
    protected $includeProgramTraintype = [
        ProgramType::TAE
    ];

    /**
     * Model Injection
     *
     * @param  Excel   $excel
     * @param  Program $program
     * @return Void
     */
    public function __construct(
        Excel $excel,
        Program $program,
        CostCenter $costcenter,
        InvoiceHistory $invoicehistory,
        NprCost $nprcost,
        NprCostCategory $nprcostcategory,
        Contract $contract,
        Nomination $nomination)
    {
        $this->excel   = $excel;
        $this->program = $program;
        $this->costcenter = $costcenter;
        $this->invoicehistory = $invoicehistory;
        $this->nprcost = $nprcost;
        $this->nprcostcategory = $nprcostcategory;
        $this->contract = $contract;
        $this->nomination = $nomination;
    }

    /**
     * Produce the report
     *
     * @return Excel
     */
    protected function process()
    {
        return $this->excel->create($this->getReportName(), function($excel){
            # Set standard properties on the file
            $this->setProperties($excel);

            # Produce the tab
            # @todo exctract tabs into their own classes
            $excel->sheet('Summary', function ($sheet) {
                 $sheet->loadView('reports.invoice.summary.report')
                      ->with('programs',  $this->candidates )
                      ->with('costcenter',  $this->getCostCenter() )
                      ->with('invoicehistory',  $this->getInvoicehistory() )
                      ->with('programfee',  $this->getProgramFee() )
                      ->with('programmealfee',  $this->getProgramMealFee() )
                      ->with('programtravelfee',  $this->getProgramTravelFee() )
                      ->with('programshonorarium',  $this->getProgramsHonorarium() )
                      ->with('programsoops',  $this->getProgramsOOPs() )
                      ->with('theaterhonorarium',  $this->getTheaterHonorarium() )
                      ->with('theateroops',  $this->getTheaterOOPs() )
                      ->with('theaterfees',  $this->getTheaterFees() )
                      ->with('trainanexperthonorarium',  $this->getTrainHonorarium() )
                      ->with('trainanexpertoops',  $this->getTrainOOPs() )
                      ->with('trainanexpertfees',  $this->getTrainFees() )
                      ->with('traininghonorarium',  $this->getTrainingHonorarium() )
                      ->with('trainingfee',  $this->getTrainingFee() )
                      ->with('conversationshonorarium',  $this->getConversationsHonorarium() )
                      ->with('conversationsoops',  $this->getConversationsOOPs() )
                      ->with('totalsummarycost',  $this->getTotalSummaryCost() )
                      ->with('brandname',  $this->getSubmitbrand() )
                      ->withMonthNamefromDateRange($this->getMonthNamefromDateRange($this->arguments))
                      ->withIncludeProgramtype($this->includeProgramtype)
                      ->withIncludeProgramTheatertype($this->includeProgramTheatertype)
                      ->withIncludeProgramTraintype($this->includeProgramTraintype)
                      ->setColumnFormat( $this->getSummaryFormats() );
            });

            $excel->sheet('Reconciled', function ($sheet) {
                 $sheet->loadView('reports.invoice.summary.reconciled')
                      ->with('allprograms',  $this->getReconciled() )
                      ->with('programs',  $this->getReconciledBasic() )
                      ->with('theaterprograms',  $this->getReconciledTheater() )
                      ->with('trainanexpertprograms',  $this->getReconciledTrainanexpert() )
                      ->setAutoFilter()
                      ->freezeFirstRow()
                      ->setColumnFormat( $this->getFormats() );
            });

            $excel->sheet('Unreconciled', function ($sheet) {
                 $sheet->loadView('reports.invoice.summary.unreconciled')
                      ->with('allprograms',  $this->getUnreconciled() )
                      ->with('programs',  $this->getUnreconciledBasic() )
                      ->with('theaterprograms',  $this->getUnreconciledTheater() )
                      ->with('trainanexpertprograms',  $this->getUnreconciledTrainanexpert() )
                      ->setAutoFilter()
                      ->freezeFirstRow()
                      ->setColumnFormat( $this->getFormats() );
            });

            $excel->sheet('Non Program Related', function ($sheet) {
                 $sheet->loadView('reports.invoice.summary.non_program_related')
                      ->withNprcosts($this->getNprCosts($this->arguments) )
                      ->withNprcostcategories($this->nprcostcategory->with('nprCostItems')->get() )
                      ->withMonthYearNamefromDateRange($this->getMonthYearNamefromDateRange($this->arguments))
                      ->withNominations($this->getNomination($this->arguments))
                      ->withContracts($this->getContract($this->arguments))
                      ->setAutoFilter()
                      ->setWidth('A', 50)
                      ->setWidth('B', 50)
                      ->setWidth('C', 10)
                      ->setWidth('D', 15)
                      ->setWidth('E', 5)
                      ->setWidth('F', 10)
                      ->setWidth('G', 15)
                      ->setWidth('H', 15)
                      ->setWidth('I', 15)
                      ->setWidth('J', 15)
                      ->setWidth('K', 15)
                      ->setWidth('L', 15)
                      ->setWidth('M', 15)
                      ->setWidth('N', 15)
                      ->setWidth('O', 15)
                      ->setWidth('P', 15)
                      ->setWidth('Q', 15)
                      ->setWidth('R', 15)
                      ->setWidth('S', 15)
                      ->setWidth('T', 15)
                      ->setWidth('U', 15)
                      ->setWidth('V', 15)
                      ->setWidth('W', 15)
                      ->setColumnFormat( $this->getNprFormats() );
            });

            $excel->sheet('Accounting JE', function ($sheet) {
                 $sheet->loadView('reports.invoice.summary.accounting_JE')
                      ->with('allreconsileprograms',  $this->getReconciled() )
                      ->with('allunreconsileprograms',  $this->getUnreconciled() )
                      ->with('memodetails', $this->getAccountingDetail())
                      ->with('reconciledBasicprograms',  $this->getReconciledBasic() )
                      ->with('unreconciledBasicprograms',  $this->getUnreconciledBasic() )
                      ->with('reconciledtheaterBasicprograms',  $this->getReconciledTheater() )
                      ->with('unreconciledtheaterBasicprograms',  $this->getUnreconciledTheater() )
                      ->with('reconciledTrainanexpertBasicprograms',  $this->getReconciledTrainanexpert() )
                      ->with('unreconciledTrainanexpertBasicprograms',  $this->getUnreconciledTrainanexpert() )
                      ->withNprcosts($this->getNprCosts($this->arguments) )
                      ->withNominations($this->getNomination($this->arguments))
                      ->withContracts($this->getContract($this->arguments))
                      ->with('brandname',  $this->getSubmitbrand() )
                      ->setAutoFilter()
                      ->freezeFirstRow()
                      ->setColumnFormat( $this->getFormats() );
            });

            # Set the includeSql = true to have SQL Printout tab
            # includeSql should NEVER be true for production reports
            $this->includeSqlTab($excel);

            # Make the first sheet active
            $excel->setActiveSheetIndex(0);

        })->store('xlsx', $this->getReportPath(), true);
    }

    /**
     * Return merge data for the report
     *
     * @param  array $arguments
     * @return Collection
     */
    protected function loadMergeData($arguments)
    {
        $inBrand = array_get($arguments, 'inBrand' );
        $inBrand = count($inBrand) > 1 ? $inBrand->first()->id : $inBrand;

        $filterFrom = array_get($arguments, 'from', $this->getDefaultFrom());
        $filterTo = array_get($arguments, 'to', $this->getDefaultTo());

        return $this->program
                    ->notDraft()
                    ->byBrand($inBrand)
                    ->betweenDates($filterFrom, $filterTo)
                    ->with($this->relations)
                    ->get();
    }

    /**
     * Default Filter From

     * @return Carbon\Carbon
     */
    protected function getDefaultFrom()
    {
        return Carbon::parse('January 1');
    }

    /**
     * Default To Date
     *
     * @return Carbon\Carbon
     */
    protected function getDefaultTo()
    {
        return Carbon::parse('December 31');
    }

    /**
     * Default current Year
     *
     * @return int
     */
    protected function getDefaultFromYear()
    {
        return $this->getDefaultFrom()->year;
    }

    /**
     * Default To Year
     *
     * @return int
     */
    protected function getDefaultToYear()
    {
        return $this->getDefaultTo()->year;
    }

    /**
     * Resolve User
     *
     * @return User | null
     */
    protected function getUser()
    {
        return auth()->user();
    }

    /**
     * Return Visible Brands of the User
     *
     * @return Collection
     */
    protected function getActiveBrands()
    {
        return object_get($this->getUser(), 'profile.active_brands', collect([]));
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
            'I:AA' => static::AS_CURRENCY,
            'C' => static::AS_DATE,
        ];
    }

    /**
     * Summary Formats
     *
     * @return array
     */
    public function getSummaryFormats()
    {
        return [
            'C41:C60' => static::AS_CURRENCY,
            'D9:D37' => static::AS_CURRENCY,
            'E:BK' => static::AS_CURRENCY,
        ];
    }

    /**
     * Non Program Related Costs Formats
     *
     * @return array
     */
    public function getNprFormats()
    {
        return [
            'D1:D20'  => static::AS_CURRENCY,
            'G:BK'  => static::AS_CURRENCY,
        ];
    }

    /**
     * [getCostCenter description]
     *
     * @return Collection
     */
    protected function getCostCenter()
    {
        $inBrand = array_get($this->arguments, 'inBrand' );
        $inBrand = count($inBrand) > 1 ? $inBrand->first()->id : $inBrand;
        $From    = array_get($this->arguments , 'from', $this->getDefaultFromYear() );

        return $this->costcenter->Bybrand($inBrand)->year($From)->get();
    }

    /**
     * [getInvoicehistory description]
     *
     * @return Collection
     */
    protected function getInvoicehistory()
    {
        $inBrand = array_get($this->arguments, 'inBrand' );
        $inBrand = count($inBrand) > 1 ? $inBrand->first()->id : $inBrand;
        return $this->invoicehistory->Bybrand($inBrand)->Where('invoice_type', 'Program')->OrderLatestFirst()->get();
    }

    /**
     * Filter the reconciled Programs only
     *
     * @return Collection
     */
    protected function getReconciled()
    {
        return $this->candidates->where('is_reconciled', true);
    }

    /**
     * Filter the Basic Programs
     *
     * @return Collection
     */
    protected function getReconciledBasic()
    {
        return $this->getReconciled()->whereIn('program_type_id', $this->includeProgramtype);
    }

    /**
     * Resolve Reconciled Product Theaters
     *
     * @return Collection
     */
    protected function getReconciledTheater()
    {
        return $this->getReconciled()->whereIn('program_type_id', $this->includeProgramTheatertype);
    }

    /**
     * Resolve Training
     *
     * @return Collection
     */
    protected function getReconciledTrainanexpert()
    {
        return $this->getReconciled()->whereIn('program_type_id', $this->includeProgramTraintype);
    }

    /**
     * Filter Unreconciled only
     * the candidates DO exlucde draft, check for that is excessive
     *
     * @return Collection
     */
    protected function getUnreconciled()
    {
        return $this->candidates->where('is_reconciled', false);
    }

    /**
     * Filter Unreconciled only
     *
     * @return Collection
     */
    protected function getUnreconciledBasic()
    {
        return $this->getUnreconciled()->whereIn('program_type_id', $this->includeProgramtype);
    }

    /**
     * Filter Unreconciled Theater
     *
     * @return Collection
     */
    protected function getUnreconciledTheater()
    {
        return $this->getUnreconciled()->whereIn('program_type_id', $this->includeProgramTheatertype);
    }

    /**
     * Filter Trainings that are unreconciled
     *
     * @return Collection
     */
    protected function getUnreconciledTrainanexpert()
    {
        return $this->getUnreconciled()->whereIn('program_type_id', $this->includeProgramTraintype);
    }

    /**
     * Get non program related costs
     *
     * @return Collection
     */
    protected function getNprCosts($arguments)
    {
        $inBrand = array_get($arguments, 'inBrand' );
        $inBrand = count($inBrand) > 1 ? $inBrand->first()->id : $inBrand;

        $filterFrom = array_get($arguments, 'from', $this->getDefaultFrom());
        $filterTo = array_get($arguments, 'to', $this->getDefaultTo());

        return $this->nprcost
                    ->byBrand($inBrand)
                    ->betweenDates($filterFrom, $filterTo)
                    ->get();
    }

    /**
     * Get count of nominations valid withing the period for the given brand
     *
     * @return Collection
     */
    protected function getNomination($arguments)
    {
        $inBrand = array_get($arguments, 'inBrand' );
        $inBrand = count($inBrand) > 1 ? $inBrand->first()->id : $inBrand;

        $filterFrom = array_get($arguments, 'from', $this->getDefaultFrom());
        $filterTo = array_get($arguments, 'to', $this->getDefaultTo());

        return $this->nomination->betweenDates($filterFrom, $filterTo)
                                #->where('nomination_status_id', 28)
                                ->inBrand($inBrand)
                                ->get();
    }

    /**
     * Get count of contracts valid withing the period for the given brand
     *
     * @return Collection
     */
    protected function getContract($arguments)
    {
        $inBrand = array_get($arguments, 'inBrand' );
        $inBrand = count($inBrand) > 1 ? $inBrand->first()->id : $inBrand;

        $filterFrom = array_get($arguments, 'from', $this->getDefaultFrom());
        $filterTo = array_get($arguments, 'to', $this->getDefaultTo());

        return $this->contract->betweenDates($filterFrom, $filterTo)
                                #->where('contract_status_id', 6)
                                ->inBrand($inBrand)
                                ->get();
    }

    /**
     * Resolve Program Fee
     *
     * @return Collection
     */
    protected function getProgramFee()
    {
        return $this->programfee = $this->candidates->sum(function($item){
                                        if(in_array($item->program_type_id, $this->includeProgramtype)){
                                             if($item->is_reconciled){
                                                //$management_fee =  $item->management_fee_category_costs ? $item->management_fee_category_costs->sum('real') : 0;
                                                $management_fee =  $item->base_fee_category_costs ? $item->base_fee_category_costs->sum('real') : 0;
                                                //$check_processing_costs =  $item->check_processing_costs ? $item->check_processing_costs->sum('real') : 0;
                                                //$attendee_closeout_costs =  $item->attendee_closeout_costs ? $item->attendee_closeout_costs->sum('real') : 0;
                                                //$program_materials_fulfillment =  $item->program_materials_fulfillment_costs ? $item->program_materials_fulfillment_costs->sum('real') : 0;
                                                $other_fee_category_costs =  $item->other_fee_category_costs ? $item->other_fee_category_costs->sum('real') : 0;
                                             }else{
                                                //$management_fee =  $item->management_fee_category_costs ? $item->management_fee_category_costs->sum('calculated') : 0;
                                                $management_fee =  $item->base_fee_category_costs ? $item->base_fee_category_costs->sum('calculated') : 0;
                                                //$check_processing_costs =  $item->check_processing_costs ? $item->check_processing_costs->sum('calculated') : 0;
                                                //$attendee_closeout_costs =  $item->attendee_closeout_costs ? $item->attendee_closeout_costs->sum('calculated') : 0;
                                               // $program_materials_fulfillment =  $item->program_materials_fulfillment_costs ? $item->program_materials_fulfillment_costs->sum('calculated') : 0;
                                                $other_fee_category_costs =  $item->other_fee_category_costs ? $item->other_fee_category_costs->sum('calculated') : 0;
                                             }


                                            //return $management_fee+$check_processing_costs+$attendee_closeout_costs+$program_materials_fulfillment+$other_fee_category_costs;
                                            return $management_fee+$other_fee_category_costs;
                                        }
                                    });
    }

    protected function getProgramsHonorarium()
    {
        return $this->programshonorarium = $this->candidates->sum(function($item) {
                                            if(in_array($item->program_type_id, $this->includeProgramtype)  && $item->program_status_id != 1){
                                                if($item->is_reconciled){
                                                    return $item->speaker_honorarium_costs->sum('real');
                                                }else{
                                                    return $item->speaker_honorarium_costs->sum('calculated');
                                                }

                                            }
                                    });
    }

    protected function getProgramMealFee()
    {

        return $this->programmealfee = $this->candidates->sum(function($item) {

                                        if(in_array($item->program_type_id, $this->includeProgramtype) && $item->program_status_id != 1){
                                            if($item->is_reconciled){
                                                return $item->fb_category_costs->sum('real');
                                            }else{
                                                return $item->fb_category_costs->sum('calculated');
                                            }

                                        }

                                    });
    }


    protected function getProgramTravelFee()
    {

        return $this->programtravelfee = $this->candidates->sum(function($item) {
                                            if(in_array($item->program_type_id, $this->includeProgramtype)  && $item->program_status_id != 1){

                                                if($item->is_reconciled){
                                                    $travel_air =  $item->travel_air_costs ? $item->travel_air_costs->sum('real') : 0;
                                                    $travel_hotel =  $item->travel_hotel_costs ? $item->travel_hotel_costs->sum('real') : 0;
                                                    $speaker_expense =  ($item->expense_speaker_costs ? $item->expense_speaker_costs->sum('real') : 0);
                                                    $ground_transportation_cost =  (($item->ground_transportation || $item->travel_car_costs || $item->travel_train_costs) ? $item->ground_transportation->sum('real') + $item->travel_car_costs->sum('real') + $item->travel_train_costs->sum('real') : 0);
                                                    //$other_fee_category_costs =  $item->other_fee_category_costs ? $item->other_fee_category_costs->sum('real') : 0;

                                                }else{
                                                    $travel_air =  $item->travel_air_costs ? $item->travel_air_costs->sum('calculated') : 0;
                                                    $travel_hotel =  $item->travel_hotel_costs ? $item->travel_hotel_costs->sum('calculated') : 0;
                                                    $speaker_expense =  ($item->expense_speaker_costs ? $item->expense_speaker_costs->sum('calculated') : 0);
                                                    $ground_transportation_cost =  (($item->ground_transportation || $item->travel_car_costs || $item->travel_train_costs) ? $item->ground_transportation->sum('calculated') + $item->travel_car_costs->sum('calculated') + $item->travel_train_costs->sum('calculated') : 0);
                                                    //$other_fee_category_costs =  $item->other_fee_category_costs ? $item->other_fee_category_costs->sum('calculated') : 0;

                                                }

                                                //return $travel_air+$travel_hotel+$speaker_expense+$other_fee_category_costs;
                                                return $travel_air+$travel_hotel+$speaker_expense+$ground_transportation_cost;
                                            }
                                    });
    }



    protected function getProgramsOOPs()
    {
        return $this->programsoops = $this->candidates->sum(function($item)  {
                                        if(in_array($item->program_type_id, $this->includeProgramtype)  && $item->program_status_id != 1){
                                                 if($item->is_reconciled){
                                                    #$room_rental =  $item->room_rental_costs ? $item->room_rental_costs->sum('real') : 0;
                                                    $av_category =  $item->av_category_costs ? $item->av_category_costs->sum('real') : 0;
                                                    $invite =  $item->invite_costs ? $item->invite_costs->sum('real') : 0;
                                                 }else{
                                                    #$room_rental =  $item->room_rental_costs ? $item->room_rental_costs->sum('calculated') : 0;
                                                    $av_category =  $item->av_category_costs ? $item->av_category_costs->sum('calculated') : 0;
                                                    $invite =  $item->invite_costs ? $item->invite_costs->sum('calculated') : 0;
                                                 }



                                                return $av_category+$invite;
                                            }
                                    });
    }

    protected function getTheaterHonorarium()
    {
        return $this->theaterhonorarium = $this->candidates->sum(function($item) {
                                        if(in_array($item->program_type_id, $this->includeProgramTheatertype)  && $item->program_status_id != 1){
                                                if($item->is_reconciled){
                                                     return $item->honorarium_category_costs->sum('real');
                                                }else{
                                                     return $item->honorarium_category_costs->sum('calculated');
                                                }

                                            }
                                    });
    }

    protected function getTheaterOOPs()
    {

        return $this->theateroops = $this->candidates->sum(function($item) {
                                        if(in_array($item->program_type_id, $this->includeProgramTheatertype)  && $item->program_status_id != 1){
                                                if($item->is_reconciled){
                                                    $fb_costs =  $item->fb_category_costs ? $item->fb_category_costs->sum('real') : 0;
                                                    #$room_rental =  $item->room_rental_costs ? $item->room_rental_costs->sum('real') : 0;
                                                    $av_category =  $item->av_category_costs ? $item->av_category_costs->sum('real') : 0;
                                                    $invite =  $item->invite_costs ? $item->invite_costs->sum('real') : 0;
                                                    $shipping =  $item->shipping_costs ? $item->shipping_costs->sum('real') : 0;
                                                    $travel_air =  $item->travel_air_costs ? $item->travel_air_costs->sum('real') : 0;
                                                    $travel_hotel =  $item->travel_hotel_costs ? $item->travel_hotel_costs->sum('real') : 0;
                                                    $speaker_expense =  ($item->expense_speaker_costs ? $item->expense_speaker_costs->sum('real') : 0);
                                                }else{
                                                     $fb_costs =  $item->fb_category_costs ? $item->fb_category_costs->sum('calculated') : 0;
                                                    #$room_rental =  $item->room_rental_costs ? $item->room_rental_costs->sum('calculated') : 0;
                                                    $av_category =  $item->av_category_costs ? $item->av_category_costs->sum('calculated') : 0;
                                                    $invite =  $item->invite_costs ? $item->invite_costs->sum('calculated') : 0;
                                                    $shipping =  $item->shipping_costs ? $item->shipping_costs->sum('calculated') : 0;
                                                    $travel_air =  $item->travel_air_costs ? $item->travel_air_costs->sum('calculated') : 0;
                                                    $travel_hotel =  $item->travel_hotel_costs ? $item->travel_hotel_costs->sum('calculated') : 0;
                                                    $speaker_expense =  $item->expense_speaker_costs ? $item->expense_speaker_costs->sum('calculated') : 0;
                                                }

                                                return $fb_costs+$av_category+$invite+$shipping+$travel_air+$travel_hotel+$speaker_expense;
                                            }
                                    });
    }

    protected function getTheaterFees()
    {

        return $this->theaterfees = $this->candidates->sum(function($item) {
                                        if(in_array($item->program_type_id, $this->includeProgramTheatertype)  && $item->program_status_id != 1){
                                            if($item->is_reconciled){
                                                //$management_fee =  $item->management_fee_category_costs ? $item->management_fee_category_costs->sum('real') : 0;
                                                $management_fee =  (($item->base_fee_category_costs || $item->product_theater_costs) ? $item->base_fee_category_costs->sum('real') + $item->product_theater_costs->sum('real') : 0);
                                                //$check_processing_costs =  $item->check_processing_costs ? $item->check_processing_costs->sum('real') : 0;
                                                //$attendee_closeout_costs =  $item->attendee_closeout_costs ? $item->attendee_closeout_costs->sum('real') : 0;
                                                //$program_materials_fulfillment =  $item->program_materials_fulfillment_costs ? $item->program_materials_fulfillment_costs->sum('real') : 0;
                                                $other_fee_category_costs =  $item->other_fee_category_costs ? $item->other_fee_category_costs->sum('real') : 0;
                                             }else{
                                                //$management_fee =  $item->management_fee_category_costs ? $item->management_fee_category_costs->sum('calculated') : 0;
                                                $management_fee = (($item->base_fee_category_costs || $item->product_theater_costs) ? $item->base_fee_category_costs->sum('calculated') + $item->product_theater_costs->sum('calculated') : 0);
                                                //$check_processing_costs =  $item->check_processing_costs ? $item->check_processing_costs->sum('calculated') : 0;
                                                //$attendee_closeout_costs =  $item->attendee_closeout_costs ? $item->attendee_closeout_costs->sum('calculated') : 0;
                                               // $program_materials_fulfillment =  $item->program_materials_fulfillment_costs ? $item->program_materials_fulfillment_costs->sum('calculated') : 0;
                                                $other_fee_category_costs =  $item->other_fee_category_costs ? $item->other_fee_category_costs->sum('calculated') : 0;
                                             }


                                            //return $management_fee+$check_processing_costs+$attendee_closeout_costs+$program_materials_fulfillment+$other_fee_category_costs;
                                            return $management_fee+$other_fee_category_costs;
                                        }
                                    });
    }




    protected function getTrainHonorarium()
    {
        return $this->trainanexperthonorarium = $this->candidates->sum(function($item) {
                                        if(in_array($item->program_type_id, $this->includeProgramTraintype)  && $item->program_status_id != 1){
                                                if($item->is_reconciled){
                                                     return $item->honorarium_category_costs->sum('real');
                                                }else{
                                                     return $item->honorarium_category_costs->sum('calculated');
                                                }

                                            }
                                    });
    }

    protected function getTrainOOPs()
    {

        return $this->trainanexpertoops = $this->candidates->sum(function($item) {
                                        if(in_array($item->program_type_id, $this->includeProgramTraintype)  && $item->program_status_id != 1){
                                                if($item->is_reconciled){
                                                    $fb_costs =  $item->fb_category_costs ? $item->fb_category_costs->sum('real') : 0;
                                                    #$room_rental =  $item->room_rental_costs ? $item->room_rental_costs->sum('real') : 0;
                                                    $av_category =  $item->av_category_costs ? $item->av_category_costs->sum('real') : 0;
                                                    $invite =  $item->invite_costs ? $item->invite_costs->sum('real') : 0;
                                                    $shipping =  $item->shipping_costs ? $item->shipping_costs->sum('real') : 0;
                                                    $travel_air =  $item->travel_air_costs ? $item->travel_air_costs->sum('real') : 0;
                                                    $travel_hotel =  $item->travel_hotel_costs ? $item->travel_hotel_costs->sum('real') : 0;
                                                    $speaker_expense =  $item->expense_speaker_costs ? $item->expense_speaker_costs->sum('real') : 0;
                                                }else{
                                                     $fb_costs =  $item->fb_category_costs ? $item->fb_category_costs->sum('calculated') : 0;
                                                    #$room_rental =  $item->room_rental_costs ? $item->room_rental_costs->sum('calculated') : 0;
                                                    $av_category =  $item->av_category_costs ? $item->av_category_costs->sum('calculated') : 0;
                                                    $invite =  $item->invite_costs ? $item->invite_costs->sum('calculated') : 0;
                                                    $shipping =  $item->shipping_costs ? $item->shipping_costs->sum('calculated') : 0;
                                                    $travel_air =  $item->travel_air_costs ? $item->travel_air_costs->sum('calculated') : 0;
                                                    $travel_hotel =  $item->travel_hotel_costs ? $item->travel_hotel_costs->sum('calculated') : 0;
                                                    $speaker_expense = $item->expense_speaker_costs ? $item->expense_speaker_costs->sum('calculated') : 0;
                                                }

                                                return $fb_costs+$av_category+$invite+$shipping+$travel_air+$travel_hotel+$speaker_expense;
                                            }
                                    });
    }

    protected function getTrainFees()
    {

        return $this->trainanexpertfees = $this->candidates->sum(function($item) {
                                        if(in_array($item->program_type_id, $this->includeProgramTraintype)  && $item->program_status_id != 1){
                                            if($item->is_reconciled){
                                               //$management_fee =  $item->management_fee_category_costs ? $item->management_fee_category_costs->sum('real') : 0;
                                                $management_fee =  (($item->base_fee_category_costs || $item->speaker_training_fee) ? $item->base_fee_category_costs->sum('real') + $item->speaker_training_fee->sum('real') : 0);
                                                //$check_processing_costs =  $item->check_processing_costs ? $item->check_processing_costs->sum('real') : 0;
                                                //$attendee_closeout_costs =  $item->attendee_closeout_costs ? $item->attendee_closeout_costs->sum('real') : 0;
                                                //$program_materials_fulfillment =  $item->program_materials_fulfillment_costs ? $item->program_materials_fulfillment_costs->sum('real') : 0;
                                                $other_fee_category_costs =  $item->other_fee_category_costs ? $item->other_fee_category_costs->sum('real') : 0;
                                             }else{
                                                //$management_fee =  $item->management_fee_category_costs ? $item->management_fee_category_costs->sum('calculated') : 0;
                                                $management_fee =  (($item->base_fee_category_costs || $item->speaker_training_fee) ? $item->base_fee_category_costs->sum('calculated') + $item->speaker_training_fee->sum('real') : 0);
                                                //$check_processing_costs =  $item->check_processing_costs ? $item->check_processing_costs->sum('calculated') : 0;
                                                //$attendee_closeout_costs =  $item->attendee_closeout_costs ? $item->attendee_closeout_costs->sum('calculated') : 0;
                                               // $program_materials_fulfillment =  $item->program_materials_fulfillment_costs ? $item->program_materials_fulfillment_costs->sum('calculated') : 0;
                                                $other_fee_category_costs =  $item->other_fee_category_costs ? $item->other_fee_category_costs->sum('calculated') : 0;
                                             }


                                            //return $management_fee+$check_processing_costs+$attendee_closeout_costs+$program_materials_fulfillment+$other_fee_category_costs;
                                            return $management_fee+$other_fee_category_costs;

                                        }
                                    });
    }





    protected function getTrainingHonorarium()
    {
        return $this->traininghonorarium = $this->candidates->sum(function($item) {
                                        return 0;
                                    });
    }

    protected function getTrainingFee()
    {
        return $this->trainingfee = $this->candidates->sum(function($item) {
                                        return 0;
                                    });
    }

    protected function getConversationsHonorarium()
    {
        return $this->conversationshonorarium = $this->candidates->sum(function($item) {
                                        return 0;
                                    });
    }

    protected function getConversationsOOPs()
    {
        return $this->conversationsoops = $this->candidates->sum(function($item) {
                                        return 0;
                                    });
    }

    protected function getTotalSummaryCost()
    {
        return      $this->programfee
                +   $this->programshonorarium
                +   $this->programsoops
                +   $this->theaterhonorarium
                +   $this->theateroops
                +   $this->theaterfees
                +   $this->trainanexperthonorarium
                +   $this->trainanexpertoops
                +   $this->trainanexpertfees
                +   $this->traininghonorarium
                +   $this->trainingfee
                +   $this->conversationshonorarium
                +   $this->programtravelfee
                +   $this->programmealfee
                +   $this->conversationsoops;
    }

    protected function getSubmitbrand()
    {
        $inBrand = array_get($this->arguments, 'inBrand' );
        $inBrand = count($inBrand) > 1 ? $inBrand->first()->id : $inBrand;

        $Brand = Brand::find($inBrand);
        return $Brand->label;
    }


    protected function getAccountingDetail(){
        $accountDetail = InvoiceMemo::with($this->memorelations)->get();
        return $accountDetail;
    }

    protected function getMonthNamefromDateRange($arguments)
    {

        $filterFrom = array_get($arguments, 'from', $this->getDefaultFrom());
        $filterTo = array_get($arguments, 'to', $this->getDefaultTo());
        //$month = array_map('getMonthName', range($filterFrom->format('m'),$filterTo->format('m')));
        $month = getMonthName($filterFrom, $filterTo);

        return $month;
    }

    protected function getMonthYearNamefromDateRange($arguments)
    {

        $filterFrom = array_get($arguments, 'from', $this->getDefaultFrom());
        $filterTo = array_get($arguments, 'to', $this->getDefaultTo());
        $month = getMonthYearName($filterFrom, $filterTo);

        return $month;
    }


}
