<?php

namespace Betta\Services\Generator\Streams\Usage\BusinessReview\Handlers;

use Betta\Models\Program;
use Illuminate\Support\Collection;
use Betta\Services\Generator\Foundation\AbstratRowHandler;

class BusinessReviewRow extends AbstratRowHandler
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
     * @var Betta\Models\Program
     */
    protected $program;

    /**
     * Reportable items
     *
     * @var Array
     */
    protected $keys = [
        'Program ID',
        'Brand',
        'Program Type',
        'Program Title',
        'Program Status',
        'Program Date',
        'Program Time',
        'Timezone',
        'AM Salesforce',
        'AM Name',
        'Department',
        'ASM Name',
        'Venue Status',
        'Venue Name',
        'Venue Confirmed By',
        'Venue City',
        'Venue State',
        'Caterer Required',
        'Caterer Status',
        'Caterer Name',
        'Caterer Confirmed By',
        'Speaker Status',
        'Primary Speaker Name',
        'Primary Speaker Confirmed By',
        'Other Confirmed Speakers',
        'Primary Speaker Driving Distance',
        'Primary Speaker Travel Required',
        'AV Required',
        'Estimated Number of Attendees',
        'Total Attended HCPs',
        'Total Attended Target HCPs',
        'Total Attended Field',
        'Help Recruit',
        'Invitations',
        'Invitation QTY',
        'Reconciled',
        'Program Created By',
        'Program Submitted At',
        'Program Approved At',
        'Program Manager',
        'Program Claimed Date',
        'Program Closeout Date',
        'Number Of Days For Closeout',
        'Number Of Program Closeout Follow Up Reminders Sent',
        'Speaker Confirmed Date',
        'Speaker Flights Confirmed Date',
        'Speaker Hotel Confirmed Date',
        'Speaker Car Confirmed Date',
        'Venue Confirmed Date',
        'Invites Shipped Date',
        'Materials Box Shipped Date',
        'Speaker Check Mailed'
    ];

    /**
     * Helper values hidden from public array
     *
     * @var array
     */
    protected $hidden = [
        'program_brands',
        'field',
        'caterer',
        'speaker',
        'address'
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
     * Get Program ID
     *
     * @return string
     */
    protected function getProgramIdAttribute()
    {
        return $this->program->id;
    }

    /**
     * Get Program Brand
     *
     * @return string
     */
    protected function getProgramBrandsAttribute()
    {
        return $this->program->brands;
    }

    /**
     * Get Program Brand Label
     *
     * @return string
     */
    protected function getBrandAttribute()
    {
        return $this->program_brands->implode('label', ' | ');
    }

    /**
     * Get Program Type
     *
     * @return string
     */
    protected function getProgramTypeAttribute()
    {
        return $this->program->program_type_label;
    }

    /**
     * Get Program Title
     *
     * @return string
     */
    protected function getProgramTitleAttribute()
    {
        return $this->program->title;
    }

    /**
     * Get Program Status
     *
     * @return string
     */
    protected function getProgramStatusAttribute()
    {
        return $this->program->status_label;
    }

    /**
     * Get Program Date
     *
     * @return string
     */
    protected function getProgramDateAttribute()
    {
        return excel_date($this->program->start_date);
    }

    /**
     * Get Program Time
     *
     * @return string
     */
    protected function getProgramTimeAttribute()
    {
        return excel_date($this->program->start_date);
    }

    /**
     * Get Program Timezone
     *
     * @return string
     */
    protected function getTimezoneAttribute()
    {
        return $this->program->timezone->label;
    }

    /**
    * Program Primary Field
    *
    * @access hidden
    * @return Profile | null
    */
    public function getFieldAttribute()
    {
        return $this->program->primary_field;
    }

    /**
     * Get AM Salesforce
     *
     * @return string
     */
    protected function getAmSalesforceAttribute()
    {
        return data_get($this->field, 'preferred_name');
    }

    /**
     * Get AM Name
     *
     * @return string
     */
    protected function getAmNameAttribute()
    {
        return data_get($this->field, 'preferred_name');
    }

    /**
     * Get Department
     *
     * @return string
     */
    protected function getDepartmentAttribute()
    {
        return data_get($this->field, 'primary_territory.label');
    }

    /**
     * Get ASM Name
     *
     * @return string
     */
    protected function getAsmNameAttribute()
    {
        return data_get($this->field, 'parent.preferred_name');
    }

    /**
     * Return The Location Address
     * @access hidden
     * @return Address
     */
    public function getAddressAttribute()
    {
        return $this->program->address;
    }

    /**
     * Get Venue Status
     *
     * @return string
     */
    protected function getVenueStatusAttribute()
    {
        return data_get($this->program, 'primaryLocation.status_label');
    }

    /**
     * Get Venue Name
     *
     * @return string
     */
    protected function getVenueNameAttribute()
    {
        return data_get($this->address, 'name');
    }

    /**
     * Get Venue Confirmed By
     *
     * @return string
     */
    protected function getVenueConfirmedByAttribute()
    {
        return data_get($this->program, 'primaryLocation.confirmed_by');
    }

    /**
     * Get Venue City
     *
     * @return string
     */
    protected function getVenueCityAttribute()
    {
        return data_get($this->address, 'state_province');
    }

    /**
     * Get Venue State
     *
     * @return string
     */
    protected function getVenueStateAttribute()
    {
        return data_get($this->address, 'state');
    }

    /**
    * Program Primary Caterer
    *
    * @access hidden
    * @return Profile | null
    */
    public function getCatererAttribute()
    {
        return $this->program->programCaterers->where('is_primary', true)->first();
    }

    /**
     * Get Caterer Required
     *
     * @return string
     */
    protected function getCatererRequiredAttribute()
    {
        return $this->program->requires_caterings;
    }

    /**
     * Get Caterer Status
     *
     * @return string
     */
    protected function getCatererStatusAttribute()
    {
        return data_get($this->caterer, 'status_label');
    }

    /**
     * Get Caterer Name
     *
     * @return string
     */
    protected function getCatererNameAttribute()
    {
        return data_get($this->caterer, 'name');
    }

    /**
     * Get Caterer Confirmed By
     *
     * @return string
     */
    protected function getCatererConfirmedByAttribute()
    {
        return data_get($this->caterer, 'confirmed_by');
    }

    /**
     * Get Primary Speaker
     *
     * @return string
     */
    protected function getSpeakerAttribute()
    {
        return $this->program->primarySpeakers->first();
    }

    /**
     * Get Speaker Status
     *
     * @return string
     */
    protected function getSpeakerStatusAttribute()
    {
        return data_get($this->speaker, 'status_label');
    }

    /**
     * Get Primary Speaker Name
     *
     * @return string
     */
    protected function getPrimarySpeakerNameAttribute()
    {
        return data_get($this->speaker, 'preferred_name');
    }

    /**
     * Get Primary Speaker Confirmed By
     *
     * @return string
     */
    protected function getPrimarySpeakerConfirmedByAttribute()
    {
        return data_get($this->speaker, 'confirmed_by');
    }

    /**
     * Get Other Confirmed Speakers
     *
     * @return string
     */
    protected function getOtherConfirmedSpeakersAttribute()
    {
        return $this->program->other_confirmed_speakers->implode('profile.preferred_name', ', ');
    }

    /**
     * Get Primary Speaker Driving Distance
     *
     * @return string
     */
    protected function getPrimarySpeakerDrivingDistanceAttribute()
    {
        return data_get($this->speaker, 'driving_distance');
    }

    /**
     * Get Primary Speaker Travel Required
     *
     * @return string
     */
    protected function getPrimarySpeakerTravelRequiredAttribute()
    {
        return data_get($this->speaker, 'is_travel_required_label');
    }

    /**
     * Get AV Required
     *
     * @return string
     */
    protected function getAvRequiredAttribute()
    {
        return $this->boolString($this->program->is_av_requered);
    }

    /**
     * Get Estimated Number of Attendees
     *
     * @return string
     */
    protected function getEstimatedNumberOfAttendeesAttribute()
    {
        return $this->program->total_estimated_attendees;
    }

    /**
     * Get Total Attended HCPs
     *
     * @return string
     */
    protected function getTotalAttendedHcpsAttribute()
    {
        $this->program->attendee_count_hcp;
    }

    /**
     * Get Total Attended Target HCPs
     *
     * @return string
     */
    protected function getTotalAttendedTargetHcpsAttribute()
    {
        $this->program->attendee_count_hcp;
    }

    /**
     * Get Total Attended Field
     *
     * @return string
     */
    protected function getTotalAttendedFieldAttribute()
    {
        $this->program->attendee_count_field;
    }

    /**
     * Get Help Recruit
     *
     * @return string
     */
    protected function getHelpRecruitAttribute()
    {
        return $this->boolString($this->program->help_recruit);
    }

    /**
     * Get Program Invitation
     *
     * @return string
     */
    protected function getProgramInvitationAttribute()
    {
        return $this->program->ProgramInvitation;
    }

    /**
     * Get Invitations
     *
     * @return string
     */
    protected function getInvitationsAttribute()
    {
        return $this->boolString(data_get($this->program, 'programInvitation.is_invitation_required'));
    }

    /**
     * Get Invitation QTY
     *
     * @return string
     */
    protected function getInvitationQtyAttribute()
    {
        return data_get($this->program, 'programInvitation.invitation_quantity');
    }

    /**
     * Get Reconciled
     *
     * @return string
     */
    protected function getReconciledAttribute()
    {
        return $this->boolString($this->program->is_reconciled);
    }

    /**
     * Get Program Created By
     *
     * @return string
     */
    protected function getProgramCreatedByAttribute()
    {

        return data_get($this->program, 'createdBy.preferred_name');
    }

    /**
     * Get Program Submitted At
     *
     * @return string
     */
    protected function getProgramSubmittedAtAttribute()
    {
        return ($this->program->was_submitted) ? excel_date($this->program->was_submitted->created_at): null;
    }

    /**
     * Get Program Approved At
     *
     * @return string
     */
    protected function getProgramApprovedAtAttribute()
    {
        return ($this->program->was_approved) ? excel_date($this->program->was_approved->created_at): null;
    }

    /**
     * Get Program Manager
     *
     * @return string
     */
    protected function getProgramManagerAttribute()
    {
        return data_get($this->program, 'primary_pm.preferred_name');
    }

    /**
     * Get Program Claimed Date
     *
     * @return string
     */
    protected function getProgramClaimedDateAttribute()
    {
        return excel_date(data_get($this->program, 'primary_pm.pivot.created_at'));
    }

    /**
     * Get Program Close Out Date
     *
     * @return string
     */
    protected function getProgramCloseoutDateAttribute()
    {
        return ($this->program->was_closed_out) ? excel_date($this->program->was_closed_out->created_at): null;
    }

    /**
     * Get Number of days between Program date and Close Out date
     *
     * @return string
     */
    protected function getNumberOfDaysForCloseoutAttribute()
    {
        return ($this->program->was_closed_out) ?
            $this->program->was_closed_out->created_at->diffInDays($this->program->start_date) : null;
    }

    /**
     * Get Program Close Out Date
     *
     * @return string
     */
    protected function getNumberOfProgramCloseoutFollowUpRemindersSentAttribute()
    {
        # Communication Context: Betta\Models\Program
        # Communication Recipient: Betta\Models\Profile
        # Get the Primary Field of the program (this is intentional)
        # get the communications where comunication template ids are 12, 13, 14, 15 and 16
        # and above constraints
        $closeoutTemplates = [12,13,14,15,16];
        if(empty($communications = object_get($this->program, 'primary_field.communications')))
            return 0;
        return $communications->whereIn('communication_template_id',$closeoutTemplates)
                                ->where('context_id',$this->program->id)
                                ->where('context_type','Betta\\Models\\Program')
                                ->count();
    }

    /**
     * Get Speaker Confirmed Date
     *
     * @return string
     */
    protected function getSpeakerConfirmedDateAttribute()
    {
        # there may not be a speaker
        $speakerProgressions = data_get($this->speaker, 'progressions');
        # get the last progression
        $progression = ($speakerProgressions) ? $speakerProgressions->where('to_status_id', 5)->last() : null;
        # resolve the date
        return ($progression) ? excel_date($progression->created_at) : null;
    }

    /**
     * Get Speaker Flights Confirmed Date
     *
     * @return string
     */
    protected function getSpeakerFlightsConfirmedDateAttribute()
    {
        $flightTravelProgressions = data_get($this->speaker, 'air_travel.progressions');
        $progression = ($flightTravelProgressions) ? $flightTravelProgressions->where('to_status_id', 5)->last() : null;
        return ($progression) ? excel_date($progression->created_at) : null;

    }

    /**
     * Get Speaker Hotel Confirmed Date
     *
     * @return string
     */
    protected function getSpeakerHotelConfirmedDateAttribute()
    {
        $hotelTravelProgressions = data_get($this->speaker, 'hotel_travel.progressions');
        $progression = ($hotelTravelProgressions) ? $hotelTravelProgressions->where('to_status_id', 5)->last() : null;
        return ($progression) ? excel_date($progression->created_at) : null;
    }

    /**
     * Get Speaker Car Confirmed Date
     *
     * @return string
     */
    protected function getSpeakerCarConfirmedDateAttribute()
    {
        $carTravelProgressions = data_get($this->speaker, 'car_travel.progressions');
        $progression = ($carTravelProgressions) ? $carTravelProgressions->where('to_status_id', 5)->last() : null;
        return ($progression) ? excel_date($progression->created_at) : null;
    }

    /**
     * Get Venue Confirmed Date
     *
     * @return string
     */
    protected function getVenueConfirmedDateAttribute()
    {
        $venueProgressions = data_get($this->program, 'primaryLocation.progressions');
        $progression = ($venueProgressions) ? $venueProgressions->where('to_status_id', 5)->last() : null;
        return ($progression) ? excel_date($progression->created_at) : null;
    }

    /**
     * Get Invites Shipped Date
     *
     * @return string
     */
    protected function getInvitesShippedDateAttribute()
    {
        return excel_date(data_get($this->program, 'first_invitations_shipment.created_at'));
    }

    /**
     * Get Materials Box Shipped Date
     *
     * @return string
     */
    protected function getMaterialsBoxShippedDateAttribute()
    {
        return excel_date(data_get($this->program, 'first_material_shipment.created_at'));
    }

    /**
     * Get Speaker Check Mailed
     *
     * @return string
     */
    protected function getSpeakerCheckMailedAttribute()
    {
        return null;
    }
}
