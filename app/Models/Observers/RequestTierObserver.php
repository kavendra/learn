<?php

namespace Betta\Models\Observers;

use Betta\Models\RequestTier as Model;
use App\Events\Request\CheckRequest;
use Betta\Foundation\Eloquent\AbstractObserver;

class RequestTierObserver extends AbstractObserver
{
    /**
     * Listen to the RequestTier creating event.
     *
     * @param  RequestTier  $model
     * @return void
     */
    public function creating(Model $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
        # Set Current User as Cerator
        $model->setAttribute('assigned_to_id', $this->assignedTo($model));
    }

    /**
     * Listen to the RequestTier saving event.
     *
     * @param  RequestTier  $model
     * @return void
     */
    public function saving(Model $model)
    {
        # when the completed_at is dirtly from null, fire the completed event
        if($model->isDirtyFromNull('tier_id')){
            # Who completes?
            $model->setAttribute('completed_by_id', $this->completedBy($model));
            # First time it happens..?
            $model->setAttribute('completed_at', $this->now());
        }
    }

    /**
     * Listen to the Nomination History updated event
     *
     * @param  RequestTier  $model
     * @return void
     */
    public function saved(Model $model)
    {
        # when the completed_at is dirtly from null, fire the completed event
        if($model->isDirtyFromNull('completed_at')){
            event(new CheckRequest($model->request));
        }

        # When tier is changed, sync it into upper scope
        if($model->isDirty('tier_id')){
            # Sync up the tier
            $model->request->update(['tier_id'=>$model->tier_id]);
            # leave an automatic note
            $model->request->addPrivateNote("[Auto] Updated Tier to {$model->tier_label}");
        }
    }

    /**
     * Come up with teh default assignedTo
     *
     * @param  Model  $model
     * @return int | null
     */
    protected function assignedTo(Model $model)
    {
        # Default to the base medical approver
        $default = data_get($model, 'brand.base_medical_approver_id');
        # resolve
        return $model->getAttribute('assigned_to_id') ?: $default;
    }

    /**
     * Come up with teh default completedBy
     *
     * @param  Model  $model
     * @return int | null
     */
    protected function completedBy(Model $model)
    {
        return $model->getAttribute('completed_by_id') ?: $this->getUserId();
    }
}
