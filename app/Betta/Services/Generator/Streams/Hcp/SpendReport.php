<?php

namespace Betta\Services\Generator\Streams\Hcp;

use Betta\Models\Profile;

use Maatwebsite\Excel\Excel;
use Betta\Services\Generator\Foundation\AbstractReport;
use Auth;

class SpendReport extends AbstractReport
{

    /**
     * Bind the implementation
     *
     * @var
     */
    protected $excel;


	/**
     * Bind implementation
     * 
     * @var Model
   */
  	protected $profile;

    /**
     * Title of the Report
     *
     * @var string
     */
    protected $title = 'HCP Spend Report';


    /**
     * Value to populate in the Report
     *
     * @var string
     */
    protected $description = 'Display information about HCP spend recorded.';


    /**
     * Include SQL tab
     *
     * @var boolean
     */
    protected $includeSql = true;


    /**
     * Always fetch these relations for the Resource
     *
     * @var array
     */
    protected $relations = [
        
    ];

	
    /**
     * Model Injection
     *
     * @param  Excel   $excel
     * @param  Program $program
     * @return Void
     */
    public function __construct(Excel $excel, Profile $profile)
    {
        $this->excel   = $excel;
		$this->profile = $profile;
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
                    $excel->sheet('Summary', function($sheet)
                            {
                              $sheet->loadView( 'reports.hcp.spend.summary' )
                                    ->with([ 'profiles' => $this->candidates ])
                                    ->freezeFirstRow()
                                    ->setColumnFormat([ 'E' => self::AS_CURRENCY,
                                                        'F' => self::AS_CURRENCY,
                                                      ])
                                    # Make sure to use it last
                                    # This is becuase Excel cannot always figure out we have data. So.
                                    ->setAutoFilter();
                            })
                          ->sheet('State Spend Tracking', function($sheet)
                            {
                              $sheet->loadView( 'reports.hcp.spend.state-spend-tracking' )
                                    ->with([ 'stateSpend' => $this->candidates ])
                                    ->freezeFirstRow()
                                    ->setColumnFormat([ 'F' => self::AS_DATE,
                                                        'G' => self::AS_DATE,
                                                        'J' => self::AS_CURRENCY,
                                                      ])
                                    # Make sure to use it last
                                    # This is becuase Excel cannot always figure out we have data. So.
                                    ->setAutoFilter();
                            });
                    
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
        return $this->profile
                    ->hcps()
                    ->get();
    }
	
	


}
