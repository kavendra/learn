<?php

namespace Betta\Models\Observers;

use Betta\Models\InvoiceHistory;
use Betta\Foundation\Eloquent\AbstractObserver;

class InvoiceHistoryObserver extends AbstractObserver
{
    /**
     * Listen to the ProgramHistory creating event.
     *
     * @param  InvoiceHistory  $model
     * @return void
     */
    public function creating(InvoiceHistory $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the InvoiceHistory created event.
     *
     * @param  InvoiceHistory  $model
     * @return void
     */
    public function created(InvoiceHistory $model)
    {

    }
}
