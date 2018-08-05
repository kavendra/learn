<?php

namespace Betta\Services\Generator\Streams\Conference\ScheduleList;

use Betta\Models\ConferenceSchedule;
use Maatwebsite\Excel\Excel;
use Betta\Services\Generator\Foundation\AbstractReport;

class Report extends AbstractReport
{
    use ReportQueryBuilder;

    /**
     * Title of the Report
     *
     * @var string
     */
    protected $title = 'Schedule List Report';

    /**
     * Value to populate in the Report
     *
     * @var string
     */
    protected $description = 'List all Conference Schedule information';

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
        'schedules.attendees',
    ];

     /**
     * Column Formats
     *
     * @see Betta\Services\Generator\Interfaces\ExcelFormatsInterface
     * @var array
     */
    protected $formats = [

    ];

    /**
     * Create new Instance of Conference List Report

     * @return Void
     */
    public function __construct(Excel $excel)
    {
        $this->excel = $excel;
    }

    /**
     * Produce the Report
     *
     * @return Array
     */
    protected function process()
    {
        return $this->excel->create($this->getReportName(), function($excel){
            # Set standard properties on the file
            $this->setProperties($excel);
            # Produce the tab
            # @todo exctract tabs into their own classes
            foreach($this->getCandidates() as $conference ){
                $slot  = $conference->slots_day_wise;
                foreach($slot as $day_date => $schedule){
                    $sheet_name = date('l', $day_date). ' Meeting Rooms Schedule';
                    # Sheet for each group
                    $excel->sheet($sheet_name, function ($tab) use($day_date, $schedule){
                        $tab->setColumnFormat( $this->getFormats() )
                            ->freezeFirstRow();

                        $tab->fromArray( $this->getInTransform($day_date, $schedule) );

                        $tab->mergeCells('A2:C2');
                        $tab->mergeCells('A3:C3');
                        $tab->mergeCells('A4:C4');
                        $tab->getStyle('A2:C2')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $tab->getStyle('A3:C3')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $tab->getStyle('A4:C4')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $this->setHeaderFooterStyles($tab, $schedule);
                    });
               }
            }
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
        # get the results from builder
        return $data = $this->getBuilder($arguments)->with($this->relations)->get()->sortByDesc('start_date');
    }

    /**
     * Overload the transform method
     *
     * @param  Collection $data
     * @return Collection
     */
    protected function getInTransform($day_date, $schedules)
    {
        $conference = $this->getCandidates()->first();
        $i = 1;
        $array = [];
        $array[$i++][0] = $conference->label;
        $array[$i++][0] = date('m/d/Y', $day_date);
        $array[$i++][0] = date('l', $day_date);

        $array[$i][0] = '';
        $j = 1;
        if($conference){
          foreach($conference->rooms as $room){
            $array[$i][$j++] = $room->name;
          }
        }
        $i++;
        foreach($schedules as $schedule){
            $start = $schedule->start_time->format("h:i A");
            $end = $schedule->end_time->format("h:i A");
            $array[$i][0] = $start.'-'.$end;
            if($schedule->schedule){
                foreach($conference->rooms as $room){
                    $filterschedule = $schedule->schedule->filter(function($item) use($room){
                        return $item->room_id == $room->id;
                    })->first();
                    $array[$i][$room->id] = $filterschedule ? ('Host: '.$filterschedule->host_name.', '.' Attendee(s): '.$filterschedule->attendees->implode('attendee_name', ', ')) : '';
                }
            }
            $i++;
        }
        return $array;
    }

    /**
      * Set Header/Footer Styles
      *
      * @param $sheet
      * @return Collection
      */
    protected function setHeaderFooterStyles($sheet, $schedules)
    {
        $sheet->row(2, function($row) {
            $row->setFontWeight('bold');
        });
        $sheet->row(5, function($row) {
            $row->setFontWeight('bold');
        });
        $l=6;
        if($schedules){
            foreach($schedules as $schedule){
                $sheet->setHeight($l, 50);
                $l++;
            }
        }
        $sheet->row('2', function($cells) {
          $cells->setFontSize(15);
        });
        return $sheet;
    }
}
