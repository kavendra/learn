<?php

namespace Betta\Models\Observers;

use Betta\Models\ProgramPresentation;
use Betta\Foundation\Eloquent\AbstractObserver;

class ProgramPresentationObserver extends AbstractObserver
{

    /**
     * Listen to the ProgramPresentation creating event.
     *
     * @param  ProgramPresentation  $model
     * @return void
     */
    public function creating(ProgramPresentation $model)
    {

    }
}
