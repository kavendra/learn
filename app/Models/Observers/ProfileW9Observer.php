<?php

namespace Betta\Models\Observers;

use Betta\Models\ProfileW9;
use App\Events\Profile\W9\Completed;
use App\Events\Profile\W9\Initiated;
use Betta\Foundation\Eloquent\AbstractObserver;

class ProfileW9Observer extends AbstractObserver
{
    /**
     * Listen to the ProfileW9 creating event.
     *
     * @param  ProfileW9  $model
     * @return void
     */
    public function creating(ProfileW9 $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the ProfileW9 created event.
     *
     * @param  ProfileW9  $model
     * @return void
     */
    public function created(ProfileW9 $model)
    {
        if(!$model->is_completed){
            event(new Initiated($model));
        }
    }

    /**
     * Listen to the ProfileW9 saving event.
     *
     * @param  ProfileW9  $model
     * @return void
     */
    public function saving(ProfileW9 $model)
    {
        $this->completeIfCompleted($model);
    }

    /**
     * Listen to the ProfileW9 saved event.
     * We cannot "complete" an unfinished model
     *
     * @param  ProfileW9  $model
     * @return void
     */
    public function saved(ProfileW9 $model)
    {
        if($model->is_completed AND $model->isDirty('is_completed')){
            # fire the Event
            event(new Completed($model));
        }
    }

    /**
     * True if the ProfileW9 is recently completed
     *
     * @param  ProfileW9 $model
     * @return boolean
     */
    protected function isRecentlyCompleted(ProfileW9 $model)
    {
        # New status, boolean
        $isCompleted = $model->getAttribute('is_completed');
        # When date, Carbon
        $completedWhen = $model->getAttribute('completed_at');
        # compare and return
        return $model->isDirty('is_completed') AND $isCompleted AND empty($completedWhen);
    }

    /**
     * Complete if Completed
     *
     * @param  ProfileW9 $model
     * @return Void
     */
    protected function completeIfCompleted(ProfileW9 $model)
    {
        # Is recently completed
        if( $this->isRecentlyCompleted($model) ){
            # Set Current time as completed time
            $model->setAttribute('completed_at', $this->now());
        }
    }

    /**
     * Listen to the ProfileW9 deleted event.
     *
     * @param  ProfileW9  $model
     * @return void
     */
    public function deleted(ProfileW9 $model)
    {
        $model->alerts()->delete();
    }
}
