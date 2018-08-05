<?php

namespace Betta\Models\Observers;

use Betta\Models\InvoiceMemo;
use Betta\Foundation\Eloquent\AbstractObserver;

class InvoiceMemoObserver extends AbstractObserver
{
    /**
     * Listen to the InvoiceMemo creating event.
     *
     * @param  InvoiceMemo  $model
     * @return void
     */
    public function creating(InvoiceMemo $model)
    {

    }

    /**
     * Listen to the Memo event.
     *
     * @param  InvoiceMemo  $model
     * @return void
     */
    public function created(InvoiceMemo $model)
    {

    }
}
