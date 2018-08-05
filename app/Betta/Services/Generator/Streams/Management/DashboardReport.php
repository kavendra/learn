<?php

namespace Betta\Services\Generator\Streams\Management;

use Betta\Models\Program;
use Betta\Models\Brand;
use Betta\Models\Profile;
use Betta\Models\ProgramStatus;
use Betta\Models\ProgramType;
use Betta\Models\CancellationReason;
use Betta\Models\BudgetJar;
use Betta\Models\BudgetType;
use Maatwebsite\Excel\Excel;
use Betta\Services\Generator\Foundation\AbstractReport;
use App\Http\Controllers\Program\Scopes\AbstractScopesController;
use Carbon\Carbon;
use Auth;

class DashboardReport extends AbstractReport
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
     * @var Model
     */
    protected $program;
    protected $brand;

    /**
     * Title of the Report
     *
     * @var string
     */
    protected $title = 'Dashboard Report';


    /**
     * Value to populate in the Report
     *
     * @var string
     */
    protected $description = 'All-encompassing Brand Dashboard Report';


    /**
     * Include SQL tab
     *
     * @var boolean
     */
    protected $includeSql = false;

	protected $first = 26;
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

    protected $excludeProgramTraintype = [
        ProgramType::TAE
    ];


    protected $months = [
     1=>'January',
     2=>'February',
     3=>'March',
     4=>'April',
     5=>'May',
     6=>'June',
     7=>'July',
     8=>'August',
     9=>'September',
     10=>'October',
     11=>'November',
     12=>'December',
    ];


    /**
     * Model Injection
     *
     * @param  Excel   $excel
     * @param  Program $program
     * @return Void
     */
    public function __construct(Excel $excel, Brand $brand, Program $program, ProgramType $programtype, CancellationReason $cancellationreason, BudgetJar $budgetjar, Profile $profile, BudgetType $budgettype)
    {
        $this->excel   = $excel;
        $this->program = $program;
        $this->programtype = $programtype;
        $this->cancellationreason = $cancellationreason;
        $this->budgetjar = $budgetjar;
        $this->brand = $brand;
        $this->profile = $profile;
        $this->budgettype = $budgettype;
    }


    protected function process()
    {
		$this->arguments = $this->sanitizeArguments($this->arguments);

        return $this->excel->create($this->getReportName(), function($excel){

                    # Set standard properties on the file
                    $this->setProperties($excel);


                    # Produce the tab
                    # @todo exctract tabs into their own classes
                    $excel->sheet('Summary', function ($sheet) {
                        $sheet->loadView('reports.management.dashboard.report')
                              ->with($this->candidates)
                              ->with('arguments',  $this->arguments )
                              ->with('months',  $this->months )
                              ->with('programtypes',  $this->programtype->where('id', '<>', 7) )
                              //->with('cancellationreasons',  $this->cancellationreason )
                              //->setAutoFilter()
                              ->freezeFirstRow()
                              //->with('brand',  $this->getSubmitbrand() )
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
     * @return Array
     */
    protected function loadMergeData($arguments)
    {
 		return [
		  'brand'					=> $this->getBrand(),
		  'cancellationReasons' => $this->getCancellationReasons(),
		  'budgettypes'   => $this->getBudgetType(),
		];
    }


	/**
   * Load Program Types
   *
   * @return Collection
   */
	protected function getProgramTypes()
	{
		return $this->programtype
				->forBrand( $this->getInBrand()  )
				->get();
	}

	/**
   * Load Cancellation Reasons
   *
   * @return Collection
   */
  protected function getCancellationReasons()
  {
    return $this->cancellationreason
                ->get();
  }

	/**
   * Apply necessary mutations
   *
   * @param  array  $arguments
   * @return array
   */
  protected function sanitizeArguments( $arguments = [])
  {
		# Add comparison year
		return array_set($arguments, 'inComparisonYear', array_get($arguments, 'inYear', date('Y')) - 1  );
  }

    /**
     * Column Formats
     *
     * @see Betta\Services\Generator\Interfaces\ExcelFormatsInterface
     * @return array
     */
    public function getFormats()
    {
        return ['B9:M21' => static::AS_NICE_INTEGER]
         + $this->getProgramTypesFormats()+$this->getBudgetFormats();
    }


	/**
   * Assuming the number of months does not really change:
   *
   * @return Array
   */
  protected function getProgramTypesFormats()
  {
    $count = $this->getProgramTypes()->count();

    $first = $this->first;
    $last  = $first + $count+1; # count will push to totals

    $this->first = $last;

    return ["J{$first}:J{$last}" => static::AS_CURRENCY,
            "M{$first}:M{$last}" => static::AS_CURRENCY ];
  }

  /**
   * Assuming the number of months does not really change:
   *
   * @return Array
   */
  protected function getBudgetFormats()
  {
    $count = $this->getBrand()->presentations->count();
    $count += $this->getCancellationReasons()->count();

    $first = $this->first + $count +11;
    $last  = $first + $count+11; # count will push to totals

    $this->first = $last;

    return ["J{$first}:J{$last}" => static::AS_CURRENCY, 
			"K{$first}:K{$last}" => static::AS_CURRENCY, 
			"L{$first}:L{$last}" => static::AS_CURRENCY,
            "M{$first}:M{$last}" => static::AS_CURRENCY ];
  }

    protected function getSubmitbrand()
    {
        $inBrand = $this->getInBrand();

        $Brand = Brand::find($inBrand);
        return $Brand;
    }

     
	protected function getBudgetType()
  {
    $budgetRelations = [
      'profiles.territories',
      //'accruingPrograms.costs.context',
    ];
					
		return $this->budgettype			
				->isDirect()
				->valid()				
				->allowsProgram()				
				->forBrand( $this->getInBrand()  )
				->get();
  }
       /**
       * Resolve Brand
       *
       * @return int|null
       */
      protected function getInBrand()
      {
        return (int) array_get($this->arguments, 'inBrand');
      }

      /**
       * Resolve Year
       *
       * @return int|null
       */
      protected function getInYear()
      {
        return (int) array_get($this->arguments, 'inYear');
      }

	  /**
   * Load Brand
   *
   * @return Brand
   */
  protected function getBrand()
  {
    $relations = [

    ];
    return $this->brand
                ->with( $relations )
                ->findOrFail( $this->getInBrand() );
  }
}
