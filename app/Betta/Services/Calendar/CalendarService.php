<?php

namespace Betta\Services\Calendar;

use Makzumi\Calendar\Calendar;

class CalendarService
{
    /**
     * Bind the implementation
     *
     * @var Makzumi\Calendar\Calendar;
     */
    protected $calendar;

    /**
     * Create new instance of the Caledar service
     *
     * @param Calendar $calendar
     */
    public function __construct(Calendar $calendar)
    {
        $this->calendar = $calendar;
    }

    /**
     * Generate the Calendar for the view
     *
     * @param  array  $programs
     * @return string
     */
    public function generate($programs = array())
    {
        $this->calendar->setDate( request('cdate') );
        $this->calendar->setBasePath('');
        $this->calendar->setLink( $this->datesToUrls($programs) );
        $this->calendar->setNextIcon('<i class="fa fa-chevron-right"></i>');
        $this->calendar->setPrevIcon('<i class="fa fa-chevron-left"></i>');
        $this->calendar->setTableClass('table calendar');
        $this->calendar->setHeadClass('table-header');
        $this->calendar->setNextClass('btn');
        $this->calendar->setPrevClass('btn');
        $this->calendar->setEvents( $this->convertPrograms($programs) );

        return  $this->calendar->generate();
    }

    /**
     * Obtain the list of months for the dropdown
     */
    public function monthList()
    {
        # define Array
        $monthList = [];

        # iterate through 12 months
        for ($i = 1; $i <= 12; $i++) {
            $monthList[$i] = date('F', mktime(0, 0, 0, $i + 1, 0, 0));
        }

        # return localized months
        return $monthList;
    }

    /**
     * Produce the list of years for Dropdown
     *
     * @param  integer $fromYear
     * @param  integer $toYear
     * @return Array
     */
    public  function yearList($fromYear = 2016, $toYear = null, $format = '%s')
    {
        $toYear = is_null($toYear)  ? intval(date('Y') + 1) : $toYear;

        # combine years and match to IDs
        return array_combine($range = range((int) $fromYear, (int) $toYear), $range);
    }

    /**
     * Produce the array of the URLs
     * @param array $programs
     * @return array
     */
    protected function datesToUrls(array $programs)
    {
        return $programs;
    }

    /**
     * Produce the array of the Programs
     *
     * @param array $programs
     * @return array
     */
    protected function convertPrograms(array $programs)
    {
        return $programs;
    }
}
