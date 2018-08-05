<?php

namespace Betta\Models\Observers;

use Betta\Models\ConferenceInvoiceBatch;
use Betta\Foundation\Eloquent\AbstractObserver;

class ConferenceInvoiceBatchObserver extends AbstractObserver
{
    /**
     * Listen to the ConferenceInvoiceBatch creating event.
     *
     * @param  ConferenceInvoiceBatch  $model
     * @return void
     */
    public function creating(ConferenceInvoiceBatch $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the ConferenceInvoiceBatch created event.
     *
     * @param  ConferenceInvoiceBatch  $model
     * @return void
     */
    public function created(ConferenceInvoiceBatch $model)
    {

    }
}
