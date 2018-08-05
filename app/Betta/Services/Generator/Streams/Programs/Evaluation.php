<?php

namespace Betta\Services\Generator\Streams\Programs;

use Carbon\Carbon;
use Maatwebsite\Excel\Excel;
use Betta\Models\Program;
use Betta\Models\ProgramType;
use Betta\Models\SurveyQuestion;
use Illuminate\Support\MessageBag;
use Betta\Services\Generator\Foundation\AbstractReport;

class Evaluation extends AbstractReport
{
	
  /**
   * Bind the implementation
   * 
   * @var Model
   */
  protected $program;

  /**
   * Bind the implementation
   *
   * @var Model
   */
  protected $survyeQuestion;

  
  protected $speakerQuestionId = [14, 15, 16, 17, 18, 20, 21];


  /**
   * Share Errors
   * 
   * @var Collection
   */
  protected $errors;


  /**
   * Title of the Report
   * 
   * @var string
   */
  protected $title = 'Representative Program Survey';
  

    /**
     * Value to populate in the Report
     *
     * @var string
     */
    protected $description = 'Display simple program data speaker evaluation results as provided by the field during program close out optional survey.';
	
	protected $excludeProgramTraintype = [
        ProgramType::TAE
    ];

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
      'brands',
      'presentations',
      'programSpeakers.profile',
      'surveys.details',
     // 'ams',
    );


    /**
	   * @param Excel           $excel
	   * @param Program         $program
	   * @param SurveyQuestion  $surveyQuestion
	   * @param MessageBag      $messageBag
	*/
	public function __construct( Excel $excel
								 , Program $program
								 , SurveyQuestion $surveyQuestion
								 , MessageBag $messageBag)
	{
		# implementations
		$this->excel          = $excel;
		$this->program        = $program;
		$this->surveyQuestion = $surveyQuestion;
		$this->errors         = $messageBag;
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
                    $excel->sheet('Survey Evaluation Results', function($sheet)
                            {
                              $sheet->loadView('reports.program.evaluation.report' )
                                    ->withSurveyQuestions( $this->getSurveyQuestions() )
                                    ->withPrograms( $this->candidates )
                                    ->freezeFirstRow()
                                    ->setColumnFormat([ 'B' => static::AS_DATE ])
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
      $inBrand = array_get($arguments, 'inBrand' );
      $inBrand = is_array($inBrand) ? $inBrand : $inBrand->pluck('id');
    	return $this->program
                ->closedOut()
				->byBrand($inBrand)
				->notInType($this->excludeProgramTraintype)
                ->betweenDates(array_get($arguments, 'from'), array_get($arguments, 'to'))
                ->with( $this->relations )
                ->orderBy('start_date')
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
		  'B' => static::AS_DATE,
		  'H' => static::AS_NICE_INTEGER,
		  'P:W' => static::AS_CURRENCY,
		];
  	}
  
     
  /**
   * Resolve additional Headers from SurveyQuestions
   * 
   * @return Collection
   */
  protected function getSurveyQuestions()
  {
    return $this->surveyQuestion
                ->byKey($this->speakerQuestionId)
                ->orderBy('id')
                ->get();
  }
  
}
