<?php

namespace Betta\Models\Observers;

use Betta\Models\Request;
use Betta\Models\RequestStatus;
use App\Events\Request\PropagadeTier;
use Betta\Foundation\Eloquent\AbstractObserver;

class RequestObserver extends AbstractObserver
{
    /**
     * Create the lsit of Request events
     *
     * @var Array
     */
    protected $statusEvents = [
        RequestStatus::DRAFT => 'App\Events\Request\Draft',
        RequestStatus::SUBMITTED => 'App\Events\Request\Submitted',
        RequestStatus::IN_PROGRESS => 'App\Events\Request\InProgress',
        RequestStatus::COMPLETED => 'App\Events\Request\Completed',
        RequestStatus::CANCELLED => 'App\Events\Request\Cancelled',
    ];

    /**
     * Listen to the Request creating event.
     *
     * @param  Request  $model
     * @return void
     */
    public function creating(Request $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
        # Set the Owner ID to current user
        $model->setAttribute('owner_id', $model->getAttribute('owner_id') ?: $this->getUserId() );
        # Set Tiering Needs
        $model->setAttribute('is_tiering_needed', $this->isTieringNeeded($model));
        # Set Tiering New
        $model->setAttribute('is_tiering_new', $this->isTieringNew($model));
    }

    /**
     * Listen to the Nomination History updated event
     *
     * @param  Nomination  $model
     * @return void
     */
    public function saved(Request $model)
    {
        if($model->isDirty($model->getStatusFieldName())){
            $this->recordHistory($model);
            $this->fireStatusEvent($model);
        }
        # Propagade tier changes
        if($model->isDirty('tier_id')){
            event(new PropagadeTier($model));
        }
    }

    /**
     * Listen to the Nomination History deleted event
     *
     * @param  Nomination  $model
     * @return void
     */
    public function deleted(Request $model)
    {
        $models = [
            'engagement',
            'address',
        ];

        foreach($models as $relation){
            $model->$relation()->delete();
        }
    }

    /**
     * Record History
     *
     * @param  Request $model
     * @return Void
     */
    protected function recordHistory(Request $model)
    {
        # we have Previous status
        $from_status_id = $model->getOriginal($model->getStatusFieldName());
        # we have next status
        $to_status_id = $model->getAttribute($model->getStatusFieldName());
        # Inject history
        $model->histories()->create(compact('from_status_id','to_status_id'));
    }

    /**
     * First Status Event
     *
     * @param  Request $model
     * @return Void
     */
    protected function fireStatusEvent(Request $request)
    {
        # we have next status
        $to = $request->getAttribute($request->getStatusFieldName());
        # If located, fire
        if ($event = array_get($this->statusEvents, $to)){
            event (new $event($request));
        }
    }

    /**
     * True if tiering is needed
     *
     * @see    Betta\Models\Decorators\Request\Tiering
     * @param  Request $request
     * @return boolean
     */
    protected function isTieringNeeded(Request $request)
    {
        # regardless of what's "needed", we can determine that
        # if the rating is requested and there is no tier, we need to create it
        return $request->is_tiering_needed OR $request->is_tiering_required;
    }

    /**
     * True if new tiering is needed
     *
     * @see    Betta\Models\Decorators\Request\Tiering
     * @param  Request $request
     * @return boolean
     */
    protected function isTieringNew(Request $request)
    {
        # regardless of what's requested, we can determine that
        # if the rating is requested and there is no tier, we need to create it
        return $request->is_tiering_needed and empty($request->tier_id);
    }
}
