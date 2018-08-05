<?php

namespace Betta\Services\Generator\Streams\Conference\Invoice;

use Carbon\Carbon;
use Betta\Models\Brand;
use Betta\Models\Conference;
use Maatwebsite\Excel\Excel;
use Betta\Models\ConferenceStatus;
use Betta\Services\Generator\Foundation\AbstractReport;

class Report extends AbstractReport
{
    /**
     * Bind the implementation
     *
     * @var Model
     */
    protected $conference;
    protected $costcenter;
    protected $conferencemanagementregistrationfee;
    protected $conferencemanagementmaterials;
    protected $conferencemanagementattendeelists;
    protected $conferencefee;
    protected $exhibitorfees;
    protected $candyfees;
    protected $additionalsponsorshipfees;

    /**
     * Title of the Report
     *
     * @var string
     */
    protected $title = 'Conference Costs';

    /**
     * Value to populate in the Report
     *
     * @var string
     */
    protected $description = 'List all Conference Invoice information';

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
        'costs',
        'brands',
        'addresses',
        'conferenceStatus',
        'createdBy.territories.parent.primaryProfiles',
    ];

    /**
     * Column Formats
     *
     * @see Betta\Services\Generator\Interfaces\ExcelFormatsInterface
     * @var array
     */
    protected $formats = [
        'C:D' => self::AS_DATE,
        'L:AA' => self::AS_CURRENCY,
    ];

    /**
     * Create new Instance of Conference List Report

     * @param  Conference Conference $conference
     * @return Void
     */
    public function __construct(Excel $excel, Conference $conference)
    {
        $this->excel = $excel;
        $this->conference = $conference;
    }

    /**
     * Make the report
     *
     * @return object
     */
    protected function process()
    {
        return $this->excel->create($this->getReportName(), function($excel){
            # Set standard properties on the file
            $this->setProperties($excel);
            # Produce the tab
            # @todo exctract tabs into their own classes
            # Produce the tab
            $excel->sheet($this->title, function ($sheet) {
                $sheet->setColumnFormat($this->getFormats())
                      ->rows($this->getConferenceCostDetails())
                      ->setAutoFilter()
                      ->freezeFirstRow();
                $this->setHeaderFooterStyles($sheet);
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
        return $this->conference
                    ->byBrand($this->brands())
                    ->betweenDates($this->from(), $this->to())
                    ->with($this->relations)
                    ->notDraft()
                    ->orderBy('exibitor_start_date')
                    ->get();
    }

    /**
     * Get the Brands
     *
     * @return Illuminate\Support\Collection
     */
    protected function brands()
    {
        $values = array_get($this->arguments, 'inBrand', []);
        # Resolve
        return is_array($values) ? $values : $values->pluck('id');
    }

    /**
     * Get the start date
     *
     * @return Carbon\Carbon
     */
    protected function from()
    {
        return Carbon::parse(array_get($this->arguments, 'from', $this->getDefaultFrom()));
    }

    /**
     * Get the end date
     *
     * @return Carbon\Carbon
     */
    protected function to()
    {
        return Carbon::parse(array_get($this->arguments, 'to', $this->getDefaultTo()));
    }

    /**
     * Get transformed rows
     *
     * @return Illuminate\Support\Collection
     */
    protected function transform($data)
    {
        return $data->map(function($row){
            return (new Handlers\RowHandler($row))->fill();
        });
    }

    /**
      * Get Conference Cost
      *
      * @return Collection
    */
    protected function getConferenceCostDetails()
    {
        $rows = $this->transform($this->candidates);

        #get sum of each valid column
        $totalRow      = array(
            'Total',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            $rows->sum('Exhibitor Fee Paid (FLS)'),
            $rows->sum('Addl Sponsorship Fee'),
            $rows->sum('Conference Management-Registration'),
            $rows->sum('Conference Management-Materials'),
            $rows->sum('Booth Amenities'),
            $rows->sum('Candy/Shipping'),
            $rows->sum('Consultant Payment'),
            $rows->sum('Freight Charges'),
            $rows->sum('Other'),
            $rows->sum('Cancellation Fee'),
            $rows->sum('Check Processing Fee'),
            $rows->sum('Change Fee'),
            $rows->sum('Convenience Fee'),
            $rows->sum('Expediting Fee'),
            $rows->sum('Total'),
        );
        #keys
        $keys = Handlers\RowHandler::headers();
        # add one more row..
        $rows = array_prepend($rows->toArray(), $keys);
        # whatever
        $rows[] = $totalRow;
        # resolve
        return $rows;
    }

    /**
     * Default date
     *
     * @return Carbon\Carbon
     */
    protected function getDefaultFrom()
    {
        return Carbon::parse('January 1');
    }

    /**
     * Default end Date
     *
     * @return Carbon\Carbin
     */
    protected function getDefaultTo()
    {
        return Carbon::parse('December 31');
    }

    /**
      * Set Header/Footer Styles
      *
      * @param $sheet
      * @return Collection
      */
    protected function setHeaderFooterStyles($sheet)
    {
        $sheet->row(1, function($row) {
            $row->setFontWeight('bold');
        });

        $sheet->row($sheet->getHighestRow(), function($row) {
            $row->setFontWeight('bold');
            $row->setBackground('#7df7f7');
        });

        return $sheet;
    }
}
