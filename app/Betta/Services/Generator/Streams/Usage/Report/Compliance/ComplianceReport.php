<?php

namespace Betta\Services\Generator\Streams\Usage\Report\Compliance;

use Carbon\Carbon;
use Betta\Models\Program;
use Maatwebsite\Excel\Excel;
use Betta\Models\ProgramType;
use Betta\Models\Registration;
use Betta\Models\ProgramStatus;
use Betta\Models\ProgramPresentation;
use Betta\Models\RegistrationStatus;
use Betta\Services\Generator\Foundation\AbstractReport;

class ComplianceReport extends AbstractReport
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
     * @var Betta\Models\Registration
     */
    protected $registration;

    /**
     * Bind the implementation
     *
     * @var Betta\Models\ProgramPresention
     */
    protected $programPresentation;

    /**
     * Title of the Report
     *
     * @var string
     */
    protected $title = 'Compliance Report';

    /**
     * Value to populate in the Report
     *
     * @var string
     */
    protected $description = 'Display information about Compliance';

    /**
     * Include SQL tab
     *
     * @var boolean
     */
    protected $includeSql = false;

    /**
     * Format the Report
     *
     * @see Betta\Services\Generator\Interfaces\ExcelFormatsInterface
     * @var Array
     */
    protected $formats = [
        'default' => [],
        'summary_hcp_attend' => [
            'B' => self::AS_DATE,
        ],
        'attend_more' => [
            'B' => self::AS_DATE,
        ],
        'offsite_office_staff'  => [
            'B' => self::AS_DATE,
        ],
        'one_attendee' => [
            'B' => self::AS_DATE,
        ],
        'catering'  => [
            'D' => self::AS_DATE,
            'E' => self::AS_TIME,
            'S' => self::AS_ZIP_CODE,
            'U' => self::AS_CURRENCY,
            'W' => self::AS_CURRENCY,
        ],
        'fnb'   => [
            'D' => self::AS_DATE,
            'E' => self::AS_TIME,
            'S' => self::AS_ZIP_CODE,
            'U' => self::AS_CURRENCY,
            'W' => self::AS_CURRENCY,
            'X' => self::AS_CURRENCY,
            'Y' => self::AS_CURRENCY,
            'Z' => self::AS_CURRENCY,
        ],
        'rental'  => [
            'D' => self::AS_DATE,
            'E' => self::AS_TIME,
            'S' => self::AS_ZIP_CODE,
            'U:Y' => self::AS_CURRENCY,
        ],
        'prescribers_attendees' => [
            'D' => self::AS_DATE,
            'E' => self::AS_TIME,
            'V:Y' => self::AS_CURRENCY,
        ],
        'closeout' => [
            'E' => self::AS_DATE,
            'F' => self::AS_TIME,
        ],
        'multiple_reps' => [
            'B' => self::AS_DATE,
        ],
    ];

    /**
     * Always fetch these relations for the Resource
     *
     * @var array
     */
    protected $relations = [
        'av',
        'pms',
        'costs',
        'fields',
        'brands.programTypes',
        'addresses',
        'budgetJars',
        'programStatus',
        'audienceTypes',
        'presentations',
        'confirmCaterers',
        'programCaterers',
        'programLocations',
        'costs.notes',
        'primaryConfirmedLocations',
        'programSpeakers.profile',
        'registrations.profile',
        'registrations.hcpProfile',
        'registrations.registrationStatus',
        'registrations.program.brands',
        'registrations.program.fields',
        'registrations.program.presentations',
        'registrations.program.programStatus',
    ];

    /**
     * List Completed Statuses
     *
     * @var Array
     */
    protected $completedStatuses = [
        ProgramStatus::COMPLETED
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
        Registration $registration,
        ProgramPresentation $programPresentation ){
        $this->excel   = $excel;
        $this->program = $program;
        $this->registration = $registration;
        $this->programPresentation = $programPresentation;
    }

    /**
     * Produce the Report
     *
     * @return Excel
     */
    protected function process()
    {
        return $this->excel->create($this->getReportName(), function($excel){
            # Set standard properties on the file
            $this->setProperties($excel);

            $excel->sheet('Definitions', function ($sheet) {
                $sheet->loadView('reports.usage.compliance.summary')
                      ->freezeFirstRow()
                      ->setAutoFilter();
            });

            $excel->sheet('Summary HCP attend > 1', function ($sheet) {
                $sheet->setColumnFormat( $this->getFormats('summary_hcp_attend') )
                      ->fromArray( $this->summaryHcp(), null, 'A1', false, false )
                      ->setAutoFilter()
                      ->freezeFirstRow();
            });

            $excel->sheet('Attend more > 1', function ($sheet) {
                $sheet->setColumnFormat( $this->getFormats('attend_more') )
                      ->fromArray( $this->attendMore(), null, 'A1', false, false )
                      ->setAutoFilter()
                      ->freezeFirstRow();
            });

            $excel->sheet('Office Staff offsite', function ($sheet) {
                $sheet->setColumnFormat( $this->getFormats('offsite_office_staff') )
                      ->fromArray( $this->officeStaff(), null, 'A1', false, false )
                      ->setAutoFilter()
                      ->freezeFirstRow();
            });

            $excel->sheet('1 Attendee', function ($sheet) {
                $sheet->setColumnFormat( $this->getFormats('one_attendee') )
                      ->fromArray( $this->oneAttendee(), null, 'A1', false, false )
                      ->setAutoFilter()
                      ->freezeFirstRow();
            });

            $excel->sheet('Room Rental >$750', function ($sheet) {
                $sheet->setColumnFormat( $this->getFormats('rental') )
                     ->fromArray($this->roomRental(), null, 'A1', false, false)
                     ->setAutoFilter()
                     ->freezeFirstRow();
            });

            $excel->sheet('Catering >$50.01', function ($sheet) {
                $sheet->setColumnFormat( $this->getFormats('catering') )
                      ->fromArray( $this->catering(), null, 'A1', false, false)
                      ->setAutoFilter()
                      ->freezeFirstRow();
            });

            $excel->sheet('F&B >$125', function ($sheet) {
                $sheet->setColumnFormat( $this->getFormats('fnb') )
                      ->fromArray( $this->fnb(), null, 'A1', false, false)
                      ->setAutoFilter()
                      ->freezeFirstRow();
            });

            $excel->sheet('< 1 prescribers attendees', function ($sheet) {
                $sheet->setColumnFormat( $this->getFormats('prescribers_attendees') )
                      ->fromArray( $this->prescribersAttendees(), null, 'A1', false, false)
                      ->setAutoFilter()
                      ->freezeFirstRow();
            });

            $excel->sheet('Program Closeout', function ($sheet) {
                $sheet->setColumnFormat($this->getFormats('closeout'))
                      ->fromArray($this->programCloseout($this->arguments), null, 'A1', false, false)
                      ->setAutoFilter()
                      ->freezeFirstRow();
            });

            $excel->sheet('Multiple Reps (1|8)', function ($sheet) {
                $sheet->setColumnFormat($this->getFormats('multiple_reps'))
                      ->fromArray($this->multipleReps($this->arguments), null, 'A1', false, false)
                      ->setAutoFilter()
                      ->freezeFirstRow();
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
        return $this->program->anyReport($this->arguments)->with($this->relations)->noTest()->get();
    }

    /**
     * Return merge data for the report
     *
     * @param  array $arguments
     * @return Array
     */
    protected function summaryHcp()
    {
       return $this->candidates->pluck('registrations')->collapse()->filter(function($reg){
            return $reg->is_hcp  AND ($reg->is_onsite OR $reg->is_attended);;
        })
        ->map(function ($registration) {
            $registration['attendee_program_presentation'] = 'no_presentations';
            if($pre_id = data_get($registration->program,'primary_presentation.id'))
                $registration['attendee_program_presentation'] = $registration->profile_id.'_'.$pre_id;
            return $registration;
        })
        ->groupBy('attendee_program_presentation')->forget('no_presentations')->filter(function($regs){
            $count = $regs->count();
            if($count>1){
                $regs->map(function($r) use ($count){
                    return $r['program_count'] = $count;
                } );
             return true;
            }
            return false;
        })
        ->transform(function($registrations){
             return (new Handlers\SummaryHcpRow($registrations))->fill();
         })
        ->prepend(Handlers\SummaryHcpRow::headers())
        ->toArray();
    }

    /**
     * Return merge data for the report
     *
     * @param  array $arguments
     * @return Array
     */
    protected function attendMore()
    {
        return $this->candidates->pluck('registrations')->collapse()->filter(function($reg){
            return $reg->is_hcp  AND ($reg->is_onsite OR $reg->is_attended);
        })
        ->map(function ($registration) {
            $registration['attendee_program_presentation'] = 'no_presentations';
            if($pre_id = data_get($registration->program,'primary_presentation.id'))
                $registration['attendee_program_presentation'] = $registration->profile_id.'_'.$pre_id;
            return $registration;
        })
        ->groupBy('attendee_program_presentation')->forget('no_presentations')->filter(function($regs){
             return $regs->count()>1;
        })
        ->collapse()
        ->transform(function($registration){
             return (new Handlers\AttendMoreRow($registration))->fill();
         })
        ->prepend(Handlers\AttendMoreRow::headers())
        ->toArray();
    }

    /**
     * Report program registrations for
     * - any offsite program that
     * - does not have a single prescriber in attendance (defined as any HCP with a degree of [.. your list ])
     *
     * @return Array
     */
    protected function officeStaff()
    {
        return $this->candidates
                    ->where('is_offsite', true)
                    ->reject(function($program){
                        return $program->hcp_registrations->where('attended', true)->filter(function($registration){
                            return $this->isPrescriber($registration);
                        })->isNotEmpty();
                    })
                    ->pluck('registrations')
                    ->collapse()
                    ->where('is_hcp', true)
                    ->where('attended', true)
                    ->reject(function($registration){
                        return empty($registration->profile);
                    })
                    ->transform(function($registration) {
                        return (new Handlers\OfficeStaffRow($registration))->fill();
                    })
                    ->prepend(Handlers\OfficeStaffRow::headers())
                    ->toArray();
    }

    /**
     * True if the Prozio-mapped degree is matches to a list
     *
     * @param  Registration $registration
     * @return boolean
     */
    protected function isPrescriber(Registration $registration)
    {
        $list = [
            'MD',
            'DO',
            'DPM',
            'PODIATRIST',
            'PA',
            'PA-C',
            'PHYSICIAN ASSISTANT',
            'PHYSICIAN ASSISTANTS',
            'CRNP',
            'FNP',
            'FAMILY NURSE PRACTITIONER',
            'NP',
            'NURSE PRACTITIONER',
            'NURSE PRACTITIONERS',
            'RNP',
        ];
        # make sure we are comparing orange to oranges
        return in_array(strtoupper($registration->porzio_degree), $list);
    }

     /**
     * Return merge data for the report
     *
     * @param  array $arguments
     * @return Array
     */
    protected function oneAttendee()
    {


        return $this->candidates
                    ->where('is_closed_out', true)
                    ->reject(function($program){
                        $excludeTypes = [
                            ProgramType::LIVE_TRAINING,
                            ProgramType::TAE,
                            ProgramType::AAE,
                            ProgramType::PRODUCT_THEATER_BREAKFAST,
                            ProgramType::PRODUCT_THEATER_LUNCH,
                            ProgramType::PRODUCT_THEATER_DINNER,
                            ProgramType::AUDIO,
                        ];
                        # make sure we are comparing orange to oranges
                        return in_array($program->program_type_id, $excludeTypes);
                    })
                    ->filter(function($program){
                        return $program->hcp_registrations->where('attended', true)->count() == 1;
                    })
                    ->pluck('registrations')
                    ->collapse()
                    ->transform(function($registration){
                        return (new Handlers\OneAttendeeRow($registration))->fill();
                    })
                    ->prepend(Handlers\OneAttendeeRow::headers())
                    ->toArray();
    }

    /**
     * Return merge data for the report
     *
     * @param  array $arguments
     * @return Array
     */
    protected function roomRental()
    {
        $excludeTypes = [
            ProgramType::LIVE_TRAINING,
        ];

        return $this->candidates->filter(function($program) use($excludeTypes){
            return $program->is_reconciled
               and $program->primaryConfirmedLocations->isNotEmpty()
               and $program->room_rental_costs->sum('real') > 750
               and $program->costs->where('cost_item_id', 22)->count() > 0
               and !in_array($program->program_type_id, $excludeTypes);
        })
        ->transform(function($program){
            return (new Handlers\RoomRentalRow($program))->fill();
        })
        ->prepend(Handlers\RoomRentalRow::headers())
        ->toArray();
    }

    /**
     * Return merge data for the report
     *
     * @param  array $arguments
     * @return Array
     */
    protected function catering()
    {
        $excludeTypes = [
            ProgramType::LIVE_TRAINING,
            ProgramType::IMPACT_PATIENT,
            ProgramType::TAE
        ];

        return $this->candidates->where('fb_per_person', '>', 50.01)->filter(function($program) use($excludeTypes){
                return $program->is_reconciled
                    and $program->confirmCaterers->isNotEmpty()
                    and $program->costs->where('cost_item_id', 22)->count() > 0
                    and !in_array($program->program_type_id, $excludeTypes);
        })
        ->transform(function($program){
            return (new Handlers\CateringRow($program))->fill();
        })
        ->prepend(Handlers\CateringRow::headers())
        ->toArray();

    }

    /**
     * Return merge data for the report
     *
     * @param  array $arguments
     * @return Array
     */
    protected function fnb()
    {
        return $this->candidates->filter(function($program) {
                                    $location = $program->primaryConfirmedLocations->first();
                                    $fbMaxLimit = data_get($location, 'fb_max_per_person');
                                    $fbCondition = $fbMaxLimit ? ($program->fb_per_person > $fbMaxLimit) : ($program->fb_per_person>=125.01);
                                    return ($program->is_dinner || $program->is_product_theater_dinner)
                                            && $fbCondition
                                            && $program->is_reconciled;
                                })
                                ->transform(function($program){
                                    return (new Handlers\FnbRow($program))->fill();
                                })
                                ->prepend(Handlers\FnbRow::headers())
                                ->toArray();
    }

    /**
     * Return merge data for the report
     *
     * @param  array $arguments
     * @return Array
     */
    protected function prescribersAttendees()
    {
        $excludeTypes = [
            ProgramType::LIVE_TRAINING,
            ProgramType::IMPACT_PATIENT,
            ProgramType::IMPACT_DINNER,
            ProgramType::IMPACT_LUNCH,
            ProgramType::TAE,
            ProgramType::PRODUCT_THEATER_BREAKFAST,
            ProgramType::PRODUCT_THEATER_LUNCH,
            ProgramType::PRODUCT_THEATER_DINNER,
        ];

        return $this->candidates->filter(function($program) use($excludeTypes){
            return $program->is_closed_out
                and !in_array($program->program_type_id, $excludeTypes);
        })
        ->filter(function($program){
            return $program->hcp_registrations->filter(function($registration){
                return  in_array($registration->porzio_degree, ['MD', 'DO', 'NP', 'PA', 'DPM'] );
            })->count() < 1;
        })
        ->transform(function($program){
            return (new Handlers\PrescribersAttendeesRow($program))->fill();
        })
        ->prepend(Handlers\PrescribersAttendeesRow::headers())
        ->toArray();

    }

    /**
     * Return merge data for the report
     *
     * @param  array $arguments
     * @return Array
     */
    protected function programCloseout()
    {
        return $this->candidates->filter(function($program){
            return $program->program_status_id == ProgramStatus::COMPLETED
                    AND $program->start_date < Carbon::now()->subDays(15);
        })
        ->transform(function($program){
            return (new Handlers\ProgramCloseoutComplianceRow($program))->fill();
        })
        ->prepend(Handlers\ProgramCloseoutComplianceRow::headers())
        ->toArray();

    }

/**
     * Collect data for programs with multiple reps where the ratio is less then 1:8
     *
     * @param  array $arguments
     * @return Array
     */
    protected function multipleReps()
    {
        return $this->candidates->filter(function($program){
            # Only select programs with multiple Field reps and Speaker Programs
            return $program->is_speaker_program_type AND $program->field_registrations->where('attended', true)->count() > 1;
        })
        ->filter(function($program) {
            # Count HCPs
            $hcpCount = $program->hcp_registrations->where('attended', true)->count();
            # Count Fields
            $fieldCount = $program->field_registrations->where('attended', true)->count();
            # set ratio
            $ratio = 8;
            # Resolve answer
            return ($hcpCount / $ratio) < $fieldCount;
        })
        ->transform(function($program){
            return (new Handlers\MultipleRepsRow($program))->fill();
        })
        ->prepend(Handlers\MultipleRepsRow::headers())
        ->toArray();
    }

    /**
     * Match columns to formats
     *
     * @return array
     */
    protected function getFormats($tab = 'default')
    {
        return $this->formats[$tab];
    }
}
