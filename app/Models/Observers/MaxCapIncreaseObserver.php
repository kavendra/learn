<?php

namespace Betta\Models\Observers;

use Betta\Models\MaxCapIncrease;
use Betta\Foundation\Eloquent\AbstractObserver;
use App\Events\MaxCap\Increase\RequestApproval;

class MaxCapIncreaseObserver extends AbstractObserver
{
    /**
     * Listen to the MaxCapIncrease creating event.
     *
     * @param  MaxCapIncrease  $model
     * @return void
     */
    public function creating(MaxCapIncrease $model)
    {
        # Set Current User as Creator, unless provided
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the MaxCapIncrease created() event.
     *
     * @param  MaxCapIncrease  $model
     * @return void
     */
    public function created(MaxCapIncrease $model)
    {
        event(new RequestApproval($model));
    }

    /**
     * Listen to the MaxCapIncrease deleted() event.
     *
     * @param  MaxCapIncrease  $model
     * @return void
     */
    public function deleted(MaxCapIncrease $model)
    {
        # remove the unsued approvas
        # That should trigger cascade of ActionUrls deletions
        $model->approvals()->unused()->delete();
    }
}
