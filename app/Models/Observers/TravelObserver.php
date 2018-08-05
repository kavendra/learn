<?php

namespace Betta\Models\Observers;

use Betta\Models\Travel;
use Betta\Foundation\Eloquent\AbstractObserver;
use Betta\Models\Interfaces\ProgressionStatusInterface as Status;

class TravelObserver extends AbstractObserver
{
    use ProgressionEvents;

    /**
     * In what status we shall create progression
     *
     * @var integer
     */
    protected $initialState = Status::REQUESTED;

    /**
     * Listen to the Travel creating event.
     *
     * @param  Travel  $model
     * @return void
     */
    public function creating(Travel $model)
    {
        $this->setCreator($model);
        # set Initial Status
        $model->setAttribute('progression_status_id', $model->getAttribute('progression_status_id') ?: $this->initialState);
    }

    /**
     * Listen to the Travel saved event.
     *
     * @param  Travel  $model
     * @return void
     */
    public function saved(Travel $model)
    {
        if ($model->isDirty('progression_status_id')){
            $this->recordProgression($model);
        }
    }

    /**
     * Record Progression History
     *
     * @param  Travel $model
     * @return void
     */
    protected function recordProgression(Travel $model)
    {
        # We have Previous status
        $from_status_id = $model->getOriginal('progression_status_id');
        # We have next status
        $to_status_id = $model->getAttribute('progression_status_id');
        # Progress
        $model->progressions()->create(compact('from_status_id', 'to_status_id'));
    }
}
