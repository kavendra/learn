<?php

namespace Betta\Services\Generator\Streams\Usage\FieldPortal\Handlers;

use Betta\Models\LoginHistory;
use Illuminate\Support\Collection;
use Betta\Services\Generator\Foundation\AbstratRowHandler;

class RowHandler extends AbstratRowHandler
{
    /**
     * Bind the implementation
     *
     * @var Illuminate\Support\Collection
     */
    protected $collection;

    /**
     * Bind the implementation
     *
     * @var Betta\Models\LoginHistory
     */
    protected $loginHistory;

    /**
     * Reportable items
     *
     * @var Array
     */
    protected $keys = [
        'Profile ID',
        'Name',
        'Username',
        'Current Account Status',
        'Last Access',
        'Result',
        'IP',
        'Simulated By',
    ];

    /**
     * Helper values hidden from public array
     *
     * @var array
     */
    protected $hidden = [
        'profile'
    ];

    /**
     * Create new Row instance
     *
     * @param LoginHistory $loginHistory
     */
    public function __construct(LoginHistory $loginHistory)
    {
        $this->loginHistory = $loginHistory;
    }

    /**
     * Get Profile
     *
     * @access hidden
     * @return string
     */
    protected function getProfileAttribute()
    {
        return data_get($this->loginHistory, 'profile');
    }

    /**
     * Get Profile ID
     *
     * @return string
     */
    protected function getProfileIdAttribute()
    {
        return data_get($this->profile, 'customer_master_id');
    }

    /**
     * Get Name of the user
     *
     * @return string
     */
    protected function getNameAttribute()
    {
        return data_get($this->profile, 'preferred_name');
    }

    /**
     * Get Username
     *
     * @return string
     */
    protected function getUsernameAttribute()
    {
        return data_get($this->profile, 'user.username', '--NOT FOUND');
    }

    /**
     * Get Current Account Status
     *
     * @return string
     */
    protected function getCurrentAccountStatusAttribute()
    {
        return data_get($this->profile, 'user.status_label');
    }

    /**
     * Get Last Access
     *
     * @return float
     */
    protected function getLastAccessAttribute()
    {
        return excel_date($this->loginHistory->created_at);
    }

    /**
     * Get Last Access
     *
     * @return string
     */
    protected function getResultAttribute()
    {
        return $this->loginHistory->login_result_label;
    }

    /**
     * Get Last Access IP
     *
     * @return string
     */
    protected function getIpAttribute()
    {
        return $this->loginHistory->ip_address;
    }

    /**
     * Get Last Access IP
     *
     * @return string
     */
    protected function getSimulatedByAttribute()
    {
        return data_get($this->loginHistory->simulant, 'preferred_name');
    }
}
