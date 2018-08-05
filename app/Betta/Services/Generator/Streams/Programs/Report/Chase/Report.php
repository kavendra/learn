<?php

namespace Betta\Services\Generator\Streams\Programs\Report\Chase;

use Auth;
use Betta\Models\Program;
use Maatwebsite\Excel\Excel;
use Betta\Models\ProgramType;
use Betta\Models\ProgramStatus;
use Betta\Models\ProgramSpeaker;
use Betta\Services\Generator\Foundation\AbstractReport;
use Betta\Composers\Chase\Program\Unclaimed\UnclaimedBuilder;
use App\Http\Controllers\Program\Scopes\AbstractScopesController;

class Report extends AbstractReport
{
    use UnclaimedBuilder;

    /**
     * Bind the implementation
     *
     * @var Maatwebsite\Excel\Excel
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
     * @var Betta\Models\ProgramSpeaker
     */
    protected $programSpeaker;

    /**
     * Title of the Report
     *
     * @var string
     */
    protected $title = 'Chase Report';

    /**
     * Value to populate in the Report
     *
     * @var string
     */
    protected $description = 'List all Chase Report';

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
        'brands',
        'fields',
        'programStatus',
    ];

    /**
     * Variables to cache the tabs
     *
     * @var Illuminate\Support\Collection
     */
    protected $_avPrograms;
    protected $_exceptions;
    protected $_reminders;
    protected $_invitations;
    protected $_recruit;
    protected $_uncancelled;
    protected $_unapproved;
    protected $_lowregistration;
    protected $_unclaimed;
    protected $_unconfirmed;
    protected $_unconfirmedtravel;
    protected $_unconfirmedlocation;
    protected $_unconfirmedcatering;
    protected $_onhold;

    /**
     * List Negative Statuses
     *
     * @var Array
     */
    protected $excludeStatuses = [
        ProgramStatus::DRAFT,
        ProgramStatus::SUBMITTED,
        ProgramStatus::DENIED,
        ProgramStatus::CANCELLED,
        ProgramStatus::MANAGER_DENIED,
    ];

    /**
     * List Negative Types
     *
     * @var Array
     */
    protected $excludeTypes = [
        ProgramType::PRODUCT_THEATER_BREAKFAST,
        ProgramType::PRODUCT_THEATER_LUNCH,
        ProgramType::PRODUCT_THEATER_DINNER
    ];

    /**
     * List Cancelled Statuses
     *
     * @var Array
     */
	protected $cancelledStatuses = [
        ProgramStatus::CANCELLED
    ];

    /**
     * Formats of the resulting tabs
     *
     * @var array
     */
    protected $formats = [
        'unclaimed' => [
            'B' => self::AS_DATE
        ],
        'unconfirmed' => [
            'B' => self::AS_DATE,
            'G:J' => self::AS_DATE,
        ],
        'travel' => [
            'B' => self::AS_DATE
        ],
        'location' => [
            'B' => self::AS_DATE
        ],
        'catering' => [
            'B' => self::AS_DATE,
            'I:K' => self::AS_DATE_LONG,
        ],
        'invitations' => [
            'B' => self::AS_DATE
        ],
        'av' => [
            'B' => self::AS_DATE
        ],
        'reminders' => [
            'B' => self::AS_DATE
        ],
        'onhold' => [
            'B' => self::AS_DATE
        ],
        'uncancelled' => [
            'B' => self::AS_DATE
        ],
        'unapproved' => [
            'B' => self::AS_DATE
        ],
        'lowregistration' => [
            'B' => self::AS_DATE
        ],
    ];

    /**
     * Create new instance of Report
     *
     * @param  Excel   $excel
     * @param  Program $program
     * @param  ProgramSpeaker $programSpeaker
     * @return Void
     */
    public function __construct(Excel $excel, Program $program, ProgramSpeaker $programSpeaker)
    {
        $this->excel   = $excel;
        $this->program = $program;
        $this->programSpeaker = $programSpeaker;
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
                $sheet->loadView('reports.program.chase.summary')
                      ->with('programs',  $this->candidates )
                      ->with('unclaimed', $this->unclaimedList())
                      ->with('unconfirmed', $this->unconfirmedList())
                      ->with('unconfirmedTravel',  $this->unconfirmedTravel() )
                      ->with('unconfirmedLocation',  $this->unconfirmedLocation() )
                      ->with('unconfirmedCatering',  $this->unconfirmedCatering() )
                      ->with('onhold',  $this->onholdProgram() )
                      ->with('lowregistration',  $this->getLowRegistration() )
                      ->with('unapproved',  $this->getUnapproved() )
                      ->with('uncancelled',  $this->getCountUncancelled() )
                        # Hide recruitment tab until furtehr notice
                        #->with('recruit',  $this->getRecruit() )
                      ->with('invitations',  $this->getInvitations() )
                      ->with('reminders',  $this->getReminders() )
                        # Hide exceptions until implemented
                        # ->with('exceptions',  $this->getExceptions() )
                      ->with('avprograms',  $this->avPrograms() )
                      ->freezeFirstRow()
                      ->setColumnFormat( $this->getFormats() )
                      ->freezeFirstRow()
                      ->setAutoFilter();
                    });


            $excel->sheet('Unclaimed', function ($sheet) {
                $sheet->loadView('reports.program.chase.unclaimed')
                      ->with('programs',  $this->candidates )
                      ->with('unclaimed', $this->unclaimedList())
                      ->setAutoFilter()
                      ->freezeFirstRow()
                      ->setColumnFormat( $this->getFormats('unclaimed') );
            });

            $excel->sheet('Unconfirmed', function ($sheet) {
                $sheet->setColumnFormat( $this->getFormats('unconfirmed') )
                      ->fromArray( $this->unconfirmedList()->toArray() )
                      ->setAutoFilter()
                      ->freezeFirstRow();
            });

            $excel->sheet('Travel', function ($sheet) {
                $sheet->loadView('reports.program.chase.travel')
                      ->with('programs',  $this->candidates )
                      ->with('unconfirmedTravel',  $this->unconfirmedTravel() )
                      ->setAutoFilter()
                      ->freezeFirstRow()
                      ->setColumnFormat( $this->getFormats('travel') );
            });

            $excel->sheet('Location', function ($sheet) {
                $sheet->loadView('reports.program.chase.location')
                    ->with('unconfirmedLocation',  $this->unconfirmedLocation() )
                    ->setAutoFilter()
                    ->freezeFirstRow()
                    ->setColumnFormat( $this->getFormats('location') );
            });

            $excel->sheet('Catering', function ($sheet) {
                $sheet->loadView('reports.program.chase.catering')
                    ->with('unconfirmedCatering',  $this->unconfirmedCatering() )
                    ->setAutoFilter()
                    ->freezeFirstRow()
                    ->setColumnFormat( $this->getFormats('catering') );
            });

            $excel->sheet('Invitations', function ($sheet) {
                $sheet->loadView('reports.program.chase.invitations')
                      ->with('invitations',  $this->getInvitations() )
                      ->setAutoFilter()
                      ->freezeFirstRow()
                      ->setColumnFormat( $this->getFormats('invitations') );
            });

            $excel->sheet('AV', function ($sheet) {
                $sheet->loadView('reports.program.chase.av')
                      ->with('programsav',  $this->avPrograms() )
                      ->setAutoFilter()
                      ->freezeFirstRow()
                      ->setColumnFormat( $this->getFormats('av') );
            });

            $excel->sheet('Reminders', function ($sheet) {
                $sheet->loadView('reports.program.chase.reminders')
                      ->with('reminders',  $this->getReminders() )
                      ->setAutoFilter()
                      ->freezeFirstRow()
                      ->setColumnFormat( $this->getFormats('reminders') );
            });

            $excel->sheet('On Hold', function ($sheet) {
                $sheet->loadView('reports.program.chase.onhold')
                      ->with('onhold',  $this->onholdProgram() )
                      ->setAutoFilter()
                      ->freezeFirstRow()
                      ->setColumnFormat( $this->getFormats('onhold') );
            });

            $excel->sheet('Uncancelled', function ($sheet) {
                $sheet->loadView('reports.program.chase.uncancelled')
                      ->with('programs',  $this->candidates )
                	  ->with('uncancelled',  $this->getUncancelled() )
                      ->setAutoFilter()
                      ->freezeFirstRow()
                      ->setColumnFormat( $this->getFormats('uncancelled') );
            });

            $excel->sheet('unapproved', function ($sheet) {
                $sheet->loadView('reports.program.chase.unapproved')
                      ->with('programs',  $this->getUnapproved() )
                      ->setAutoFilter()
                      ->freezeFirstRow()
                      ->setColumnFormat( $this->getFormats('unapproved') );
            });

            $excel->sheet('Low Registration', function ($sheet) {
                $sheet->loadView('reports.program.chase.lowregistration')
                      ->with('programs',  $this->getLowRegistration() )
                      ->setAutoFilter()
                      ->freezeFirstRow()
                      ->setColumnFormat( $this->getFormats('lowregistration') );
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
        return $this->program
                    ->with($this->relations)
                    ->get();
    }

    /**
     * Resolve User from container
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
     * Load the Programs for the current User
     *
     * @return Collection
     */
    protected function unclaimedList()
    {
        $this->_unclaimed = $this->_unclaimed ? $this->_unclaimed :  $this->getBuilder()->get();

        return $this->_unclaimed;
    }

    /**
     * Resolve Programs that has a speaker but have no confirmed speaker
     *
     * @return Collection
     */
    protected function unconfirmedList()
    {
        return $this->_unconfirmed ?: $this->_unconfirmed = $this->loadUnconfirmedList();
    }

    /**
     * Load Unconfirmed List
     *
     * @return Collection
     */
    protected function loadUnconfirmedList()
    {
        $relations = [
            'pms',
            'fields',
            'programSpeakers.communications',
            'programSpeakers.profile.contracts',
            'programSpeakers.profile.trainings',
        ];

        return $this->program->with($relations)
                    ->notOnHold()
                    ->has('programSpeakers')
                    ->doesntHave('confirmedSpeakers')
                    ->notInStatus($this->excludeStatuses)
                    ->byBrand( $this->getActiveBrands()->pluck('id') )
                    ->orderBy('start_date')
                    ->get()
                    ->transform(function($program){
                        return (new Handlers\UnconfirmedRow($program))->fill();
                    });
    }

    /**
     * Return the Programs without confirmed travel, where travel is requested
     *
     * @return Collection
     */
    protected function unconfirmedTravel()
    {
        return empty($this->_unconfirmedtravel)
             ? $this->_unconfirmedtravel = $this->loadUnconfirmedTravel()
             : $this->_unconfirmedtravel;
    }

    /**
     * Locate Programs without confirmed travel, where travel is requested
     *
     * @return Collection
     */
    protected function loadUnconfirmedTravel()
    {
        $relations = [
            'notes',
            'brand',
            'profile',
            'travels.notes',
            'program.fields',
            'program.brands.programTypes.rules',
        ];

        return $this->programSpeaker
                    ->whereHas('program', function($program){
                        $program->future()
                                ->notOnHold()
                                ->byBrand($this->getActiveBrands()->pluck('id'))
                                ->notInStatus($this->excludeStatuses);
                    })
                    ->noTravelNeeded()
                    ->with($relations)
                    ->get()
                    ->filter(function($speaker){
                        return $speaker->program->businessRule('allow_speaker_travel');
                    })
                    ->where('is_chase_travel_incomplete', true)
                    ->sortBy('program.start_date');
    }

  /**
     * Load the Programs for the current User
     *
     * @return Collection
     */
    protected function unconfirmedLocation()
    {
    $this->_unconfirmedlocation = $this->_unconfirmedlocation ? $this->_unconfirmedlocation : $this->program
        ->notInStatus($this->excludeStatuses)
                ->byBrand( Auth::user()->profile->active_brands->pluck('id') )
                ->has('locations')
                ->notOnHold()
                ->doesntHave('confirmedLocations')->orderBy('start_date')->get();
    return $this->_unconfirmedlocation ;
    }

    /**
     * Get the Unconfirmed Catering list
     *
     * @return Collection
     */
    protected function unconfirmedCatering()
    {
        return empty($this->_unconfirmedcatering)
             ? $this->_unconfirmedcatering = $this->loadUnconfirmedCatering()
             : $this->_unconfirmedcatering;
    }

    /**
     * Resolve Unconfirmed Catering
     *
     * @return Collection
     */
    protected function loadUnconfirmedCatering()
    {
        # Eager load Catering info
        $relations = [
            'pms',
            'vcs',
            'brands',
            'fields',
            'programCaterers.communications',
        ];

        return $this->program
                    ->byBrand($this->getActiveBrands()->pluck('id'))
                    ->notInStatus($this->excludeStatuses)
                    ->notOnHold()
                    ->future()
                    ->where(function($builder){
                        $builder->where(function($program){
                            $program->has('caterers')->doesntHave('confirmedCaterers');
                        })->orWhere(function($program){
                            $program->doesntHave('caterers')->onsite()->doesntNeedCatering();
                        });
                    })
                    ->onsite()
                    ->with( $relations )
                    ->orderBy('start_date')
                    ->get();
    }

  /**
     * Load the Programs for the current User
     *
     * @return Collection
     */
    protected function onholdProgram()
    {
    $this->_onhold = $this->_onhold ? $this->_onhold : $this->program
                ->byBrand( Auth::user()->profile->active_brands->pluck('id') )
        ->has('holds')
        ->where('is_on_hold', 1)
                ->IsActive()->get();
    return $this->_onhold;
    }

    /**
     * Column Formats
     *
     * @see Betta\Services\Generator\Interfaces\ExcelFormatsInterface
     * @return array
    */

    /**
     * List of AV items that need to have confirmed AV
     *
     * @return Collection
     */
    protected function avPrograms()
    {
        return empty($this->_avPrograms) ? $this->_avPrograms = $this->loadAvPrograms() : $this->_avPrograms;
    }

    /**
     * Load the Values for the AV programs
     *
     * @return Collection
     */
    protected function loadAvPrograms()
    {
        return $this->program
                    ->byBrand($this->getActiveBrands()->pluck('id'))
                    ->notInStatus($this->excludeStatuses)
                    ->future()
                    ->has('av')
                    ->doesntHave('confirmAv')
                    ->notOnHold()
                    ->get();
    }

  protected function getExceptions()
    {
    $this->_exceptions = $this->_exceptions ? $this->_exceptions : $this->program
                ->byBrand( Auth::user()->profile->active_brands->pluck('id') )
        ->notInStatus($this->excludeStatuses)
                ->notOnHold()
                ->get();
    return $this->_exceptions;
    }

  protected function getReminders()
    {
    $this->_reminders = $this->_reminders ? $this->_reminders : $this->program
                ->byBrand( Auth::user()->profile->active_brands->pluck('id') )
        				->Confirmed()
                        ->notOnHold()
        				->notInStatus($this->excludeStatuses)
        				->orderBy('start_date')
                ->get();
    return $this->_reminders;
    }

    /**
     * Resolve the Programs that need to have invitation shipped
     *
     * @author ZR | PT
     * @return Collection
     */
    protected function getInvitations()
    {
      $this->_invitations = $this->_invitations ? $this->_invitations : $this->program
                ->byBrand( Auth::user()->profile->active_brands->pluck('id') )
                ->confirmed()
                ->notOnHold()
                ->doesntHave('shipments')
                ->has('invitationQuantity')
                ->orderBy('start_date')
                ->get();

        return $this->_invitations;
    }

  protected function getRecruit()
    {
    $this->_recruit = $this->_recruit ? $this->_recruit : $this->program
                ->byBrand( Auth::user()->profile->active_brands->pluck('id') )
                ->notInStatus($this->excludeStatuses)
                ->notOnHold()
                ->get();
    return $this->_recruit;
    }

  protected function getUncancelled()
    {
    $this->_uncancelled = $this->_uncancelled ? $this->_uncancelled : $this->program
                ->byBrand( Auth::user()->profile->active_brands->pluck('id') )
        ->InStatus($this->cancelledStatuses)
		->where(function($query){
			$query->Has('confirmedSpeakers');
			$query->orWhereHas('programSpeakers', function ($speaker) {
				$speaker->whereHas('travels', function ($travelArrangement) {
					$travelArrangement->where('progression_status_id', 5);
				});

			});
        	$query->orHas('confirmedLocations');
        	$query->orHas('confirmAv');
			$query->orHas('confirmCaterers');
        })


        //->ClosedOut()
        //->has('primaryCancelledSpeakers')
        //->orHas('primaryCancelledLocations')
		//->has('confirmedSpeakers')
        //->orHas('confirmedLocations')
		//->orHas('confirmAv')
        ->notOnHold()
        //->where('is_reconciled', 0)
        ->get();
    return $this->_uncancelled;
    }

    /**
     * Resolve programs that require approval
     *
     * @author ZR | PT
     * @return Collection
     */
    protected function getUnapproved()
    {
        $status = [
          ProgramStatus::SUBMITTED,
          ProgramStatus::PENDING_MANAGER
        ];

        # assign the value
        $this->_unapproved = $this->_unapproved ? $this->_unapproved : $this->program
             ->byBrand( Auth::user()->profile->active_brands->pluck('id') )
             ->inStatus($status)
             ->notOnHold()
             ->orderBy('start_date')
             ->get();

        return $this->_unapproved;
    }


    /**
     * Resolve the Programs with Low registration
     *
     * @author ZR | PT
     * @return Collection (of Programs)
     */
    protected function getLowRegistration()
    {
        return empty($this->_lowregistration)
             ? $this->_lowregistration = $this->loadLowRegistrations()
             : $this->_lowregistration;
    }

    /**
     * Resolve the low registrations
     *
     * @return Collection
     */
    protected function loadLowRegistrations()
    {
        $relations = [
            'pms',
            'registrations',
            'fields.territories',
            'programSpeakers.profile.speakerProfile'
        ];
        # what to exclude
        $except = array_merge($this->excludeTypes, [
            ProgramType::TAE,
            ProgramType::AAE,
            ProgramType::AUDIO,
            ProgramType::IMPACT_LUNCH,
            ProgramType::IMPACT_DINNER,
            ProgramType::IMPACT_BREAKFAST,
        ]);
        # this DOES look like a setting
        $limit = 3;
        # resolve
        return $this->program
                    ->speakerPrograms()
                    ->notInStatus($this->excludeStatuses)
                    ->notByType($except)
                    ->future()
                    ->notOnHold()
                    ->byBrand($this->getActiveBrands()->pluck('id'))
                    ->whereHas('registrations', function($registration){
                        $registration->hcps();
                    }, '<', $limit)
                    ->with($relations)
                    ->orderBy('start_date')
                    ->get();
    }


	protected function getCountUncancelled()
    {
		$count = 0;
        if($this->getUncancelled()) {
			foreach($this->getUncancelled() as $program) {
				if($program->confirmedSpeakers) {
					$count += $program->confirmedSpeakers->count();
				}

				if($program->programSpeakers) {
					foreach($program->programSpeakers as $speaker) {
						if($speaker->travels) {
							foreach($speaker->travels as $travel) {
								if($travel->progression_status_id == 5) {
									$count += 1;
								}
							}
						}
					}
				}

				if($program->confirmedLocations) {
					$count += $program->confirmedLocations->count();
				}

				if($program->confirmAv) {
					$count += 1;
				}

				if($program->confirmCaterers) {
					$count += $program->confirmCaterers->count();
				}
			}
		}
		return $count;
    }


    /**
     * Return formats for the Tabs
     *
     * @param  string $tab
     * @return array
     */
    public function getFormats( $tab = '')
    {
        return data_get($this->formats, $tab, []);
    }
}
