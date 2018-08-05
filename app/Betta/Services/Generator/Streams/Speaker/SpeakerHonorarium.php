<?php

namespace Betta\Services\Generator\Streams\Speaker;

use Carbon\Carbon;
use Maatwebsite\Excel\Excel;
use Betta\Models\Profile;
use Betta\Models\ProfileRateCard;
use Betta\Models\Nomination;
use Betta\Services\Generator\Foundation\AbstractReport;

class SpeakerHonorarium extends AbstractReport
{


   /**
   * Bind the implementation
   *
   * @var Model
   */
  protected $profile;
  protected $_speakers;
  protected $profileratecard;
  protected $nomination;

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
    protected $title = 'Speaker Honorarium Report';


    /**
     * Value to populate in the Report
     *
     * @var string
     */
    protected $description = 'Collect information in Speaker Honorarium Report';


    /**
     * Include SQL tab
     *
     * @var boolean
     */
    protected $includeSql = false;


    /**
   * Always fetch these relations for the Main Resource
   *
   * @var array
   */
  protected $relations = array(
					'profile.rateCards',
					'profile.speakerProfile',
					'brand',
					'rates'
				);


    /**
     * Create new Instance of Conference List Report

     * @param  Conference Conference $conference
     * @return Void
     */
    public function __construct(Excel $excel, Profile $profile, Carbon $carbon, ProfileRateCard $profileratecard, Nomination $nomination)
    {
        $this->excel = $excel;
        $this->profile = $profile;
    		$this->carbon         = $carbon;
    		$this->profileratecard  = $profileratecard;
    		$this->nomination		= $nomination;
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
                    $excel->sheet('FMV Speakers', function($sheet)
                            {
                              $sheet->loadView('reports.speaker.speaker-honorarium.report')
                                    ->withFromDate( $this->getFromDate() )
                                    ->withSpeakers( $this->getFmvSpeakers() )
                                    ->freezeFirstRow()
                                    ->setColumnFormat( $this->getFormats() )
                                    # Make sure to use it last
                                    # This is becuase Excel cannot always figure out we have data. So.
                                    ->setAutoFilter();
                            });

					$excel->sheet('Program Type', function($sheet)
                            {
                              $sheet->loadView('reports.speaker.speaker-honorarium.programtype')
                                    ->withFromDate( $this->getFromDate() )
                                    ->withProgramTypeSpeakers( $this->getProgramTypeSpeakers() )
                                    ->freezeFirstRow()
                                    ->setColumnFormat( $this->getSpecificFormats() )
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
                ->store( 'xlsx', $this->getReportPath(), true);

    }


    /**
     * Return merge data for the report
     *
     * @param  array $arguments
     * @return Array
     */
    protected function loadMergeData($arguments)
    {
    	return $this->_speakers = $this->nomination
								//->whereHas('profile.speakerProfile')
								->whereHas('profile.rateCards')
								->valid()
								->inBrand(array_get($arguments, 'inBrand' ) )
								->betweenDates(array_get($arguments, 'from'), array_get($arguments, 'to'))
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
			'M:AB' => static::AS_CURRENCY,
		];
  	}

	public function getSpecificFormats()
    {
		return [
			'M:P' => static::AS_CURRENCY,
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

  /**
   * Resolve the getFmvSpeakers
   *
   * @return Carbon
   */
	private function getFmvSpeakers()
	{
		return $this->_speakers->filter(function($item) {
      return $item->has_fmv_rates;
		});
	}

	/**
   * Resolve the getProgramTypeSpeakers
   *
   * @return Carbon
   */
	private function getProgramTypeSpeakers()
	{
		return $this->_speakers->filter(function($item) {
      return $item->has_specific_rates;
		});
	}

}
