<?php

namespace Betta\Models\Observers;

use Betta\Models\Contract;
use Betta\Models\ContractStatus;
use Betta\Foundation\Eloquent\AbstractObserver;

class ContractObserver extends AbstractObserver
{
    /**
     * In what status we shall create progression
     *
     * @var integer
     */
    protected $initialState = ContractStatus::INITIATED;

    /**
     * Create the list of events
     *
     * @var Array
     */
    protected $statusEvents = [
        ContractStatus::INITIATED => 'App\Events\Contract\Initiated',
        ContractStatus::PENDING => 'App\Events\Contract\Pending',
        ContractStatus::SIGNED => 'App\Events\Contract\Signed',
        ContractStatus::COUNTER_SIGNED => 'App\Events\Contract\CounterSigned',
        ContractStatus::DECLINED => 'App\Events\Contract\Declined',
        ContractStatus::ACTIVE => 'App\Events\Contract\Active',
        ContractStatus::SUSPENDED => 'App\Events\Contract\Suspended',
        ContractStatus::EXPIRED => 'App\Events\Contract\Expired',
        ContractStatus::CANCELLED => 'App\Events\Contract\Cancelled',
        ContractStatus::INACTIVE => 'App\Events\Contract\Inactive',
    ];

    /**
     * Listen to the Contract creating event.
     *
     * @param  Contract  $model
     * @return void
     */
    public function creating(Contract $model)
    {
        # Set Current User as Creator
        $this->setCreator($model);
        # set Initial Status
        $model->setAttribute('contract_status_id', $model->getAttribute($model->getStatusFieldName()) ?: $this->initialState );
    }

    /**
     * Listen to the Contract saving event.
     *
     * @param  Contract  $model
     * @return void
     */
    public function saving(Contract $model)
    {
        # w9 is nullable
        $this->setNullableField($model, 'source_profile_w9_id');
        # rateCard is nullable
        $this->setNullableField($model, 'source_profile_honorarium_rate_id');
    }

    /**
     * Listen to the Nomination History saved event
     *
     * @param  Nomination  $model
     * @return void
     */
    public function saved(Contract $model)
    {
        if($model->isDirty($model->getStatusFieldName())){
            $this->fireStatusEvents($model);
        }
    }

    /**
     * Fire events
     *
     * @param  Contract $model
     * @return Void
     */
    protected function fireStatusEvents(Contract $model)
    {
        # we have Previous status
        $from_status_id = $model->getOriginal($model->getStatusFieldName());
        # we have next status
        $to_status_id = $model->getAttribute($model->getStatusFieldName());
        # Inject history
        $model->histories()->create(compact('from_status_id','to_status_id'));
        # Map the status to events and fire them all
        if ($event = array_get($this->getStatusEvents($model), $to_status_id)){
            event (new $event($model));
        }
    }

    /**
     * Decide what events should take palce
     *
     * @param  Contract $model
     * @return Array
     */
    protected function getStatusEvents(Contract $model)
    {
        return  $this->statusEvents;
    }
}
