<?php

namespace Betta\Services\Generator\Streams\Programs\Report\Chase\Handlers;

use Betta\Models\Program;
use Betta\Helpers\DateFormats;
use Betta\Services\Generator\Foundation\AbstratRowHandler;

class UnconfirmedRow extends AbstratRowHandler
{
    /**
     * Bind the implementation
     *
     * @var Betta\Models\Program
     */
    protected $program;

    /**
     * Reportable items
     *
     * @var Array
     */
    protected $keys = [
        'ID',
        'Program Date',
        'Brand',
        'Type',
        'Account Manager',
        'Speaker Name',
        'Program Claimed',
        'Speaker Initial Contact',
        'Speaker Last Contact',
        'Speaker Follow Up',
        'Contracted',
        'Trained',
        'Program Manager',
    ];

    /**
     * Helper values hidden from public array
     *
     * @var array
     */
    protected $hidden = [
        'primary_speaker'
    ];

    /**
     * Create new Row instance
     *
     * @param Program $program
     */
    public function __construct(Program $program)
    {
        $this->program = $program;
    }

    /**
     * ID Attribute for the program
     *
     * @return int
     */
    protected function getIdAttribute()
    {
        return $this->program->id;
    }

    /**
     * Program Date
     *
     * @return float
     */
    protected function getProgramDateAttribute()
    {
        return DateFormats::excelDate($this->program->start_date);
    }

    /**
     * Program Brands
     *
     * @return string
     */
    protected function getBrandAttribute()
    {
        return $this->program->brands->implode('label', ' | ');
    }

    /**
     * Program Type label
     *
     * @return string
     */
    protected function getTypeAttribute()
    {
        return $this->program->program_type_label;
    }

    /**
     * Program Field Name
     *
     * @return string | null
     */
    protected function getAccountManagerAttribute()
    {
        return data_get($this->program->primary_field, 'preferred_name');
    }

    /**
     * Program Speakers' names
     *
     * @return string
     */
    protected function getSpeakerNameAttribute()
    {
        return $this->program->primarySpeakers->implode('profile.preferred_name', ', ');
    }

    /**
     * Program Claimed at
     *
     * @return float | null
     */
    protected function getProgramClaimedAttribute()
    {
        return DateFormats::excelDate(data_get($this->program->primary_pm, 'pivot.created_at'));
    }

    /**
     * Program Speaker | initial contact
     *
     * @return float
     */
    protected function getSpeakerInitialContactAttribute()
    {
        return DateFormats::excelDate(data_get($this->program->speaker_communications_latest, 'created_at'));
    }

    /**
     * Program Speakers' last communication
     *
     * @return float | null
     */
    protected function getSpeakerLastContactAttribute()
    {
        return DateFormats::excelDate(data_get($this->program->speaker_communications_last, 'created_at'));
    }

    /**
     * Closest follow up for the speaker
     *
     * @return float | null
     */
    protected function getSpeakerFollowUpAttribute()
    {
        return DateFormats::excelDate($this->program->primarySpeakers->min('follow_up'));
    }

    /**
     * Primary Speaker
     *
     * @return \Betta\Models\ProgramSpeaker | null
     */
    protected function getPrimarySpeakerAttribute()
    {
        return $this->program->primarySpeakers->first();
    }

    /**
     * Yes if Primary Speaker is contracted, no otherwise
     *
     * @return string
     */
    protected function getContractedAttribute()
    {
        if($profile = data_get($this->primary_speaker, 'profile')){
            return  $profile->hasValidProgramContract($this->program) ? 'Yes' : 'No';
        }
        return '-';
    }

    /**
     * Yes if Primary Speaker is trained, no otherwise
     *
     * @return float | null
     */
    protected function getTrainedAttribute()
    {
        if($profile = data_get($this->primary_speaker, 'profile')){
            return $profile->hasValidProgramTraining($this->program) ? 'Yes' : 'No';
        }
        return '-';
    }

    /**
     * Program Manager Name
     *
     * @return string | null
     */
    protected function getProgramManagerAttribute()
    {
        return data_get($this->program->primary_pm, 'preferred_name');
    }
}
