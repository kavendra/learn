<?php

namespace Betta\Services\Generator\Streams\Grids\Master\Handlers;

use Betta\Models\Brand;
use Betta\Models\Program;
use Betta\Services\Generator\Foundation\AbstratRowHandler;

class RowHandler extends AbstratRowHandler
{
    /**
     * Bind the implementation
     *
     * @var Betta\Models\Program
     */
    protected $program;

    /**
     * Bind the implementation
     *
     * @var Betta\Models\Brand
     */
    protected $brand;

    /**
     * Reportable items
     *
     * @var Array
     */
    protected $keys = [
        'Program ID',
        'Brand Name',
        'Speaker Bureau',
        'Is Primary',
        'Program Type',
        'Associated Conference',
        'Presentation Topic',
        'Additional Topics',
        'Program Status',
        'On Hold',
        'Cancellation Date',
        'Cancellation Reason',
        'Cancellation Notes',
        'Program Date',
        'Program Time',
        'Time zone',
        'Program Date (Option 2)',
        'Program Time (Option 2)',
        'Audience Type',
        'Estimated Attendees',
        'Estimated Field Sales',
        'Territory',
        'Rep Name',
        'Rep Email',
        'District',
        'Mgr Name',
        'RD/NSM Name',
        'Venue Status',
        'Venue Name',
        'Venue Confirmed By',
        'Venue Address Line 1',
        'Venue Address Line 2',
        'Venue City',
        'Venue State',
        'Venue Zip',
        'Venue Phone',
        'Venue Contact',
        'Venue Notes',
        'Catering Required',
        'Caterer Status',
        'Caterer Name',
        'Caterer Confirmed By',
        'Caterer Contact Name',
        'Caterer Contact Phone',
        'Caterer Notes',
        'Primary Speaker Status',
        'Primary Speaker Name',
        'Primary Speaker Confirmed By',
        'Speaker Notes',
        'Contract',
        'Product Training',
        'Compliance Training',
        'Code of Conduct',
        'Travel Policy',
        'Other Speakers',
        'Primary Speaker Honorarium',
        'Speaker Distance',
        'Speaker Travel Required',
        'Speaker Travel: Air',
        'Speaker Travel: Train',
        'Speaker Travel: Car',
        'Speaker Travel: Car Notes',
        'Speaker Travel: Rep to drive speaker',
        'Speaker Travel: Hotel',
        'Speaker Travel: Notes',
        'AV Requested',
        'AV Status',
        'AV Comments',
        'Total Attended HCP',
        'Total Attended Field',
        'Invitations',
        'Invitation QTY',
        'Reconciled',
        'Program Created By',
        'Program Submitted At',
        'Program Approved At',
        'Program Manager',
    ];

    /**
     * Helper values hidden from public array
     *
     * @var array
     */
    protected $hidden = [
        'av',
        'primary_field',
        'location',
        'address',
        'caterer',
        'primary_speaker',
        'program_invitation',
        'confirmed_speakers',
    ];

    /**
     * Create new Row instance
     *
     * @param Program $nomination
     * @param Brand $nomination
     */
    public function __construct(Program $program, Brand $brand)
    {
        $this->brand = $brand;
        $this->program = $program;
    }


    /**
     * Resolve Program ID
     *
     * @return int
     */
    public function getProgramIDAttribute()
    {
        return $this->program->id;
    }

    /**
     * Resolve Brand Name
     *
     * @return string
     */
    public function getBrandNameAttribute()
    {
        return $this->brand->label;
    }

    /**
     * Resolve Speaker Bureau
     *
     * @return string
     */
    public function getSpeakerBureauAttribute()
    {
        return data_get($this->program, 'speakerBureau.label');
    }

    /**
     * Resolve is the Brand Primary
     *
     * @return string
     */
    public function getIsPrimaryAttribute()
    {
        return $this->boolString( $this->brand->pivot->is_primary );
    }

    /**
     * Program Type Label
     *
     * @return string
     */
    public function getProgramTypeAttribute()
    {
        return $this->program->program_type_label;
    }

    /**
     * Resolve Associated Conference
     *
     * @return string
     */
    public function getAssociatedConferenceAttribute()
    {
        return $this->program->associated_conference;
    }

    /**
     * Primary topic
     *
     * @return string
     */
    public function getPresentationTopicAttribute()
    {
        return $this->program->title;
    }

    /**
     * Resolve Additional topics
     *
     * @return string
     */
    public function getAdditionalTopicsAttribute()
    {
        return $this->program->presentations->where('pivot.is_primary', false)->implode('title', ', ');
    }

    /**
     * Status Label
     *
     * @return string
     */
    public function getProgramStatusAttribute()
    {
        return $this->program->status_label;
    }

    /**
     * Is the Status on Hold
     *
     * @return
     */
    public function getOnHoldAttribute()
    {
        return $this->boolString($this->program->is_on_hold);
    }

    /**
     * Resolve Latet cancelation Date
     *
     * @return float | void
     */
    public function getCancellationDateAttribute()
    {
        if($date = $this->program->cancellation_date){
            return excel_date($date);
        }
    }

    /**
     * Resolve Latest Cancellation Reaons
     *
     * @return string | null
     */
    public function getCancellationReasonAttribute()
    {
        return data_get($this->program, 'latest_cancellationt.reason');
    }

    /**
     * Resolve Notes from cancellation
     *
     * @return string
     */
    public function getCancellationNotesAttribute()
    {
        return $this->program->cancellation_notes;
    }

    /**
     * Start Date
     *
     * @return float
     */
    public function getProgramDateAttribute()
    {
        return excel_date($this->program->start_date);
    }

    /**
     * Resolve Time
     *
     * @return float
     */
    public function getProgramTimeAttribute()
    {
        return excel_date($this->program->start_date);
    }

    /**
     * Timezone label
     *
     * @return string
     */
    public function getTimezoneAttribute()
    {
        return $this->program->timezone_label;
    }

    /**
     * Alternative Date
     *
     * @return float
     */
    public function getProgramDateOption2Attribute()
    {
        if($date = $this->program->alternative_date){
            return excel_date($date);
        }

        return null;
    }

    /**
     * Alternative Date: (report will format as time)
     *
     * @return float
     */
    public function getProgramTimeOption2Attribute()
    {
        if($date = $this->program->alternative_date){
            return excel_date($date);
        }

        return null;
    }

    /**
     * Resolve Audience Types
     *
     * @return string
     */
    public function getAudienceTypeAttribute()
    {
        return $this->program->audienceTypes->implode('label', ', ');
    }

    /**
     * Estaimted HCPs
     *
     * @return int
     */
    public function getEstimatedAttendeesAttribute()
    {
        return $this->program->attendee_count_hcp;
    }

    /**
     * Estimate Field
     *
     * @return int
     */
    public function getEstimatedFieldSalesAttribute()
    {
        return $this->program->attendee_count_field;
    }

    /**
     * Resolve Territory, if provided
     *
     * @return string | null
     */
    public function getTerritoryAttribute()
    {
        return data_get($this->program->territory, 'account_territory_id');
    }

    /**
     * Resolve the Profile of the Primary Field
     *
     * @access hidden
     * @return Profile | null
     */
    public function getPrimaryFieldAttribute()
    {
        return $this->program->primary_field;
    }

    /**
     * Resolve Preferred Name of the Field
     *
     * @return string
     */
    public function getRepNameAttribute()
    {
        return data_get($this->primary_field, 'preferred_name');
    }

    /**
     * Primary Field email
     *
     * @return string
     */
    public function getRepEmailAttribute()
    {
        return data_get($this->primary_field, 'email');
    }

    /**
     * Parent Territory ID
     *
     * @return string | null
     */
    public function getDistrictAttribute()
    {
        return data_get($this->primary_field, 'parent.account_territory_id');
    }

    /**
     * Parent's Name
     *
     * @return string | null
     */
    public function getMgrNameAttribute()
    {
        return data_get($this->primary_field, 'parent.preferred_name');
    }

    /**
     * Grand-Parent Name
     *
     * @return
     */
    public function getRDNSMNameAttribute()
    {
        return data_get($this->primary_field, 'parent.parent.preferred_name');
    }

    /**
     * Primary Program Location
     *
     * @access hidden
     * @return ProgramLocation
     */
    public function getLocationAttribute()
    {
        return $this->program->primary_location;
    }

    /**
     * Primary Program Location' Address
     *
     * @access hidden
     * @return Address | null
     */
    public function getAddressAttribute()
    {
        return data_get($this->location, 'address');
    }

    /**
     * Resolve the status
     *
     * @return string | null
     */
    public function getVenueStatusAttribute()
    {
        return data_get($this->location, 'status_label');
    }

    /**
     * Resolve
     *
     * @return
     */
    public function getVenueNameAttribute()
    {
        return data_get($this->location, 'name');
    }

    /**
     * Resolve
     *
     * @return
     */
    public function getVenueConfirmedByAttribute()
    {
        return data_get($this->location, 'confirmed_by');
    }

    /**
     * Resolve
     *
     * @return
     */
    public function getVenueAddressLine1Attribute()
    {
        return data_get($this->address, 'line_1');
    }

    /**
     * Resolve
     *
     * @return
     */
    public function getVenueAddressLine2Attribute()
    {
        return data_get($this->address, 'line_2');
    }

    /**
     * Resolve
     *
     * @return
     */
    public function getVenueCityAttribute()
    {
        return data_get($this->address, 'city');
    }

    /**
     * Resolve
     *
     * @return
     */
    public function getVenueStateAttribute()
    {
        return data_get($this->address, 'state_province');
    }

    /**
     * Resolve
     *
     * @return
     */
    public function getVenueZipAttribute()
    {
        return data_get($this->address, 'postal_code');
    }

    /**
     * Resolve
     *
     * @return
     */
    public function getVenuePhoneAttribute()
    {
        return data_get($this->location, 'contact_phone');
    }

    /**
     * Resolve
     *
     * @return
     */
    public function getVenueContactAttribute()
    {
        return data_get($this->location, 'contact_name');
    }

    /**
     * Resolve
     *
     * @return
     */
    public function getVenueNotesAttribute()
    {
        if($notes = data_get($this->location, 'notes')){
            return $notes->implode('content', "; \r\n");
        }
    }

    /**
     * Resolve
     *
     * @return
     */
    public function getCateringRequiredAttribute()
    {
        return $this->boolString($this->program->requires_catering);
    }

    /**
     * Primary Program Caterer
     *
     * @access hidden
     * @return ProgramCaterer | null
     */
    public function getCatererAttribute()
    {
        return $this->program->primary_caterer;
    }

    /**
     * Resolve
     *
     * @return
     */
    public function getCatererStatusAttribute()
    {
        return data_get($this->caterer, 'status_label' );
    }

    /**
     * Resolve
     *
     * @return
     */
    public function getCatererNameAttribute()
    {
        return data_get($this->caterer, 'name' );
    }

    /**
     * Resolve
     *
     * @return
     */
    public function getCatererConfirmedByAttribute()
    {
        return data_get($this->caterer, 'confirmed_by' );
    }

    /**
     * Resolve
     *
     * @return
     */
    public function getCatererContactNameAttribute()
    {
        return data_get($this->caterer, 'contact_name' );
    }

    /**
     * Resolve
     *
     * @return
     */
    public function getCatererContactPhoneAttribute()
    {
        return data_get($this->caterer, 'contact_phone' );
    }

    /**
     * Resolve
     *
     * @return
     */
    public function getCatererNotesAttribute()
    {
        if($notes = data_get($this->caterer, 'notes')){
            return $notes->implode('content', "; \r\n");
        }
    }

    /**
     * Resolve Primary Speaker
     *
     * @access hidden
     * @return ProgramSpeaker
     */
    public function getPrimarySpeakerAttribute()
    {
        return $this->program->primarySpeakers->where('brand_id', $this->brand->id)->first();
    }


    /**
     * Code of Conduct of the Primary Speaker
     *
     * @return string | null
     */
    public function getCodeofConductAttribute()
    {

        $attestations = data_get($this->primary_speaker, 'profile.attestations');
        $codeOfConduct = ($attestations) ? $attestations->where('id', 1)->first() : null;
        return $this->boolString($codeOfConduct);
    }

    /**
     * Travel Policy of the Primary Speaker
     *
     * @return string | null
     */
    public function getTravelPolicyAttribute()
    {
        $attestations = data_get($this->primary_speaker, 'profile.attestations');
        $travelPolicy = ($attestations) ? $attestations->where('id', 2)->first() : null;
        return $this->boolString($travelPolicy);
    }

    /**
     * Other Confirmed Speakers
     *
     * @access hidden
     * @return ProgramSpeaker
     */
    public function getConfirmedSpeakersAttribute()
    {
        return $this->program->confirmed_speakers->where('brand_id', $this->brand->id);
    }

    /**
     * Status of the Primary Speaker
     *
     * @return string | null
     */
    public function getPrimarySpeakerStatusAttribute()
    {
        return data_get($this->primary_speaker, 'status_label');
    }

    /**
     * Name of the Primary Speaker
     *
     * @return string | null
     */
    public function getPrimarySpeakerNameAttribute()
    {
        return data_get($this->primary_speaker, 'preferred_name');
    }

    /**
     * Who confirmed the Primary Speaker
     *
     * @return string | null
     */
    public function getPrimarySpeakerConfirmedByAttribute()
    {
        return data_get($this->primary_speaker, 'confirmed_by');
    }

    /**
     * Speaker Notes
     *
     * @return string | null
     */
    public function getSpeakerNotesAttribute()
    {
        if($notes = data_get($this->primary_speaker, 'notes')){
            return $notes->implode('content', "; \r\n");
        }
    }

    /**
     * Contracted is the Valis Program Contract is present
     *
     * @return string | null
     */
    public function getContractAttribute()
    {
        if($this->primary_speaker AND $this->primary_speaker->profile){
            return $this->primary_speaker->profile->hasValidProgramContract($this->program) ? 'Contracted' : '';
        }
    }

    /**
     * Trained is the Valid Program Training is present
     *
     * @return string | null
     */
    public function getProductTrainingAttribute()
    {
        if($this->primary_speaker AND $this->primary_speaker->profile){
            return $this->primary_speaker->profile->hasValidProgramTraining($this->program) ? 'Trained' : '';
        }
    }

    /**
     * Completed if the Compliance Training is present
     *
     * @return string | null
     */
    public function getComplianceTrainingAttribute()
    {
        if($this->primary_speaker AND $this->primary_speaker->profile){
            return $this->primary_speaker->profile->hasValidComplianceTrainingAt($this->program->start_date) ? 'Completed' : '';
        }
    }

    /**
     * Other Speaker
     *
     * @return string | null
     */
    public function getOtherSpeakersAttribute()
    {
        if ($this->confirmed_speakers){
            return $this->confirmed_speakers->implode('preferred_name', ', ');
        }
    }

    /**
     * Honorarium, calcualted
     *
     * @return float | null
     */
    public function getPrimarySpeakerHonorariumAttribute()
    {
        return (float) data_get($this->primary_speaker, 'honoraria.calculated');
    }

    /**
     * Driving distance between Speaker's Preferred Address and Location
     *
     * @return float
     */
    public function getSpeakerDistanceAttribute()
    {
        return (float) data_get($this->primary_speaker, 'driving_distance');
    }

    /**
     * Travel needs label
     *
     * @return string
     */
    public function getSpeakerTravelRequiredAttribute()
    {
        return data_get($this->primary_speaker, 'is_travel_required_label');
    }

    /**
     * Yes if Travel By Air is neede
     *
     * @return string
     */
    public function getSpeakerTravelAirAttribute()
    {
        return data_get($this->primary_speaker, 'is_travel_air_required_label');
    }

    /**
     * Yes if travel by train is neede
     *
     * @return string
     */
    public function getSpeakerTravelTrainAttribute()
    {
        return data_get($this->primary_speaker, 'is_travel_train_required_label');
    }

    /**
     * Yes if the Travel by Car is neede
     *
     * @return string
     */
    public function getSpeakerTravelCarAttribute()
    {
        return data_get($this->primary_speaker, 'is_travel_car_required_label');
    }

    /**
     * Can Travel Notes
     *
     * @return string
     */
    public function getSpeakerTravelCarNotesAttribute()
    {
        return data_get($this->primary_speaker, 'travel_car_notes');
    }

    /**
     * Yes if the reo will drive the Speaker
     *
     * @return string
     */
    public function getSpeakerTravelRepToDriveSpeakerAttribute()
    {
        return data_get($this->primary_speaker, 'is_travel_rep_to_drive_speaker_required_label');
    }

    /**
     * Yes oif Hotel Travel  is required
     *
     * @return string | null
     */
    public function getSpeakerTravelHotelAttribute()
    {
        return data_get($this->primary_speaker, 'is_travel_lodging_required_label');
    }

    /**
     * Travel Notes for the speaker
     *
     * @return string
     */
    public function getSpeakerTravelNotesAttribute()
    {
        return data_get($this->primary_speaker, 'travel_notes');
    }

    /**
     * Resolve the AV from the Program
     *
     * @return Program AV | null
     */
    public function getAvAttribute()
    {
        return $this->program->av;
    }

    /**
     * AV requires tatus label
     *
     * @return string
     */
    public function getAVRequestedAttribute()
    {
        return data_get($this->av, 'is_av_required_label');
    }

    /**
     * AV Status label
     *
     * @return string | null
     */
    public function getAVStatusAttribute()
    {
        return data_get($this->av, 'status_label');
    }

    /**
     * AV Comments
     *
     * @return string | null
     */
    public function getAVCommentsAttribute()
    {
        if($notes = data_get($this->av, 'notes')){
            return $notes->implode('content', "; \r\n");
        }
    }

    /**
     * Count of HCP attendees
     *
     * @return int
     */
    public function getTotalAttendedHCPAttribute()
    {
        return $this->program->hcp_registrations->sum('attended');
    }

    /**
     * Count of Field Attendees
     *
     * @return int
     */
    public function getTotalAttendedFieldAttribute()
    {
        return $this->program->field_registrations->sum('attended');
    }

    /**
     * Resolve the Program Invitation
     *
     * @access hidden
     * @return Program Invitation | null
     */
    public function getProgramInvitationAttribute()
    {
        return $this->program->programInvitation;
    }

    /**
     * Yes if the Invtiations has been requested
     *
     * @return string
     */
    public function getInvitationsAttribute()
    {
        return $this->boolString(data_get($this->program_invitation, 'is_invitation_required'));
    }

    /**
     * Quantity of Invitations
     *
     * @return int | null
     */
    public function getInvitationQTYAttribute()
    {
        return data_get($this->program_invitation, 'invitation_quantity');
    }

    /**
     * Is Program Reconciled
     *
     * @return string
     */
    public function getReconciledAttribute()
    {
        return $this->boolString($this->program->is_reconciled);
    }

    /**
     * Who created the Program
     *
     * @return string | null
     */
    public function getProgramCreatedByAttribute()
    {
        return data_get($this->primary_speaker, 'createdBy.preferred_name');
    }

    /**
     * When the program was submitted
     *
     * @return float | void
     */
    public function getProgramSubmittedAtAttribute()
    {
        if($date = $this->program->was_submitted){
            return excel_date($date->created_at);
        }
    }

    /**
     * When the Program was approved
     *
     * @return float | void
     */
    public function getProgramApprovedAtAttribute()
    {
        if($date = $this->program->was_approved){
            return excel_date($date->created_at);
        }
    }

    /**
     * Program Manager
     *
     * @return string | null
     */
    public function getProgramManagerAttribute()
    {
        return data_get($this->program, 'primary_pm.preferred_name');
    }

}
