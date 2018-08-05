<?php

namespace Betta\Services\Generator\Streams\Speaker\Report\BrandHonorarium\Handlers;

use Betta\Models\ProgramSpeaker;
use Betta\Services\Generator\Foundation\AbstratRowHandler;

class BrandHonorariumRow extends AbstratRowHandler
{
    /**
     * Bind the implementation
     *
     * @var Betta\Models\ProgramSpeaker
     */
    protected $programSpeaker;

    /**
     * Reportable items
     *
     * @var Array
     */
    protected $keys = [
        'Program Id',
        'Program Date',
        'Type',
        'Status',
        'Location Name',
        'Location City',
        'Location State',
        'Total Attended HCP',
        'Program Manager',
        'Brand',
        'Field Representative',
        'Area',
        'Speaker Status',
        'Speaker Name',
        'Degree',
        'Milage Round trip',
        'Speaker Honorarium',
        'Speaker Expenses',
        'Exp Form',
        'Acctg Code',
        'Payment Method',
        'Check',
        'Check date',
        'Date sent',
        'UPS Tracking',
        'Speaker City',
        'Speaker State',
    ];

    /**
     * Helper values hidden from public array
     *
     * @var array
     */
    protected $hidden = [
        'program',
        'address',
        'document',
        'profile',
        'payments',
        'payment_date',
        'speaker_address',
    ];

    /**
     * Create new Row instance
     *
     * @param ProgramSpeaker $programSpeaker
     */
    public function __construct(ProgramSpeaker $programSpeaker)
    {
        $this->programSpeaker = $programSpeaker;
    }

    /**
     * Program of ProgramSpeaker
     *
     * @see $this->hidden
     * @return Program | null
     */
    public function getProgramAttribute()
    {
        return $this->programSpeaker->program;
    }

    /**
     * Get Program ID
     * @key program_id
     *
     * @return interger
     */
    public function getProgramIdAttribute()
    {
        return $this->programSpeaker->program_id;
    }

    

    /**
     * Get Program Date
     * @key start_date
     *
     * @return string
     */
    public function getProgramDateAttribute()
    {
        return excel_date($this->program->start_date);
    }

    /**
     * Get Program Type
     * @key program_type_label
     *
     * @return string
     */
    public function getTypeAttribute()
    {
        return empty($this->program->program_type_label) ? '' : $this->program->program_type_label;
    }

    /**
     * Get Program Status
     * @key status_label
     *
     * @return string
     */
    public function getStatusAttribute()
    {
        return empty($this->program->status_label) ? '' : $this->program->status_label;
    }


    /**
     * Address of ProgramLocation
     *
     * @see $this->hidden
     * @return Address | null
     */
    public function getAddressAttribute()
    {
        return $this->program->address;
    }


    /**
     * Get Location Name
     * @key name
     *
     * @return string
     */
    public function getLocationNameAttribute()
    {
        return empty($this->address->name) ? null : $this->address->name;
    }

    /**
     * Get Location City
     * @key city
     *
     * @return string
     */
    public function getLocationCityAttribute()
    {
        return empty($this->address->city) ? null : $this->address->city;
    }

    /**
     * Get Location State
     * @key state_province
     *
     * @return string
     */
    public function getLocationStateAttribute()
    {
        return empty($this->address->state_province) ? null : $this->address->state_province;
    }

    /**
     * Get Total Attended HCP
     * @key hcp_registrations
     *
     * @return interger
     */
    public function getTotalAttendedHcpAttribute()
    {
        return $this->program->hcp_registrations->where('attended', true)->count();
    }

    /**
     * Get Program Manager
     * @key preferred_name
     *
     * @return string
     */
    public function getProgramManagerAttribute()
    {
        return empty($this->program->primary_pm->preferred_name) ? null : $this->program->primary_pm->preferred_name;
    }

    /**
     * Get Brand
     * @key label
     *
     * @return string
     */
    public function getBrandAttribute()
    {
        return empty($this->programSpeaker->brand->label) ? null : $this->programSpeaker->brand->label;
    }

    /**
     * Get Field Representative
     * @key preferred_name
     *
     * @return string
     */
    public function getFieldRepresentativeAttribute()
    {
        return empty($this->program->primary_field->preferred_name) ? null : $this->program->primary_field->preferred_name;
    }

    /**
     * Get Area
     * @key title
     *
     * @return string
     */
    public function getAreaAttribute()
    {
        return empty($this->program->primary_field->repProfile->title) ? null : $this->program->primary_field->repProfile->title;
    }

    /**
     * Get Speaker Status
     * @key status_label
     *
     * @return string
     */
    public function getSpeakerStatusAttribute()
    {
        return empty($this->programSpeaker->status_label) ? null : $this->programSpeaker->status_label;
    }

    /**
     * Get Speaker Name
     * @key preferred_name
     *
     * @return string
     */
    public function getSpeakerNameAttribute()
    {
        return empty($this->programSpeaker->preferred_name) ? null : $this->programSpeaker->preferred_name;
    }

    /**
     * Profile of ProgramSpeaker
     *
     * @see $this->hidden
     * @return Profile | null
     */
    public function getProfileAttribute()
    {
        return $this->programSpeaker->profile;
    }

    /**
     * Get Degree
     * @key degree
     *
     * @return string
     */
    public function getDegreeAttribute()
    {
        return data_get($this->profile,'hcpProfile.degree');
    }

    /**
     * Get Milage (Round trip)
     * @key driving_distance
     *
     * @return integer
     */
    public function getMilageRoundtripAttribute()
    {
        return $this->programSpeaker->driving_distance * 2;
    }

    /**
     * Get Speaker Honorarium
     * @key costs
     *
     * @return integer
     */
    public function getSpeakerHonorariumAttribute()
    {
        return $this->programSpeaker->costs->where('costItem.is_any_honorarium', true)->sum('calculated');
    }

    /**
     * Get Speaker Expenses
     * @key expenses
     *
     * @return integer
     */
    public function getSpeakerExpensesAttribute()
    {
        return $this->programSpeaker->expenses->sum('real');
    }

    /**
     * Get Document of programSpeaker
     * @hidden owner
     *
     * @return string
     */
    public function getDocumentAttribute()
    {
        return $this->programSpeaker->documents->whereIn('pivot.reference_name', 'EXPENSE_FORM')->first();
    }

    /**
     * Get Exp Form
     * @key created_at
     *
     * @return string | null
     */
    public function getExpFormAttribute()
    {
        return empty($this->document->created_at) ? '' : excel_date($this->document->created_at);
    }

    /**
     * Get Acctg Code
     * @key 
     *
     * @return string | null
     */
    public function getAcctgCodeAttribute()
    {
        return null;
    }

    /**
     * Get Payment Method of programSpeaker
     * @key payment_method
     *
     * @return string | null
     */
    public function getPaymentMethodAttribute()
    {
        return empty($this->profile->paymentMethod->payment_method) ? null : $this->profile->paymentMethod->payment_method;
    }


    /**
     * Payments of ProgramSpeaker
     *
     * @see $this->hidden
     * @return Collection | null
     */
    public function getPaymentsAttribute()
    {
        return $this->program->payments;
    }

     /**
     * Get Check #s
     *
     * @return string
     */
    public function getCheckAttribute()
    {
        return $this->payments
                    ->pluck('payment_number')
                    ->reject(function($value){
                        return empty($value);
                    })
                    ->unique()
                    ->implode(', ');
    }



    /**
     * Get Check date
     *
     * @return string
     */
    public function getCheckDateAttribute()
    {
        return $this->payments
                    ->pluck('payment_date')
                    ->reject(function($value){
                        return empty($value);
                    })
                    ->map(function($date){
                        return $date->format('m/d/Y');
                    })
                    ->unique()
                    ->implode(', ');
    }

    /**
     * Get Date sent
     * @key shipped_at
     *
     * @return string | null
     */
    public function getDatesentAttribute()
    {

        return $this->payments
                    ->map(function($payment){
                        return $payment->shipments->pluck('shipped_at')
                                        ->reject(function($value){
                                            return empty($value);
                                        })
                                        ->map(function($date){
                                            return $date->format('m/d/Y');
                                        })
                                        ->unique()
                                        ->implode(', ');

                    })->implode(', ');
    } 

    /**
     * Get UPS Tracking
     * @key tracking_id
     *
     * @return string | null
     */
    public function getUpsTrackingAttribute()
    {
        return $this->payments
                    ->map(function($payment){
                        return $payment->shipments->pluck('tracking_id')
                                        ->reject(function($value){
                                            return empty($value);
                                        })
                                        ->unique()
                                        ->implode(', ');

                    })->implode(', ');
    } 


    /**
     * Preferred Address of ProgramSpeaker
     *
     * @see $this->hidden
     * @return Address | null
     */
    public function getSpeakerAddressAttribute()
    {
        return $this->programSpeaker->profile->preferred_Address;
    }


    /**
     * Get Speaker City
     * @key city
     *
     * @return string
     */
    public function getSpeakerCityAttribute()
    {
        return data_get($this->speaker_address,'city');
    }

    /**
     * Get Speaker State
     * @key state_province
     *
     * @return string
     */
    public function getSpeakerStateAttribute()
    {
        return data_get($this->speaker_address,'state_province');
    }


}
