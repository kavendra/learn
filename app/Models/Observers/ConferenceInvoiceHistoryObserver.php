<?php

namespace Betta\Models\Observers;

use Betta\Models\ConferenceInvoiceHistory;
use Betta\Foundation\Eloquent\AbstractObserver;

class ConferenceInvoiceHistoryObserver extends AbstractObserver
{
    /**
     * Listen to the ProgramHistory creating event.
     *
     * @param  ConferenceInvoiceHistory  $model
     * @return void
     */
    public function creating(ConferenceInvoiceHistory $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the ConferenceInvoiceHistory created event.
     *
     * @param  ConferenceInvoiceHistory  $model
     * @return void
     */
    public function created(ConferenceInvoiceHistory $model)
    {

    }
}
