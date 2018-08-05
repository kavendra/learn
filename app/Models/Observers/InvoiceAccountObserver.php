<?php

namespace Betta\Models\Observers;

use Betta\Models\InvoiceAccount;
use Betta\Foundation\Eloquent\AbstractObserver;

class InvoiceAccountObserver extends AbstractObserver
{
    /**
     * Listen to the InvoiceAccount creating event.
     *
     * @param  InvoiceAccount  $model
     * @return void
     */
    public function creating(InvoiceAccount $model)
    {

    }

    /**
     * Listen to the InvoiceAccount event.
     *
     * @param  InvoiceAccount  $model
     * @return void
     */
    public function created(InvoiceAccount $model)
    {

    }
}
