<?php

namespace Betta\Foundation\Eloquent;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractSeeder extends Seeder
{
    /**
     * Bind the implementation
     *
     * @var Model
     */
    protected $model;

    /**
     * Records to insert
     *
     * @var array
     */
    protected $records = [];


    /**
     * Indicates if existing records need to be removed
     *
     * @var boolean
     */
    protected $refresh = true;


    /**
     * Create new isntance of Seeder
     *
     * @param Illuminate\Database\Eloquent\Model $model
     */
    public function __construct(Model $model = null)
    {
        $this->model = $model;
    }


    /**
     * Seed the model
     *
     * @return void
     */
    public function run()
    {
        # Make sure we can write into model
        $this->model->unguard();

        # delete if requested
        if ($this->refresh){
            $this->model->query()->delete();
        }

        # Seed
        foreach($this->getRecords() as $record)
        {
            $this->model->create($record);
        }

        # Protect model again
        $this->model->reguard();
    }


    /**
     * Return the Records into the
     *
     * @return array
     */
    protected function getRecords()
    {
        return $this->records;
    }
}
