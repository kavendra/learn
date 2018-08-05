<?php

namespace Betta\Models\Observers;

use Betta\Models\ContractMeta;
use Betta\Foundation\Eloquent\AbstractObserver;

class ContractMetaObserver extends AbstractObserver
{
    /**
     * Listen to the ContractMeta creating event.
     *
     * @param  ContractMeta  $model
     * @return void
     */
    public function creating(ContractMeta $model)
    {
        $this->setCreator($model);
    }
}
