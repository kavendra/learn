<?php

namespace Betta\Services\Generator\Streams\Engagement;

use Carbon\Carbon;
use Betta\Models\Engagement;

trait MergesEngagementData
{
    /**
     * Use Engagement to resolve the Engagement Id
     *
     * @return string | null
     */
    public function getEngagementIdAttribute()
    {
        return data_get($this->engagement, 'id');
    }

    /**
     * Use Engagement to resolve the Engagement Label
     *
     * @return string | null
     */
    public function getEngagementLabelAttribute()
    {
        return data_get($this->engagement, 'label');
    }

    /**
     * Use Engagement to resolve the Engagement Type Label
     *
     * @return string | null
     */
    public function getEngagementTypeLabelAttribute()
    {
        return data_get($this->engagement, 'type_label');
    }

    /**
     * Use Engagement to resolve the Engagement Status Label
     *
     * @return string | null
     */
    public function getEngagementStatusLabelAttribute()
    {
        return data_get($this->engagement, 'status_label');
    }

    /**
     * Use Engagement to resolve the Engagement Brand Label
     *
     * @return string | null
     */
    public function getEngagementBrandAttribute()
    {
        return data_get($this->engagement, 'brand_label');
    }

    /**
     * Use Engagement to resolve the Engagement Start Date
     *
     * @return string | null
     */
    public function getEngagementStartDateAttribute()
    {
        if($value = data_get($this->engagement, 'start_date')){
            return Carbon::parse($value)->format($this->dateFormat);
        }
    }

    /**
     * Use Engagement to resolve the Engagement Start Date
     *
     * @return string | null
     */
    public function getEngagementDateLongAttribute()
    {
        if($value = data_get($this->engagement, 'start_date')){
            return Carbon::parse($value)->format(config('betta.long_date'));
        }
    }

    /**
     * Use Engagement to resolve the Engagement End Date
     *
     * @return string | null
     */
    public function getEngagementEndDateAttribute()
    {
        if($value = data_get($this->engagement, 'end_date')){
            return Carbon::parse($value)->format($this->dateFormat);
        }
    }

    /**
     * Use Engagement to resolve the Engagement Title
     *
     * @return string | null
     */
    public function getEngagementTitleAttribute()
    {
        return data_get($this->engagement, 'title');
    }

    /**
     * Use Engagement to resolve the Engagement Title
     *
     * @return string | null
     */
    public function getEngagementHostedByAttribute()
    {
        return data_get($this->engagement, 'host.preferred_name');
    }

    /**
     * Use container Request  to solve the manager
     *
     * @return string | null
     */
    public function getEngagementManagerNameAttribute()
    {
        return data_get($this->engagement, 'request.primary_coordinator.preferred_name');
    }

    /**
     * Use container Request  to solve the manager' email
     *
     * @return string | null
     */
    public function getEngagementManagerEmailAttribute()
    {
        return data_get($this->engagement, 'request.primary_coordinator.email');
    }

    /**
     * Use container Request  to solve the manager' email
     *
     * @return string | null
     */
    public function getEngagementManagerPhoneAttribute()
    {
        return data_get($this->engagement, 'request.primary_coordinator.phone');
    }

    /**
     * Use container Request to resolve Address
     *
     * @return string | null
     */
    public function getEngagementLocationNameAttribute()
    {
        return data_get($this->engagement, 'request.address.name');
    }

    /**
     * Use container Request to resolve Address
     *
     * @return string | null
     */
    public function getEngagementLocationCityStateAttribute()
    {
        return data_get($this->engagement, 'request.address.city_state');
    }

    /**
     * Use container Request to resolve Address
     *
     * @return string | null
     */
    public function getEngagementLocationCityStateZipAttribute()
    {
        return data_get($this->engagement, 'request.address.city_state_zip');
    }
}
