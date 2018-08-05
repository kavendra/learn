<?php

namespace Betta\Services\Generator\Streams\Field;

use Carbon\Carbon;
use Maatwebsite\Excel\Excel;
use Betta\Models\Alignment;
use Illuminate\Support\MessageBag;
use Betta\Services\Generator\Foundation\AbstractReport;

class AlignmentReport extends AbstractReport
{
	
	
  /**
    * Share Errors
    *
    * @var Illuminate\Support\MessageBag
  */
  	protected $errors;

  /**
   * Bind implementation
   *
   * @var Model
   */
  	protected $alignment;

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
    protected $title = 'Field Alignment Report';


    /**
     * Value to populate in the Report
     *
     * @var string
     */
    protected $description = 'Display information about Field alignment.';


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
    	'brands',
    	'territories.parent',
    	'territories.profiles.repprofile',
  );


    /**
     * Create new Instance of Conference List Report

     * @param  Conference Conference $conference
     * @return Void
     */
    public function __construct(Excel $excel, Alignment $alignment, MessageBag $messageBag)
    {
        $this->excel   = $excel;
    	$this->alignment = $alignment;
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

                    foreach( $this->candidates as $alignment )
                    {
						# Many Alignments
                    	$excel->sheet(preg_replace('/[^\p{L}\s]/u', '', $alignment->label), function($sheet)  use ($alignment)
                            {
                              $sheet->loadView('reports.field.alignment.report' )
                                      ->withAlignment($alignment)
                                      ->withAt( array_get($this->arguments, 'at') )
                                      ->setAutoFilter()
                                      ->freezeFirstRow()
                                      ->setColumnFormat([]);
                            });
					}

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
		return $this->alignment
                //->anyReport( $arguments )
                ->with( $this->relations )
                ->get();
	}

}
