<?php

namespace Betta\Services\Generator\Streams\Programs\Report\Porzio;

use Carbon\Carbon;
use Betta\Models\Program;
use Maatwebsite\Excel\Excel;
use Betta\Services\Generator\Foundation\AbstractReport;

class PorzioReport extends AbstractReport
{
    /**
     * Bind the implementation
     *
     * @var Model
     */
    protected $program;

    /**
     * Title of the Report
     *
     * @var string
     */
    protected $title = 'Pre-Porzio Report';

    /**
     * Value to populate in the Report
     *
     * @var string
     */
    protected $description = 'Collect ACA-compliance records';

    /**
     * Include SQL tab
     *
     * @var boolean
     */
    protected $includeSql = true;

    /**
     * Model Injection
     *
     * @param  Excel   $excel
     * @param  Program $program
     * @return Void
     */
    public function __construct(Excel $excel, Program $program)
    {
        # implementations
        $this->excel   = $excel;
        $this->program = $program;
    }

    /**
     * Produce the Fil
e     *
     * @param  array $reportTabs
     * @return File
     */
    protected function process()
    {
        # make new template
        return $this->excel->create( $this->getReportName(), function($excel){
            $this->setProperties($excel);
            #method could be repeated
            $excel->sheet('Program List Report', function($sheet){
                $sheet->fromArray( $this->getCandidates() );
            });
            # Attch the SQL printout
            $this->includeSqlTab($excel);
            # Make the first sheet active
            $excel->setActiveSheetIndex(0);
        })->store(data_get($this->arguments, 'format', 'xlsx'), $this->getReportPath(), true);
    }

    /**
     * Return the name of Report
     *
     * @return String
     */
    protected function getReportName()
    {
        # Parse Start date
        $from = Carbon::parse(data_get($this->arguments, 'from'))->format('Ymd');
        # Parse End date
        $to = Carbon::parse(data_get($this->arguments, 'to'))->format('Ymd');
        # Parse the template
        return "FLS_HORIZON_{$from}_{$to}";
    }

    /**
     * Load the data for the report
     *
     * @param  array $arguments
     * @return array
     */
    protected function loadMergeData($arguments)
    {
        # create empty collection
        $data = collect([]);
        # merge data into the collection and cast to Array
        return $data->merge($this->registrations())
                    ->merge($this->speakerCosts())
                    ->toArray();
    }

    /**
     * Resolve the registrations
     *
     * @return Illuminate\Support\Collection
     */
    protected function registrations()
    {
        $relations = [
            'costs',
            'fields',
            'brands',
            'registrations.hcpProfile',
            'programLocations',
        ];

        # Scope the Program with Registrations
        return $this->program
                    ->inBrand($this->arguments['inBrand'])
                    ->betweenDates($this->getFrom(), $this->getTo())
                    ->hasFbCosts()
                    ->with($relations)
                    ->with(['registrations'=>function($registration){
                        # Scope the Registrations
                        return $registration->anyAttended()->consumedMeal();
                    }])
                    ->get()
                    ->transform(function($program){
                        # get ONLY HCP + Speaker registrations
                        $registrations = $program->registrations->filter(function($registration){
                            return $registration->is_hcp
                                or $registration->is_speaker;
                        });
                        # transform each HCP and Speaker registration using eager loaded Program
                        return $registrations->map(function($registration) use ($program){
                            return (new Handlers\RegistrationRowHandler($registration, $program))->fill();
                        });
                    })
                    ->collapse();
    }

    /**
     * Load the Speaker Costs
     *
     * @return Illuminate\Support\Collection
     */
    protected function speakerCosts()
    {
        $relations = [
            'brands',
            'fields',
            'programLocations',
            'costs.payee.profile.addresses',
            'costs.payee.profile.hcpProfile',
        ];

        # Scope the Program with Registrations
        return $this->program
                    ->inBrand($this->arguments['inBrand'])
                    ->betweenDates($this->getFrom(), $this->getTo())
                    ->with($relations)
                    ->with(['costs'=>function($cost){
                        return $cost->hasPayee()->hasReportableValue('actual');
                    }])
                    ->get()
                    ->transform(function($program){
                        # transform each Cost using eager loaded Program
                        $program->costs->transform(function($cost) use ($program){
                            return (new Handlers\CostRowHandler($cost, $program))->fill();
                        });
                        # replace original Program
                        return $program;
                    })
                    # get transformed Registrations from each Program
                    ->pluck('costs')
                    ->collapse();
    }

    /**
     * Resolve the From
     *
     * @return Carbon
     */
    protected function getFrom()
    {
        return Carbon::parse($this->arguments['from'])->startOfDay();
    }

    /**
     * Resolve the To into Carbon
     *
     * @return Carbon
     */
    protected function getTo()
    {
        return Carbon::parse($this->arguments['to'])->endOfDay();
    }
}
