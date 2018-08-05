<?php

namespace Betta\Models\Observers;

use Betta\Models\ConferenceAffiliateHistory;
use Betta\Foundation\Eloquent\AbstractObserver;

class ConferenceAffiliateHistoryObserver extends AbstractObserver
{
    /**
     * Listen to the ConferenceHousing creating event.
     *
     * @param  ConferenceHousing  $model
     * @return void
     */
    public function creating(ConferenceAffiliateHistory $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the ConferenceHousing created event.
     *
     * @param  ConferenceHousing  $model
     * @return void
     */
    public function created(ConferenceAffiliateHistory $model)
    {

    }

    /**
     * Listen to ConferenceHousing update event
     *
     * @param  ConferenceHousing $model
     * @return void
     */
    public function updated(ConferenceAffiliateHistory $model)
    {

    }
}
