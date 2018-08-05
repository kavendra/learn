<?php

namespace Betta\Services\Generator\Streams\Programs\Report\Porzio\Handlers;

trait SharedValues
{
    /**
     * Enumerated Transaction Type
     *
     * @return string
     */
    public function getTransactionTypeAttribute()
    {
        return 'I';
    }

    /**
     * Enumerated value for the Source
     *
     * @return string
     */
    public function getSpendSourceCodeAttribute()
    {
        return 'FRICTIONLESS';
    }

    /**
     * Vendor producing the Report
     *
     * @return string
     */
    public function getVendorAttribute()
    {
        return 'FRICTIONLESS SOLUTIONS';
    }

    /**
     * Company Name
     *
     * @return string
     */
    public function getCompanyCodeAttribute()
    {
        return 'HORIZON PHARMA PLC';
    }

    /**
     * Enumerated value for the Spend Nature
     *
     * @return string
     */
    public function getSpendNatureOrTypeAttribute()
    {
        return 'CASH OR CASH EQUIVALENT';
    }

    /**
     * Enumerated value for the Payment Method
     *
     * @return null
     */
    public function getSpendPaymentMethodAttribute()
    {
        return null;
    }

    /**
     * Spend Owner
     *
     * @return null
     */
    public function getSpendOwnerAttribute()
    {
        return null;
    }

    /**
     * No Definition is provided
     *
     * @return null
     */
    public function getAttendeesClientEmployeesAttribute()
    {
        return null;
    }

    /**
     * Comments
     *
     * @return string
     */
    public function getCommentsAttribute()
    {
        return null;
    }

    /**
     * Single Recipient Indicator
     *
     * @return null
     */
    public function getSingleRecipientIndicatorAttribute()
    {
        return null;
    }

    /**
     * Enumerated value for teh Report Name
     *
     * @return null
     */
    public function getCustomerExpenseReportNameAttribute()
    {
        return null;
    }

    /**
     * Count the number of non-showed ups
     *
     * @return null
     */
    public function getNoShowDidNotConsumeCountAttribute()
    {
        return null;
    }

    /**
     * Reserved for Future Use
     *
     * @return null
     */
    public function getPhysicianOwnershipIndicatorAttribute()
    {
        return null;
    }
}
