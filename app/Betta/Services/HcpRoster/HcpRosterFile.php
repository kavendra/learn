<?php

namespace Betta\Services\HcpRoster;

use DB;
use SplFileObject;
use LimitIterator;
use Carbon\Carbon;
use Betta\Models\Degree;
use Betta\Models\Specialty;
use Betta\Foundation\Rosterize\AbstractRosterFeed;

class HcpRosterFile extends AbstractRosterFeed
{
    /**
     * Bind the implementation
     *
     * @var Betta\Models\Profile
     */
    protected $profile;

    /**
     * Bind implementation
     *
     * @var Maatwebsite\Excel\Excel
     */
    protected $excel;

    /**
     * Chunk the data
     *
     * @var integer
     */
    protected $readChunk = 250;

    /**
     * Keep count of records
     *
     * @var integer
     */
    protected $counter = 0;

    /**
     * Run the Roster File
     *
     * @return Collection
     */
    public function run()
    {
        # Temp iterator
        $data = [];

        DB::connection()->disableQueryLog();

        foreach( $this->getIterator() as $rowNumber => $row){
            # push the element into the row
            $data[] = $row;
            # Chunk the handling
            if (($rowNumber % $this->readChunk) == 0){
                # Handle the Data
                $this->handleData($data);
                # Reset data container
                $data = [];
            }
        }

        # Residue $data
        if(!empty($data)){
            $this->handleData( $data );
        }

        return $this->getMessages();
    }

    /**
     * Handle the number of rows
     *
     * @param  array $rows
     * @return Void
     */
    protected function handleData(array $rows)
    {
        $start = Carbon::now();

        # try to get the Degrees between each all
        # $degrees = app(Degree::class)->all()->keyBy('professional_degree');

        # try to get the Specialties between each all
        # $specialties = app(Specialty::class)->all()->keyBy('label');

        # New Chunk
        $chunk = new HcpRosterChunk($rows);

        # Preload Profiles and sync() them against the HcpRosterRow
        $this->getMessageBag()->merge( $chunk->sync() );

        $this->counter += $this->readChunk;

        echo number_format($this->counter) .' - ' .$start->diffForHumans().PHP_EOL;
    }

    /**
     * Set the Roster File
     *
     * @return  SplFileObject
     */
    protected function setRosterFile()
    {
        # read the roster in chunks and apply the changes;
        $this->rosterFile = new SplFileObject( $this->getFile() );

        # Our file is a CSV
        $this->rosterFile->setFlags( SplFileObject::READ_CSV | SplFileObject::SKIP_EMPTY);

        #our file is pipe-delimitered, non-enclosure, non-escaped
        $this->rosterFile->setCsvControl('|', '~', '\\');

        return $this->rosterFile;
    }


    /**
     * Resolve the file handler
     *
     * @return SplFileObject
     */
    protected function getRosterFile()
    {
        return empty( $this->rosterFile ) ? $this->rosterFile = $this->setRosterFile() : $this->rosterFile;
    }

    /**
     * Set the interator
     *
     * @return  LimitIterator;
     */
    protected function setIterator()
    {
        $this->iterator =  new LimitIterator($this->getRosterFile(), 1);

        return $this->iterator;
    }

    /**
     * Return the iterator
     *
     * @return Interator
     */
    protected function getIterator()
    {
        return empty( $this->iterator ) ? $this->iterator = $this->setIterator() : $this->iterator;
    }
}
