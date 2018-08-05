<?php

namespace Betta\Services\Generator\Streams\Conference;

use Maatwebsite\Excel\Excel;
use Betta\Models\Conference;
use Betta\Models\ConferenceToField;
use Betta\Models\ConferenceToLiterature;
use Betta\Services\Generator\Foundation\AbstractReport;

class ChaseReport extends AbstractReport
{
    /**
     * Title of the Report
     *
     * @var string
     */
    protected $title = 'Conference Chase Report';

    /**
     * Value to populate in the Report
     *
     * @var string
     */
    protected $description = 'List all Conference information';

    /**
     * Include SQL tab
     *
     * @var boolean
     */
    protected $includeSql = false;

    /**
     * Always fetch these relations for the Resource
     *
     * @var array
     */
    protected $relations = [
        'createdBy',
        'addresses',
    ];

    /**
     * Create new Instance of Conference List Report

     * @param  Conference Conference $conference
     * @return Void
     */
    public function __construct(Excel $excel, Conference $conference, ConferenceToField $boothbadges, ConferenceToLiterature $materials)
    {
        $this->excel = $excel;
        $this->conference = $conference;
        $this->boothbadges = $boothbadges;
        $this->materials = $materials;
    }

    /**
     * Produce the Report
     *
     * @return Object
     */
    protected function process()
    {
        return $this->excel->create($this->getReportName(), function($excel){
            # Set standard properties on the file
            $this->setProperties($excel);

            # Produce the tab
            # @todo exctract tabs into their own classes
            $excel->sheet('Summary', function ($sheet) {
                $sheet->loadView('reports.conference.chase.summary')
                      ->with('conferences',  $this->candidates )
                      ->with('boothbadges',  $this->boothbadges->where('badge_status', 1)->count() )
                      ->with('materials',  $this->materials->where('material_status', 0)->count() )
                      ->setAutoFilter()
                      ->freezeFirstRow()
                      ->setColumnFormat( $this->getFormats() );
            });

            $excel->sheet('Unapproved', function ($sheet) {
                $sheet->loadView('reports.conference.chase.unapproved')
                      ->with('conferences',  $this->candidates )
                      ->with('unapproveds', $this->conference->Unclaimed()->get())
                      ->setAutoFilter()
                      ->freezeFirstRow()
                      ->setColumnFormat( $this->getFormats() );
            });
            $excel->sheet('Unconfirmed', function ($sheet) {
                $sheet->loadView('reports.conference.chase.unconfirmed')
                      ->with('conferences',  $this->candidates )
                      ->with('unclaimeds', $this->conference->Unconfirmed()->get())
                      ->setAutoFilter()
                      ->freezeFirstRow()
                      ->setColumnFormat( $this->getFormats() );
            });
            $excel->sheet('Submitted', function ($sheet) {
                $sheet->loadView('reports.conference.chase.submitted')
                      ->with('conferences',  $this->candidates )
                      ->with('unclaimeds', $this->conference->Submitted()->get())
                      ->setAutoFilter()
                      ->freezeFirstRow()
                      ->setColumnFormat( $this->getFormats() );
            });
            $excel->sheet('Cancelled', function ($sheet) {
                $sheet->loadView('reports.conference.chase.cancelled')
                      ->with('conferences',  $this->candidates )
                      ->with('unclaimeds', $this->conference->Cancelled()->get())
                      ->setAutoFilter()
                      ->freezeFirstRow()
                      ->setColumnFormat( $this->getFormats() );
            });

            # Set the includeSql = true to have SQL Printout tab
            # includeSql should NEVER be true for production reports
            $this->includeSqlTab($excel);
            # Make the first sheet active
            $excel->setActiveSheetIndex(0);
            # Add specific styling to the first Tab
            $excel->getActiveSheet()->getStyle('A2:AZ1000')->getAlignment()->setWrapText(true);

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
        return $this->conference->with($this->relations)->latest()->get();
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
            'B' => self::AS_DATE,
        ];
    }
}
