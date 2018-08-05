<?php

namespace Betta\Services\Generator\Streams\Programs\Report\SpeakerProgramTracker;

use Carbon\Carbon;
use Maatwebsite\Excel\Excel;
use Betta\Services\Generator\Foundation\AbstractReport;

class Report extends AbstractReport
{
    use QueryBuilder;

    /**
     * Bind the implementation
     *
     * @var Maatwebsite\Excel\Excel
     */
    protected $excel;

    /**
     * Title of the Report
     *
     * @var string
     */
    protected $title = 'Speaker Program Tracker Report';

    /**
     * Value to populate in the Report
     *
     * @var string
     */
    protected $description = 'List all Speaker Program information';

    /**
     * Include SQL tab
     *
     * @var boolean
     */
    protected $includeSql = false;

    /**
     * Column Formats
     *
     * @see Betta\Services\Generator\Interfaces\ExcelFormatsInterface
     * @var array
     */
    protected $formats = [
        'J' => self::AS_DATE,
        'K' => self::AS_TIME,
        'R' => self::AS_CURRENCY,
    ];

    /**
     * Always fetch these relations for the Resource
     *
     * @var array
     */
    protected $relations = [
        'costs',
        'cancellations',
        'registrations',
        'presentations',
        'presentationTopics',
        'brands.programTypes',
        'closeout.certifications',
        'programSpeakers.profile',
        'programCaterers.documents',
        'programLocations.address',
        'programLocations.documents',
        'fields.territories.parent.primaryProfiles.territories',
    ];

    /**
     * Model Injection
     *
     * @param  Excel   $excel
     * @param  Program $program
     * @return Void
     */
    public function __construct(Excel $excel)
    {
        $this->excel   = $excel;
    }

    /**
     * Create the Structure with Data and
     *
     * @todo exctract tabs into their own classes
     * @return Excel
     */
    protected function process()
    {
        return $this->excel->create($this->getReportName(), function($excel){
            # Set standard properties on the file
            $this->setProperties($excel);
            # Produce the tab
            # @todo exctract tabs into their own classes
            foreach($this->getCandidates() as $year => $yearData ){
                # Sheet for each group(Year)
                $excel->sheet("List ". $year, function ($tab) use($yearData){
                    $tab->setColumnFormat($this->getFormats())
                        ->cells('A:N', function($cells){
                            $cells->setAlignment('left');
                        })
                        ->cells('O:S', function($cells){
                            $cells->setAlignment('center');
                        });
                    $tab->fromArray($yearData)
                        ->setAutoFilter()
                        ->freezeFirstRow();

                });
            }
            # Set the includeSql = true to have SQL Printout tab
            # includeSql should NEVER be true for production reports
            $this->includeSqlTab($excel);
            # Make the first sheet active
            $excel->setActiveSheetIndex(0);
        # Return
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
        # resolve data
        $data = $this->getBuilder($arguments)->with( $this->relations )->get();

        return $this->transform($data);
    }

    /**
     * Excellent for method overloading
     *
     * @param  mixed $data
     * @return mixed
     */
    protected function transform($data)
    {
        return $data->map(function($program){
            return (new Handlers\RowHandler($program))->fill();
        })->groupBy(function($handler) {
            return $handler->year;
        })->sortByDesc(function($group, $year){
            return $year;
        });
    }
}
