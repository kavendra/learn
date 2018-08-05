<?php

namespace Betta\Models\Observers;

use Betta\Models\Cost;
use App\Events\Cost\SyncMaxCap;
use App\Events\Cost\AccruableCostSaved;
use Betta\Foundation\Eloquent\AbstractObserver;

class CostObserver extends AbstractObserver
{
    /**
     * Listen to the Cost creating event.
     *
     * @param  Cost  $model
     * @return void
     */
    public function creating(Cost $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );

		$model->setAttribute('estimate', $model->getAttribute('estimate') ?: 0 );
    }

    /**
     * Listen to the Cost created event.
     *
     * @param  Cost  $model
     * @return void
     */
    public function saving(Cost $model)
    {
        # Empty fields should be null; 0 is an actual value
        $this->setNullableField($model, 'actual');
        $this->setNullableField($model, 'reconciled');
    }

    /**
     * Listen to the Cost::saved() event
     *
     * @param  Cost   $model
     * @return Void
     */
    public function saved(Cost $model)
    {
        if (data_get($model->context, 'accrues_costs')){
            event(new SyncMaxCap($model));
        }
    }
}
