<?php

namespace Betta\Services\Generator\Streams\Core\Report\Chase\Handlers;

use Betta\Models\Reconciliation;
use Betta\Models\Program;
use Betta\Services\Generator\Foundation\AbstratRowHandler;

class UnclaimedRow extends AbstratRowHandler
{
    /**
     * Bind the implementation
     *
     * @var Betta\Models\Reconciliation
     */
    protected $reconciliation;

    /**
     * Reportable items
     *
     * @var Array
     */
    protected $keys = [
        'CORE ID',
        'Program Id',
        'Created at',
        'Label',
        'Status',

    ];

    /**
     * Create new Row instance
     *
     * @param Reconciliation $reconciliation
     */
    public function __construct(Reconciliation $reconciliation)
    {
        $this->reconciliation = $reconciliation;
    }

    /**
     * ID Attribute for the Reconciliation
     *
     * @return int
     */
    protected function getCOREIDAttribute()
    {
        return $this->reconciliation->id;
    }

    /**
     * Program Id
     *
     * @return int
     */
    protected function getProgramIdAttribute()
    {
        return data_get($this->reconciliation->program, 'id');
    }

    /**
     * Program Date
     *
     * @return string
     */
    protected function getCreatedAtAttribute()
    {
        return excel_date($this->reconciliation->created_at);
    }

    /**
     * Program Label
     *
     * @return string
     */
    protected function getLabelAttribute()
    {
        return data_get($this->reconciliation->program, 'full_label');
    }

    /**
     * Reconciliation Status
     *
     * @return string
     */
    protected function getStatusAttribute()
    {
        return $this->reconciliation->statusLabel;
    }
}
