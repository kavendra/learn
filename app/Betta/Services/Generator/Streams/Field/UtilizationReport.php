<?php

namespace Betta\Services\Generator\Streams\Field;

use Carbon\Carbon;
use Maatwebsite\Excel\Excel;
use Betta\Models\Territory;
use Betta\Models\ProgramType;
use Illuminate\Support\MessageBag;
use Betta\Services\Generator\Foundation\AbstractReport;

class UtilizationReport extends AbstractReport
{
	
	
  /**
    * Share Errors
    *
    * @var Illuminate\Support\MessageBag
  */
  	protected $errors;
	
	/**
   * Bind the implementation
   * 
   * @var Model
   */
  protected $territory;

  /**
   * Bind implementation
   *
   * @var Model
   */
  	protected $programType;

  /**
   * Bind implementation
   *
   * @var Maatwebsite\Excel\Excel
   */
  	protected $excel;
  
    /**
     * Title of the Report
     *
     * @var string
     */
    protected $title = 'Field Utilization Report';


    /**
     * Value to populate in the Report
     *
     * @var string
     */
    protected $description = 'Display information about Field utilization.';


    /**
     * Include SQL tab
     *
     * @var boolean
     */
    protected $includeSql = true;


   /**
   * Always fetch these relations for the main resource
   * 
   * @var array
   */
  protected $relations = array(
    	//'profiles.primaryFieldPrograms',
    	//'children.profiles.primaryFieldPrograms',
    	//'children.children.profiles.primaryFieldPrograms',
    	//'children.children.children.profiles.primaryFieldPrograms',
  );


    /**
     * Create new Instance of Conference List Report

     * @param  Conference Conference $conference
     * @return Void
     */
    public function __construct(Excel $excel, ProgramType $programType, Territory $territory, MessageBag $messageBag)
    {
        $this->excel   = $excel;
    	$this->territory   = $territory;
    	$this->programType = $programType;
    	$this->errors  = $messageBag;
    }
	
	
	/**
   		* Handling should be simple and should return result of processing
   		*
   		* @param  array  $arguments
   		* @return array
   	*/
  	public function handle( $arguments = array() )
  	{
    	# Share the candidates accross, so that we can access them
    	$this->candidates  = $this->getData( $arguments );

    	# Process with Excel
    	return $this->process();
  	}

  	/**
   		* Return errors
   		*
   		* @return MessageBag
   	*/
  	public function getErrors()
  	{
    	return $this->errors
                ->getMessageBag();
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

                    foreach( $this->candidates as $territory )
                    { 
						# We need to select the brands somehow
                    	$excel->sheet('Field Utilization Report', function($sheet)
                            {
                              $sheet->loadView('reports.field.utilization.report')
                                      ->withSheet( $sheet )
                                      ->withTerritories( $this->candidates )
                                      ->withProgramTypes( $this->getProgramTypes() )
                                      ->withArguments( $this->arguments )
                                      ->setAutoFilter()
                                      ->freezeFirstRow()
                                      ->setColumnFormat([]);
                            });
					}
					
					# Include Definitions
                    $excel->sheet('Definitions', function($sheet)
                            {
                              $sheet->loadView( 'reports.field.utilization.definitionsTab' );
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
	private function getData( $arguments, $container = 'mergeData' )
	{
		if ( $mergeData = array_get($arguments, $container) )
		{
			# load the necessary relations
			$mergeData->load( $this->relations );

			# return MergeData
			return $mergeData;
		}

		return $this->loadMergeData( $arguments );
	}


	/**
		* Load the data from the database
		*
		* @param  array $arguments
		* @return Collection
	*/
	protected function loadMergeData( $arguments )
	{		
		return $this->territory
                ->whereId( array_get($arguments, 'topTerritory', 0) )
                ->with( $this->relations )
                ->get();
	}
	
	/**
   * Apply a closure to each relation
   * 
   * @return Array
   */
  protected function getRelations()
  {
    	# limit the results with a nice Closure
    	$closure = function($program){
      	$program->inYear( array_get($this->arguments,' inYear', date('Y')) )
              ->inBrand( array_get($this->arguments,'inBrand', [] ) );
              //->noFake()
              //->noTest();
    };

    return array_fill_keys ($this->relations, $closure);
  }
  
   /**
   * Return all program types
   * 
   * @return 
   */
  protected function getProgramTypes()
  {
    return $this->programType
                ->get();
  }

}
