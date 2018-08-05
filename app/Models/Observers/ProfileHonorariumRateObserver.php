<?php

namespace Betta\Models\Observers;

use Betta\Models\ProfileHonorariumRate;
use Betta\Foundation\Eloquent\AbstractObserver;

class ProfileHonorariumRateObserver extends AbstractObserver
{

    /**
     * Listen to the ProfileHonorariumRate creating event.
     *
     * @param  ProfileHonorariumRate  $model
     * @return void
     */
    public function creating(ProfileHonorariumRate $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the ProfileHonorariumRate saving reated event.
     *
     * @param  ProfileHonorariumRate  $model
     * @return void
     */
    public function saving(ProfileHonorariumRate $model)
    {
        $this->syncWithCard($model);
    }


    /**
     * Sync the values with ProfileHonoCard
     *
     * @param  ProfileHonorariumRate $model
     * @return Void
     */
    protected function syncWithCard(ProfileHonorariumRate $model)
    {
        $syncable = [
            'brand_id',
            'profile_id',
            'valid_from',
            'valid_to'
        ];

        foreach($syncable as $field){
            $model->setAttribute($field, object_get($model->rateCard, $field));
        }
    }
}
