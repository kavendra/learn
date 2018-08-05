<?php

namespace Betta\Models\Observers;

use Betta\Models\ConferenceInvoice;
use Betta\Foundation\Eloquent\AbstractObserver;

class ConferenceInvoiceObserver extends AbstractObserver
{
    /**
     * Listen to the ConferenceInvoice creating event.
     *
     * @param  ConferenceInvoice  $model
     * @return void
     */
    public function creating(ConferenceInvoice $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the ConferenceInvoice created event.
     *
     * @param  ConferenceInvoice  $model
     * @return void
     */
    public function created(ConferenceInvoice $model)
    {

    }
}
