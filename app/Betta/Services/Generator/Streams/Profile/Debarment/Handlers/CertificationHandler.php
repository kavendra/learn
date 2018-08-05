<?php

namespace Betta\Services\Generator\Streams\Profile\Debarment\Handlers;

use Betta\Models\BackgroundCheck;
use Betta\Models\BackgroundCheckProvider;
use Betta\Services\Generator\Foundation\AbstratRowHandler;

class CertificationHandler extends AbstratRowHandler
{
    /**
     * Bind the implementation
     *
     * @var Betta\Models\BackgroundCheck
     */
    protected $backgroundCheck;

    /**
     * Bind the implementation
     *
     * @var Betta\Models\BackgroundCheckProvider
     */
    protected $backgroundCheckProvider;

    /**
     * Reportable items
     *
     * @var Array
     */
    protected $keys = [
        'c_status',
        'provider',
        'c_message',
    ];

    /**
     * Helper values hidden from public array
     *
     * @var array
     */
    protected $hidden = [
        'certification_at',
        'certification_by',
        'certification'
    ];

    /**
     * Create new Row instance
     *
     * @param Betta\Models\BackgroundCheck $backgroundCheck
     * @param Betta\Models\BackgroundCheckProvider $backgroundCheckProvider
     */
    public function __construct(BackgroundCheckProvider $provider, BackgroundCheck $check)
    {
        $this->backgroundCheck = $check;
        $this->backgroundCheckProvider = $provider;
    }

    /**
     * Make the Certification: we will obtain the ::latest:: value
     *
     * @return string
     */
    protected function getCertificationAttribute()
    {
        return $this->backgroundCheck
                    ->certifications
                    ->where('certification_type_id', $this->backgroundCheckProvider->certification_type_id)
                    ->last();
    }

    /**
     * Make the Certification Status
     *
     * @return string | null
     */
    protected function getCStatusAttribute()
    {
        if($this->certification){
            return 'PASS';
        }

        return '';
    }

    /**
     * Make the Certification Date
     *
     * @return string
     */
    protected function getCertificationAtAttribute()
    {
        if($date = data_get($this->certification, 'created_at')){
            return $date->format( config('betta.short_date') );
        }

        return null;
    }

    /**
     * Make the Certification Date
     *
     * @return string | null
     */
    protected function getCertificationByAttribute()
    {
        return data_get($this->certification, 'createdBy.preferred_name');
    }

    /**
     * Make the Certification Message
     *
     * @return string | null
     */
    protected function getCMessageAttribute()
    {
        if($certification = $this->certification){
            return "Certified at {$this->certification_at} by {$this->certification_by}";
        }

        return "Check is pending";
    }

    /**
     * Make the Provider Name
     *
     * @return string
     */
    protected function getProviderAttribute()
    {
        return $this->backgroundCheckProvider->label;
    }
}
