<?php

namespace Betta\Services\Generator\Streams\Grids;

use Carbon\Carbon;
use Maatwebsite\Excel\Excel;
use Betta\Models\ProfileHonorariumRate;
use Betta\Services\Generator\Foundation\AbstractReport;

class SpeakerHonorariumRate extends AbstractReport
{
	
	
	  /**
   * Bind the implementation
   * 
   * @var Model
   */
  protected $profilehonorariumrates;

  /**
   * Bind the implementation
   * 
   * @var Carbon\Carbon
   */
  protected $carbon;
  
    /**
     * Title of the Report
     *
     * @var string
     */
    protected $title = 'Speaker Honorarium Rate Report';


    /**
     * Value to populate in the Report
     *
     * @var string
     */
    protected $description = 'Speaker Honorarium Rate Grid Report';


    /**
     * Include SQL tab
     *
     * @var boolean
     */
    protected $includeSql = true;


	/**
   * Always fetch these relations for the Main Resource
   * 
   * @var array
   */
  protected $relations = array(
    	'rateCard',
		'brand',
		'profile',
  );


    /**
     * Create new Instance of Conference List Report

     * @param  Conference Conference $conference
     * @return Void
     */
    public function __construct(Excel $excel, ProfileHonorariumRate $profilehonorariumrates, Carbon $carbon)
    {
        $this->excel = $excel;
        $this->profilehonorariumrates = $profilehonorariumrates;
		$this->carbon         = $carbon;
    }


    protected function process()
    {
		
		
    # make new template
    return $this->excel
                ->create( $this->getReportName(), function($excel) 
                  {
                    # And this is the Writer
                    $excel->setTitle( $this->title )
                          ->setCreator( 'Betta Data Team' )
                          ->setCompany( 'Frictionless Solutions' )
                          ->setDescription( $this->description );

                    #method could be repeated
                    $excel->sheet('Brand Honorarium Report', function($sheet)
                            {
                              $sheet->loadView('reports.grids.speaker-honorarium-rate.report' )
                                    //->withFromDate( $this->getFromDate() )
                                    ->withProfilehonorariumrates( $this->candidates )
                                    ->freezeFirstRow()
                                    ->setColumnFormat( $this->getFormats() )
                                    # Make sure to use it last
                                    # This is becuase Excel cannot always figure out we have data. So.
                                    ->setAutoFilter();
                            });

                    # Set the includeSql = true to have SQL Printout tab
                    # includeSql should NEVER be true for production reports
                    $this->includeSqlTab($excel);
                    
                    # Make the first sheet active
                    $excel->setActiveSheetIndex(0);
                          
                  })
                ->store( 'xlsx', $this->getReportPath(), true );
  
    }


    /**
     * Return merge data for the report
     *
     * @param  array $arguments
     * @return Array
     */
    protected function loadMergeData($arguments)
    {
        
    	return $this->profilehonorariumrates
                ->get();
  
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
		  /*'B' => static::AS_DATE,
		  'H' => static::AS_NICE_INTEGER,
		  'P:W' => static::AS_CURRENCY,*/
		];
  	}
  
   /**
   * Resolve the fromDate from the Arguments
   * 
   * @return Carbon
   */
  private function getFromDate()
  {
    return $this->carbon
                ->createFromTimestamp( strtotime(array_get( $this->arguments, 'from', time()) ));
  }
  
}
