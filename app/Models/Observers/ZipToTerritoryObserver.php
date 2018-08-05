<?php

namespace Betta\Models\Observers;

use Betta\Models\ZipToTerritory;
use Betta\Foundation\Eloquent\AbstractObserver;

class ZipToTerritoryObserver extends AbstractObserver
{
    /**
     * Listen to the model creating event.
     *
     * @param  ZipToTerritory  $model
     * @return void
     */
    public function creating(ZipToTerritory $model)
    {
        # Set Valid From
        $model->setAttribute('valid_from', $model->getAttribute('valid_from') ?: $this->now() );


        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }


}
