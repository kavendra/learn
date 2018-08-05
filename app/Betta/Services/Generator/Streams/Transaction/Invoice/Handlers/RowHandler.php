<?php

namespace Betta\Services\Generator\Streams\Conference\Invoice\Handlers;

use Betta\Models\Conference;
use Betta\Services\Generator\Foundation\AbstratRowHandler;

class RowHandler extends AbstratRowHandler
{
    /**
     * Bind the implementation
     *
     * @var Betta\Models\Conference
     */
    protected $conference;

    /**
     * Reportable items
     *
     * @var Array
     */
    protected $keys = [
        'Conference ID',
        'Conference Status',
        'Exhibit Start Date',
        'Requested Date',
        'Event Name',
        'Acronym',
        'Brand Label',
        'Event Type',
        'Requestor',
        'District Mgr',
        'Sponsorship Level',
        'Exhibitor Fee',
        'Exhibitor Fee Paid (FLS)',
        'Addl Sponsorship Fee',
        'Conference Management-Registration',
        'Conference Management-Materials',
        'Booth Amenities',
        'Candy/Shipping',
        'Consultant Payment',
        'Freight Charges',
        'Other',
        'Cancellation Fee',
        'Check Processing Fee',
        'Change Fee',
        'Convenience Fee',
        'Expediting Fee',
        'Total',
    ];

    /**
     * Helper values hidden from public array
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Create new Row instance
     *
     * @param Conference $conference
     */
    public function __construct(Conference $conference)
    {
        $this->conference = $conference;
    }

    /**
     * Verbose Conference ID
     *
     * @return int
     */
    protected function getConferenceIDAttribute()
    {
        return $this->conference->id;
    }

    /**
     * Verbose Status
     *
     * @return string
     */
    protected function getConferenceStatusAttribute()
    {
        return data_get($this->conference->conferenceStatus, 'label', '--NOT SET');
    }

    /**
     * Conference Date, formatted as Excel Date
     *
     * @return float
     */
    protected function getExhibitStartDateAttribute()
    {
        return excel_date($this->conference->exibitor_start_date);
    }

    /**
     * When was the conference requested
     *
     * @return string
     */
     protected function getRequestedDateAttribute()
    {
        return excel_date($this->conference->created_at);
    }

    /**
     * Associated Conference
     *
     * @return string
     */
    protected function getEventNameAttribute()
    {
        return $this->conference->associated_conference;
    }

    /**
     * Conference Acronym
     *
     * @return string
     */
    protected function getAcronymAttribute()
    {
        return $this->conference->acronym;
    }

    /**
     * Brands of Conference
     *
     * @return string
     */
    protected function getBrandLabelAttribute()
    {
        return $this->conference->brands->implode('label', '|');
    }

    /**
     * Event Type of Conference
     *
     * @return string
     */
    protected function getEventTypeAttribute()
    {
        return 'Conference';
    }

    /**
     * Requestor's preferred Name
     *
     * @return string
     */
     protected function getRequestorAttribute()
    {
        return data_get($this->conference->createdBy, 'preferred_name');
    }

    /**
     * District Mgr preferred Name
     *
     * @return string
     */
     protected function getDistrictMgrAttribute()
    {
        return data_get($this->conference,'createdBy.parent.preferred_name', '');
    }

    /**
     * Sponsortship level
     *
     * @return string
     */
    protected function getSponsorshipLevelAttribute()
    {
        return $this->conference->sponsorship_level;
    }

    /**
     * Exhibitor Fee
     *
     * @return string
     */
    protected function getExhibitorFeeAttribute()
    {
        return $this->conference->exhibitor_fee;
    }

    /**
     * Resolve Estiamted Fee
     *
     * @return string
     */
    protected function getExhibitorFeePaidFLSAttribute()
    {
        return $this->conference->sponsorship_fee->sum('calculated');
    }

    /**
     * Add'l Sponsorship Fee Estiamted Fee
     *
     * @return string
     */
    protected function getAddlSponsorshipFeeAttribute()
    {
        return $this->conference->sponsorship_fee_additional->sum('calculated');
    }

    /**
     * Conference Management-Registration
     *
     * @return string
     */
    protected function getConferenceManagementRegistrationAttribute()
    {
        return $this->conference->management_registration_costs->sum('calculated');
    }

    /**
     * Conference Management-Materials
     *
     * @return string
     */
    protected function getConferenceManagementMaterialsAttribute()
    {
        return $this->conference->management_materials_costs->sum('calculated');
    }

    /**
     * Booth Amenities
     *
     * @return string
     */
    protected function getBoothAmenitiesAttribute()
    {
        return $this->conference->booth_amenities_costs->sum('calculated');
    }

    /**
     * Candy/Shipping
     *
     * @return string
     */
    protected function getCandyShippingAttribute()
    {
        return $this->conference->candy_shipping->sum('calculated');
    }

    /**
     * Consultant Payment
     *
     * @return string
     */
    protected function getConsultantPaymentAttribute()
    {
        return $this->conference->consultant_payment->sum('calculated');
    }

    /**
     * Freight Charges
     *
     * @return string
     */
    protected function getFreightChargesAttribute()
    {
        return $this->conference->freight_charges_costs->sum('calculated');
    }

    /**
     * Other
     *
     * @return string
     */
    protected function getOtherAttribute()
    {
        return $this->conference->other_costs->sum('calculated');
    }

    /**
     * Cancellation Fee
     *
     * @return string
     */
    protected function getCancellationFeeAttribute()
    {
        return $this->conference->cancellation_fee_costs->sum('calculated');
    }

    /**
     * Check Processing Fee
     *
     * @return string
     */
    protected function getCheckProcessingFeeAttribute()
    {
        return $this->conference->check_processing_costs->sum('calculated');
    }

    /**
     * Change Fee
     *
     * @return string
     */
    protected function getChangeFeeAttribute()
    {
        return $this->conference->change_fee_costs->sum('calculated');
    }

    /**
     * Convenience Fee
     *
     * @return string
     */
    protected function getConvenienceFeeAttribute()
    {
        return $this->conference->convenience_fee->sum('calculated');
    }

    /**
     * Expediting Fee
     *
     * @return string
     */
    protected function getExpeditingFeeAttribute()
    {
        return $this->conference->expediting_fee->sum('calculated');
    }

    /**
     * Total
     *
     * @return string
     */
    protected function getTotalAttribute()
    {
        return $this->getExhibitorFeePaidFLSAttribute()
             + $this->getAddlSponsorshipFeeAttribute()
             + $this->getConferenceManagementRegistrationAttribute()
             + $this->getConferenceManagementMaterialsAttribute()
             + $this->getBoothAmenitiesAttribute()
             + $this->getCandyShippingAttribute()
             + $this->getConsultantPaymentAttribute()
             + $this->getFreightChargesAttribute()
             + $this->getOtherAttribute()
             + $this->getCancellationFeeAttribute()
             + $this->getCheckProcessingFeeAttribute()
             + $this->getChangeFeeAttribute()
             + $this->getConvenienceFeeAttribute()
             + $this->getExpeditingFeeAttribute();
    }
}
