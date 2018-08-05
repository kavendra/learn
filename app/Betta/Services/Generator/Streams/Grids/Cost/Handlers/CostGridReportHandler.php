<?php

namespace Betta\Services\Generator\Streams\Grids\Cost\Handlers;

use Betta\Models\Brand;
use Betta\Models\Program;
use Betta\Services\Generator\Foundation\AbstratRowHandler as Handler;

class CostGridReportHandler extends Handler
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
        'Brand Label',
        'Speaker Bureau',
        'Brand Primary',
        'Brand Contribution',
        'Program Type',
        'Program Date',
        'Program Time',
        'Program Timezone',
        'Program Status',
        'Program On Hold',
        'Reconciled',
        'Region',
        'Area',
        'Territory',
        'Representative Name',
        'District Manager Name',
        'Presentation Topic',
        'Target Attendees',
        'Total Attendance',
        'Attended Rep',
        'Location Name',
        'Location Address Line 1',
        'Location Address Line 2',
        'Location Address City',
        'Location Address State Province',
        'Location Address Postal Code',
        'Location Contact Phone',
        'Speaker Status',
        'Speaker Name',
        'Speaker Degree',
        'Speaker Horizon Id',
        'Speakers Other Confirmed',
        'Speakers Other Status',
        'Honorarium Cost Estimate',
        'Expense Cost Estimate',
        'Air Cost Estimate',
        'Train Cost Estimate',
        'Car Cost Estimate',
        'Hotel Cost Estimate',
        'AV Cost Estimate',
        'FB Cost Estimate',
        'Room Rental Estimate',
        'Room Rental - Unmet Estimate',
        'Base Fee Cost Estimate',
        'Invitations Cost Estimate',
        'Other Fee Cost Estimate',
        'Other Expense Estimate',
        'Total Estimated Cost',
        'Honorarium Cost Actual',
        'Expense Cost Actual',
        'Air Cost Actual',
        'Train Cost Actual',
        'Car Cost Actual',
        'Hotel Cost Actual',
        'AV Cost Actual',
        'FB Cost Actual',
        'Room Rental Actual',
        'Room Rental - Unmet Actual',
        'Base Fee Cost Actual',
        'Other Fee Cost Actual',
        'Invitation Cost Actual',
        'Shipping Cost Actual',
        'Other Expense Actual',
        'Total Actual Cost',
        'Final Guarantee Number',
        'FB Per Person',
        'Program Manager',
        'Budgets Used',
    ];

    /**
     * Helper values hidden from public array
     *
     * @var array
     */
    protected $hidden = [
        'field',
        'manager',
        'director',
        'address',
        'primary_speaker',
        'other_confirmed_speakers',
        'honorarium_category_costs',
        'expense_speaker_costs',
        'travel_air_costs',
        'travel_train_costs',
        'travel_car_costs',
        'travel_hotel_costs',
        'av_category_costs',
        'fb_costs',
        'room_rental_costs',
        'room_rental_unmet_costs',
        'base_fee_category_costs',
        'other_fee_category_costs',
        'invite_costs',
        'shipping_costs',
        'other_expense_category_costs',
    ];

    /**
     * Create new Row instance
     *
     * @param Program $program
     */
    public function __construct(Program $program, Brand $brand)
    {
        $this->program = $program;
        $this->brand = $brand;
    }

    /**
     * Program ID
     *
     * @return string | null
     */
    public function getProgramIDAttribute()
    {
        return $this->program->id;
    }

    /**
     * Brand Label
     *
     * @return string | null
     */
    public function getBrandLabelAttribute()
    {
        return $this->program->brands->implode('label', ', ');
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
     * Yes if Brand id Primary, otherwise No
     *
     * @return string | null
     */
    public function getBrandPrimaryAttribute()
    {
        return $this->boolString($this->brand->pivot->is_primary);
    }

    /**
     * Contributon of the Brand
     *
     * @return string | null
     */
    public function getBrandContributionAttribute()
    {
        return $this->brand->pivot->contribution;
    }

    /**
     * Program Label
     *
     * @return string | null
     */
    public function getProgramTypeAttribute()
    {
        return $this->program->program_type_label;
    }

    /**
     * Formatted Date of the Program
     *
     * @return string | null
     */
    public function getProgramDateAttribute()
    {
        return excel_date($this->program->start_date);
    }

    /**
     * Formatted Time of the Program (formatting is done by Excel)
     *
     * @return string | null
     */
    public function getProgramTimeAttribute()
    {
        return excel_date($this->program->start_date);
    }

    /**
     * Program Timezone
     *
     * @return string | null
     */
    public function getProgramTimezoneAttribute()
    {
        return $this->program->timezone_label;
    }

    /**
     * Program Status
     *
     * @return string | null
     */
    public function getProgramStatusAttribute()
    {
        return $this->program->status_label;
    }

    /**
     * Yes of the Program is on hold, otherwise No
     *
     * @return string | null
     */
    public function getProgramOnHoldAttribute()
    {
        return $this->boolString($this->program->is_on_hold);
    }

    /**
     * Yes if the Progtram is reconciled, otherwise No
     *
     * @return string | null
     */
    public function getReconciledAttribute()
    {
        return $this->boolString($this->program->is_reconciled);
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
     * Program Primary Field's Manager
     *
     * @access hidden
     * @return Profile | null
     */
    public function getManagerAttribute()
    {
        return data_get($this->field, 'parent');
    }

    /**
     * Program Primary Field's Manager' Manager
     *
     * @access hidden
     * @return Profile | null
     */
    public function getDirectorAttribute()
    {
        return data_get($this->manager, 'parent');
    }

    /**
     * Region of the Primary Field's Director
     *
     * @return string | null
     */
    public function getRegionAttribute()
    {
        return data_get($this->director, 'territory.account_territory_id');
    }

    /**
     * Area of the Primary Field' Manager
     *
     * @return string | null
     */
    public function getAreaAttribute()
    {
        return data_get($this->manager, 'territory.account_territory_id');
    }

    /**
     * Territory of the Primary Field
     *
     * @return string | null
     */
    public function getTerritoryAttribute()
    {
        return data_get($this->field, 'territory.account_territory_id');
    }

    /**
     * Primary Field's Name
     *
     * @return string | null
     */
    public function getRepresentativeNameAttribute()
    {
        return data_get($this->field, 'preferred_name');
    }

    /**
     * Primary Field's Name' Manager' name
     *
     * @return string | null
     */
    public function getDistrictManagerNameAttribute()
    {
        return data_get($this->field, 'manager.preferred_name');
    }

    /**
     * Program Title
     *
     * @return string | null
     */
    public function getPresentationTopicAttribute()
    {
        return $this->program->title;
    }

    /**
     * Audience Types
     *
     * @return string | null
     */
    public function getTargetAttendeesAttribute()
    {
        return $this->program->audienceTypes->implode('label', ', ');
    }

    /**
     * Total Attendee count
     *
     * @return string | null
     */
    public function getTotalAttendanceAttribute()
    {
        return $this->program->attendee_count_hcp;
    }

    /**
     * Attendnace Field
     *
     * @return string | null
     */
    public function getAttendedRepAttribute()
    {
        return $this->program->attendee_count_field;
    }

    /**
     * Return The Location Address
     *
     * @return Address
     */
    public function getAddressAttribute()
    {
        return $this->program->address;
    }

    /**
     * Location: Name
     *
     * @return string | null
     */
    public function getLocationNameAttribute()
    {
        return data_get($this->address, 'name');
    }

    /**
     * Location: Line 1
     *
     * @return string | null
     */
    public function getLocationAddressLine1Attribute()
    {
        return data_get($this->address, 'line_1');
    }

    /**
     * Location: Line 2
     *
     * @return string | null
     */
    public function getLocationAddressLine2Attribute()
    {
        return data_get($this->address, 'line_2');
    }

    /**
     * Location: City
     *
     * @return string | null
     */
    public function getLocationAddressCityAttribute()
    {
        return data_get($this->address, 'city');
    }

    /**
     * Location: State
     *
     * @return string | null
     */
    public function getLocationAddressStateProvinceAttribute()
    {
        return data_get($this->address, 'state_province');
    }

    /**
     * Location: ZIP
     *
     * @return string | null
     */
    public function getLocationAddressPostalCodeAttribute()
    {
        return data_get($this->address, 'postal_code');
    }

    /**
     * LocactionL Phone
     *
     * @return string | null
     */
    public function getLocationContactPhoneAttribute()
    {
        return data_get($this->program, 'primary_location.phone');
    }

    /**
     * Get the Primary Speaker
     *
     * @access hidden
     * @return ProgramSpeaker | null
     */
    public function getPrimarySpeakerAttribute()
    {
        return $this->program
                    ->primary_speakers
                    ->filter(function($speaker){
                        return $speaker->brand_id == $this->brand->id
                           AND $speaker->is_primary;
                    })
                    ->first();
    }

    /**
     * Get the Primary Speaker
     *
     * @access hidden
     * @return Collection
     */
    public function getOtherConfirmedSpeakersAttribute()
    {
        return $this->program
                    ->confirmed_speakers
                    ->filter(function($speaker){
                        return $speaker->brand_id == $this->brand->id;
                    });
    }

    /**
     * Primary Speaker: Status
     *
     * @return string | null
     */
    public function getSpeakerStatusAttribute()
    {
        return data_get($this->primary_speaker, 'status_label');
    }

    /**
     * Primary Speaker: Name
     *
     * @return string | null
     */
    public function getSpeakerNameAttribute()
    {
        return data_get($this->primary_speaker, 'preferred_name');
    }

    /**
     * Primary Speaker: Degree
     *
     * @return string | null
     */
    public function getSpeakerDegreeAttribute()
    {
        return data_get($this->primary_speaker, 'profile.hcpProfile.degree');
    }

    /**
     * Primary Speaker: CMID
     *
     * @return string | null
     */
    public function getSpeakerHorizonIdAttribute()
    {
        return data_get($this->primary_speaker, 'profile.customer_master_id');
    }

    /**
     * Other Confirmed Speakers: Name
     *
     * @return string | null
     */
    public function getSpeakersOtherConfirmedAttribute()
    {
        return $this->other_confirmed_speakers->implode('preferred_name', ', ');
    }

    /**
     * Other Confirmed Speakers: Status Label
     *
     * @return string | null
     */
    public function getSpeakersOtherStatusAttribute()
    {
        return $this->other_confirmed_speakers->implode('status_label', ', ');
    }

    /**
     * Honorarium Costs from Program
     *
     * @access hidden
     * @return Collection
     */
    public function getHonorariumCategoryCostsAttribute()
    {
        return $this->program->honorariumCategoryCosts;
    }

    /**
     * Expense Speaker Costs from Program
     *
     * @access hidden
     * @return Collection
     */
    public function getExpenseSpeakerCostsAttribute()
    {
        return $this->program->expense_speaker_costs;
    }

    /**
     * Program Travel Costs: Air
     *
     * @access hidden
     * @return Collection
     */
    public function getTravelAirCostsAttribute()
    {
        return $this->program->travel_air_costs;
    }

    /**
     * Program Travel Costs: Train
     *
     * @access hidden
     * @return Collection
     */
    public function getTravelTrainCostsAttribute()
    {
        return $this->program->travel_train_costs;
    }

    /**
     * Program Travel Costs: Car
     *
     * @access hidden
     * @return Collection
     */
    public function getTravelCarCostsAttribute()
    {
        return $this->program->travel_car_costs;
    }

    /**
     * Program Travel Costs: Hotel
     *
     * @access hidden
     * @return Collection
     */
    public function getTravelHotelCostsAttribute()
    {
        return $this->program->travel_hotel_costs;
    }

    /**
     * Estiamted Costs: Total Hono
     *
     * @return string | null
     */
    public function getHonorariumCostEstimateAttribute()
    {
        return $this->honorarium_category_costs->sum('estimate');
    }

    /**
     * Estiamted Costs: Expense
     *
     * @return string | null
     */
    public function getExpenseCostEstimateAttribute()
    {
        return $this->expense_speaker_costs->sum('estimate');
    }


    /**
     * Estiamted Costs: Air Travel
     *
     * @return string | null
     */
    public function getAirCostEstimateAttribute()
    {
        return $this->travel_air_costs->sum('estimate');
    }

    /**
     * Estiamted Costs: Train Total
     *
     * @return string | null
     */
    public function getTrainCostEstimateAttribute()
    {
        return $this->travel_train_costs->sum('estimate');
    }


    /**
     * Estiamted Costs: Car Travel
     *
     * @return string | null
     */
    public function getCarCostEstimateAttribute()
    {
        return $this->travel_car_costs->sum('estimate');
    }

    /**
     * Estiamted Costs: Hoel
     *
     * @return string | null
     */
    public function getHotelCostEstimateAttribute()
    {
        return $this->travel_hotel_costs->sum('estimate');
    }

    /**
     * Resolve AV Costs from Progran
     *
     * @access hidden
     * @return Collection
     */
    public function getAvCategoryCostsAttribute()
    {
        return$this->program->av_category_costs;
    }

    /**
     * Estiamte AV Costs
     *
     * @return string | null
     */
    public function getAVCostEstimateAttribute()
    {
        return $this->av_category_costs->sum('estimate');
    }

    /**
     * Resolve FB Costs from Program
     *
     * @access hidden
     * @return Collection
     */
    public function getFbCostsAttribute()
    {
        return$this->program->fb_costs;
    }

    /**
     * Get FB Costs Eximtate
     *
     * @return string | null
     */
    public function getFBCostEstimateAttribute()
    {
        return $this->fb_costs->sum('estimate');
    }

    /**
     * Resolve Room Rental Costs from Program
     *
     * @access hidden
     * @return Collection
     */
    public function getRoomRentalCostsAttribute()
    {
        return$this->program->room_rental_costs;
    }

    /**
     * Resolve Room Rental Unmet Costs from Program
     *
     * @access hidden
     * @return Collection
     */
    public function getRoomRentalUnmetCostsAttribute()
    {
        return$this->program->room_rental_unmet;
    }

    /**
     * Resolve Room Rental Costs from Program
     *
     * @return Collection
     */
    public function getRoomRentalEstimateAttribute()
    {
        return$this->room_rental_costs->sum('estimate');
    }

    /**
     * Actual Room Rental Costs from Program
     *
     * @return Collection
     */
    public function getRoomRentalActualAttribute()
    {
        return$this->room_rental_costs->sum('real');
    }

    /**
     * Resolve Room Rental Unmet Costs from Program
     *
     * @return Collection
     */
    public function getRoomRentalUnmetEstimateAttribute()
    {
        return$this->room_rental_unmet_costs->sum('estimate');
    }

    /**
     * Actual Room Rental Unmet Costs from Program
     *
     * @return Collection
     */
    public function getRoomRentalUnmetActualAttribute()
    {
        return$this->room_rental_unmet_costs->sum('real');
    }

    /**
     * Resolve Base Fee Costs from Program
     *
     * @access hidden
     * @return Collection
     */
    public function getBaseFeeCategoryCostsAttribute()
    {
        return$this->program->base_fee_category_costs;
    }

    /**
     * Eatime Base Fee costs
     *
     * @return string | null
     */
    public function getBaseFeeCostEstimateAttribute()
    {
        return $this->base_fee_category_costs->sum('estimate');
    }


    /**
     * Resolve Invitation Costs from Program
     *
     * @access hidden
     * @return Collection
     */
    public function getInvitationsCostEstimateAttribute()
    {
        return$this->program->invite_costs->sum('estimate');
    }

    /**
     * Resolve Other Fee Costs from Program
     *
     * @access hidden
     * @return Collection
     */
    public function getOtherFeeCategoryCostsAttribute()
    {
        return$this->program->other_fee_category_costs;
    }

    /**
     * Estiamte Other Fees costs
     *
     * @return string | null
     */
    public function getOtherFeeCostEstimateAttribute()
    {
        return $this->other_fee_category_costs->sum('estimate');
    }

    /**
     * Resolve Other Expense Costs from Program
     *
     * @access hidden
     * @return Collection
     */
    public function getOtherExpenseCategoryCostsAttribute()
    {
        return$this->program->other_expense_category_costs;
    }

    /**
     * Estimate other epxense categories
     *
     * @return string | null
     */
    public function getOtherExpenseEstimateAttribute()
    {
        return $this->other_expense_category_costs->sum('estimate');
    }

    /**
     * Estimate Total Costs
     *
     * @return string | null
     */
    public function getTotalEstimatedCostAttribute()
    {
        return $this->program->costs->sum('estimate');
    }

    /**
     * Real Honorarium Cost
     *
     * @return string | null
     */
    public function getHonorariumCostActualAttribute()
    {
        return $this->honorarium_category_costs->sum('real');
    }

    /**
     * Real Speaker OOP
     *
     * @return string | null
     */
    public function getExpenseCostActualAttribute()
    {
        return $this->expense_speaker_costs->sum('real');
    }

    /**
     * Real Air Travel Cost
     *
     * @return string | null
     */
    public function getAirCostActualAttribute()
    {
        return $this->travel_air_costs->sum('real');
    }

    /**
     * Real Train Travel Cost
     *
     * @return string | null
     */
    public function getTrainCostActualAttribute()
    {
        return $this->travel_train_costs->sum('real');
    }

    /**
     * Real Travel by Car Cost
     *
     * @return string | null
     */
    public function getCarCostActualAttribute()
    {
        return $this->travel_car_costs->sum('real');
    }

    /**
     * Real Hotel Cost
     *
     * @return string | null
     */
    public function getHotelCostActualAttribute()
    {
        return $this->travel_hotel_costs->sum('real');
    }

    /**
     * Real AV Cost
     *
     * @return string | null
     */
    public function getAVCostActualAttribute()
    {
        return $this->av_category_costs->sum('real');
    }

    /**
     * Real FB Cost
     *
     * @return string | null
     */
    public function getFBCostActualAttribute()
    {
        return $this->fb_costs->sum('real');
    }

    /**
     * Real Base Fee
     *
     * @return string | null
     */
    public function getBaseFeeCostActualAttribute()
    {
        return $this->base_fee_category_costs->sum('real');
    }

    /**
     * Real Other Fees
     *
     * @return string | null
     */
    public function getOtherFeeCostActualAttribute()
    {
        return $this->other_fee_category_costs->sum('real');
    }

    /**
     * Resolve Invitation Costs from Program
     *
     * @access hidden
     * @return Collection
     */
    public function getInvitationCostActualAttribute()
    {
        return$this->program->invite_costs->sum('real');
    }

    /**
     * Resolve Shipping Costs from Program
     *
     * @access hidden
     * @return Collection
     */
    public function getShippingCostActualAttribute()
    {
        return$this->program->shipping_costs->sum('real');
    }



    /**
     * Real Other Expenses
     *
     * @return string | null
     */
    public function getOtherExpenseActualAttribute()
    {
        return $this->other_expense_category_costs->sum('real');
    }

    /**
     * Total Real Cost
     *
     * @return string | null
     */
    public function getTotalActualCostAttribute()
    {
        return $this->program->costs->sum('real');
    }

    /**
     * Location Fianl Guarantee Number
     *
     * @return string | null
     */
    public function getFinalGuaranteeNumberAttribute()
    {
        return data_get($this->program->primary_location,'final_guarantee_number');
    }

    /**
     * FB per Person
     *
     * @return string | null
     */
    public function getFBPerPersonAttribute()
    {
        return $this->program->fb_per_person;
    }

    /**
     * Primary PM
     *
     * @return string | null
     */
    public function getProgramManagerAttribute()
    {
        return data_get($this->program->primary_pm, 'preferred_name');
    }

    /**
     * Implode Names of budgets used
     *
     * @return string | null
     */
    public function getBudgetsUsedAttribute()
    {
        return $this->program->budgetJars->implode('label', ', ');
    }
}
