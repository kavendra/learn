<?php

namespace App\Models;

use Auth;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Input;
use Betta\Foundation\Eloquent\AbstractModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Collective\Html\Eloquent\FormAccessible;

class Conference extends AbstractModel {
    use SoftDeletes;
    use FormAccessible;
    use Traits\CreatedByTrait;
   
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'conference_status_id',
        'booth_size_id',
        'associated_conference',
        'association_name',
        'acronym',
        'label',
        'website',
        'start_date',
        'end_date',
        'registration_due_date',
        'timezone_id',
        'exibitor_start_date',
        'exibitor_end_date',
        'sponsorship_level',
        'exhibitor_fee',
        'expected_attendee_count',
        'is_on_hold',
        'is_reconciled',
        'is_test',
        'is_candy',
        'creator',
        'field_marketing',
        'travel',
        'promotional_materials',
        'booth_maximun_representative',
        'booth_number',
        'parking_information',
        'reception_detail',
        'set_up_date',
        'dismantle_date',
        'sponsorship_description',
        'tier_level',
        'location_contact_name',
        'cost_per_badge',
        'display_type_id',
        'is_lead_retrieval',
        'lead_retrieval_status',
        'lead_retrieval_notes',
        'field_marketing_other',
        'field_marketing_comment',
        'candy_quantity',
        'candy_status',
        'candy_confirmation',
		'candy_tracking',
		'candy_order_date',
		'cancellation_reason_id',
		'cancellation_notes',
        'display_shortcut',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_on_hold'            => 'boolean',
        'is_reconciled'         => 'boolean',
        'is_test'               => 'boolean',
        'field_marketing'       => 'boolean',
        'field_marketing'       => 'boolean',
        'exhibitor_fee'         => 'float',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'start_date',
        'end_date',
        'registration_due_date',
        'exibitor_start_date',
        'exibitor_end_date',
        'set_up_date',
        'dismantle_date',
        'candy_order_date',
        'deleted_at'
    ];

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    //protected $with = ['conferenceStatus'];

    /**
     * Record can have Many AudienceTypes
     *
     * @return Relation
     */
    public function audienceTypes()
    {
        return $this->belongsToMany(AudienceType::class, 'conference_to_audience_type');
    }

	/**
     *  Conference has Booth Size
     *
     * @return Relation
     */
    public function boothSize()
    {
        return $this->belongsTo(BoothSize::class);
    }

    /**
     *  Conference has Display Type
     *
     * @return Relation
     */
    public function DisplayType()
    {
        return $this->belongsTo(DisplayType::class);
    }

    /**
     *  Program exits in Time
     *
     * @return Relation
     */
    public function timezone()
    {
        return $this->belongsTo(Timezone::class);
    }

    /**
     *  Conference has many Brands
     *
     * @return Relation
     */
    public function brands()
    {
        # Pivot Attributes
        $attributes = ['is_primary'];

        return $this->belongsToMany(Brand::class, 'conference_to_brand')
                    ->withPivot($attributes);
    }

    /**
     *  Conference exists in Status
     *
     * @return Relation
     */
    public function conferenceStatus()
    {
        return $this->belongsTo(ConferenceStatus::class);
    }

    /**
     *  Conference exists in ConferenceCancelreason
     *
     * @return Relation
     */
    public function conferenceCancellationReason()
    {
        return $this->belongsTo(ConferenceCancelreason::class, 'cancellation_reason_id');
    }

    /**
     * Record may have many histories
     *
     * @return Relation
     */
    public function histories()
    {
        return $this->hasMany(ConferenceHistory::class);
    }

    /**
     * Conference has Many Housings
     *
     * @return Relation
     */
    public function housings()
    {
        return $this->hasMany(ConferenceHousing::class);
    }

    /**
     * get all housings where source type is Badge and Housing
     *
     * @return Relation
     */
    public function getBadgeHousingsAttribute()
    {
        return $this->housings->filter(function($item){
            return $item->is_badge_housing;
        });
    }

    /**
     * get all housings where source type is Badge Only
     *
     * @return Relation
     */
    public function getBadgeOnlyHousingsAttribute()
    {
        return $this->housings->filter(function($item){
            return $item->is_badge_only;
        });
    }

    /**
     * Record may have many histories
     *
     * @return Relation
     */
    public function contacts()
    {
        # Pivot Attributes
        $attributes = [];
        # Relate
        return $this->belongsToMany(ConferenceContact::class, 'conference_to_contact')
                    ->withPivot($attributes);
    }

    /**
     * Conference has Booth AmenitieA
     *
     * @return Relation
     */
    public function amenities()
    {
        # Pivot Attributes
        $attributes = ['id', 'status', 'other_option'];

        return $this->belongsToMany(BoothAmenitie::class, 'conference_to_booth_amenitie')
                    ->withPivot($attributes);
    }

    /**
     * Conference has Program Managers
     *
     * @return Relation
     */
    public function pms()
    {
        # Pivot Attributes
        $attributes = ['is_primary', 'id', 'is_claim'];

        return $this->belongsToMany(Profile::class, 'conference_to_user')
                    ->withPivot($attributes);
    }

    /**
     * Conference has Program Managers
     *
     * @return Relation
     */
    public function medicalaffairs()
    {
        # Pivot Attributes
        $attributes = ['is_primary', 'id'];

        return $this->belongsToMany(Profile::class, 'conference_to_medicalaffair')
                    ->withPivot($attributes);
    }

	/**
     * Conference has Representative
     *
     * @return Relation
     */
    public function reps()
    {
        # Pivot Attributes
        $attributes = ['is_primary','badge_name','badge_title', 'id', 'badge_status'];

        return $this->belongsToMany(Profile::class, 'conference_to_field')
                    ->withPivot($attributes);
    }

    /**
     * Conference has literatures
     *
     * @return Relation
     */
    public function literatures()
    {
        # Pivot Attributes
		$attributes = [
            'id',
            'material_status',
            'material_quantity',
            'confirmation_number',
            'tracking_number',
            'order_date'
        ];

        return $this->belongsToMany(Literature::class, 'conference_to_literature')
                    ->withPivot($attributes);
    }

    /**
     * Conference has literatures
     *
     * @return Relation
     */
    public function budgetjars()
    {
	    # Pivot attributes
        $attributes = ['id', 'is_primary', 'contribution', 'creator'];

        return $this->belongsToMany(BudgetJar::class, 'conference_to_budget_jar')
                    ->withPivot($attributes)
                    ->withTimestamps();
    }

    /**
     * Record may have one Conferencecloseout
     *
     * @return Relation
     */
    public function conferencecloseout()
    {
        return $this->hasOne(ConferenceCloseout::class);
    }

	/**
     * Conference has Representative
     *
     * @return Relation
     */
    public function fieldmarketing()
    {
        # Pivot Attributes
        $attributes = [];

        return $this->belongsToMany(FieldMarketing::class, 'conference_to_field_marketing')
                    ->withPivot($attributes);
    }

	/**
     * Conference has Many Budget Jrs
     *
     * @return Relation
     */
    public function payments()
    {
        return $this->hasMany(ConferenceToPayment::class);

    }

    /**
     * Record may have many histories
     *
     * @return Relation
     */
    public function lastInvoiceNumber()
    {
        return $this->hasOne(ConferenceInvoice::class)->orderBy('id', 'desc');
    }

    /**
     * Record may have many Invoices
     *
     * @return Relation
     */
    public function ConferenceInvoice()
    {
        return $this->hasMany(ConferenceInvoice::class);
    }

    /**
     * Record may have many Invoices
     *
     * @return Relation
     */
    public function ConferenceInvoiceHistory()
    {
        return $this->hasMany(ConferenceInvoiceHistory::class);
    }

    /**
     * Record may have many Rooms
     *
     * @return Relation
     */
    public function rooms()
    {
        return $this->hasMany(ConferenceRoom::class);
    }

    /**
     * Conference has Many Affiliate Meeting
     *
     * @return Relation
     */
    public function affiliateMeeting()
    {
        return $this->hasMany(ConferenceToAffiliateMeeting::class);

    }



    /**
     * Conference has Nomination
     *
     * @return Relation
     */
    public function nominations()
    {
        # Pivot Attributes
        $attributes = [
            'group_id',
            'is_primary',
            'primary_phone',
            'primary_email',
            'badge_name',
            'badge_title',
            'id',
            'badge_status',
            'conference_nomination_status_id',
            'notes',
            'creator'
        ];

        return $this->belongsToMany(Profile::class, 'conference_to_nomination')
                    ->withPivot($attributes)
                    ->withTimestamps()
                    ->using(ConferenceNominationPivot::class);
    }

    /**
     * Resolve Group nomination Attribute
     *
     * @return string
     */
    public function getGroupNominationsAttribute()
    {
        $profileGroups = Auth()->user()->profile->groups->pluck('id')->toArray();
        if(!Auth()->user()->profile->in_rep_group){
            return $this->nominations;
        }else{
            return $this->nominations->whereIn('pivot.group_id', $profileGroups);
        }
    }




    /**
     * Resolve Group nomination Attribute
     *
     * @return string
     */
    public function getProfileGroupNominationsAttribute()
    {
        $userInfo = Auth()->user();
        $profileGroup = $userInfo->profile->groups();

        if($userInfo->profile->is_group_parent){
            $profileGroup = $profileGroup->notByReferenceName([ProfileGroup::CLIENT_VP]);
        }
        if(!$userInfo->profile->in_rep_group){
            $groups = $userInfo->profile->rep_groups;
            $profileGroup = ProfileGroup::whereIn('reference_name', $groups);
            //$profileGroup = $this->conferencenominations->GroupBy('groupName.label');
        }
        return $profileGroup->with(['nomination' => function($query){
                return $query->where('conference_id', $this->id);
            }])->get();
    }

    /**
     * Fetch Initial Nominations
     *
     * @return string
     */
    public function scopeInitialNominations($query, $groupId)
    {
        return $this->nominations->where('pivot.group_id', $groupId)->where('pivot.conference_nomination_status_id', 1);
    }

    /**
     * Conference can have many registrations
     *
     * @return Relation
     */
    public function registrations()
    {
        return $this->morphMany(Registration::class, 'context');
    }


     /**
     * Conference can have many attendees
     *
     * @return Relation
     */
    public function conferencenominations()
    {
        return $this->hasMany(ConferenceNominationPivot::class);
    }

    /**
     * Conference can have many attendees
     *
     * @return Relation
     */
    public function attendees()
    {
        return $this->hasMany(ConferenceAttendee::class);
    }

    /**
     * Resolve Primary nomination Attribute
     *
     * @return string
     */
    public function getPrimaryNominationAttribute()
    {
        $primary_nominations =  $this->nominations->where('pivot.is_primary', true)->first();

        return $primary_nominations;
    }

    /**
     * Implements StatusTrait interface requirements
     *
     * @return string
     */
    protected function getStatusFieldName()
    {
        return 'conference_status_id';
    }

    /**
    * Filter Test Conferences
    *
    * @param  Builder $query
    * @return Builder
    */
    public function scopeNoTest($query)
    {
        return $query->where('is_test', false);
    }

    /**
     * Scope results in a number of ways
     *
     * @param  Builder $query
     * @param  array  $attributes
     * @return Builder
     */
    public function scopeSearch($query, $attributes = array())
    {
        if ($scope = array_get($attributes, 'scope') ){
            $query->$scope( $attributes );
        }

        return $query;
    }

    /**
     * Return only the items  that are in draft
     *
     * @param  Builder $query
     * @return Builder
     */
    public function scopeTrash($query)
    {
        return $query->where('deleted_at' ,'<>' ,'');
    }


    /**
     * Return only the items  that are in Upcoming
     *
     * @param  Builder $query
     * @return Builder
     */
    public function scopeUpcoming($query)
    {
        return $query->where('conference_status_id', '>', ConferenceStatus::DRAFT)->where('exibitor_start_date', '>', date('Y-m-d'));
    }



    /**
     * Return only the items  that are in Upcoming
     *
     * @param  Builder $query
     * @return Builder
     */
    public function scopeBatchinvoice($query)
    {
        return $query->where('conference_status_id', '>', ConferenceStatus::DRAFT);
    }

    /**
     * Return only the items  that are in Closeout
     *
     * @todo  Review and Remove
     * @param  Builder $query
     * @return Builder
     */
    public function scopeSelfCreated($query, $profile_ids = NULL)
    {
		$user = Auth()->user();

        if(permission('repaccess')){
            $rep_id = $user->profile_id;
			if($profile_ids){
				return $query->where('creator', $rep_id)->orwhereIn('creator', $profile_ids);
			}else{
				return $query->where('creator', $rep_id);
			}
		}elseif($user->profile && $user->profile->groups->count() > 20 && $user->profile->groups->first()->id == 8){
			$brandprofile = $user->profile->confmanagerbrands->pluck('id')->toArray();
			if($brandprofile){
				$brandprofile = array_unique($brandprofile);
			}
			if($brandprofile){
				return $query->join('conference_to_brand', 'conference_to_brand.conference_id', '=', 'conferences.id')->whereIn('conference_to_brand.brand_id', $brandprofile)->groupBY('conferences.id');
			}
		}else{
            return $query;
        }

    }


    /**
     * Scope Conferences Visible  to user
     *
     * @param  Builder $query
     * @param  array|string|int $ids
     * @return Builder
     */
    public function scopeVisible($query, $id)
    {
        $query->where(function($advanced) use($id){
            $advanced->byCreator($id)
                     ->byManager($id, 'orWhereHas');
        });
    }





    /**
     * Scope results to assigned Managers
     *
     * @param  Buidler $query
     * @param  array|string|int $id
     * @return Builder
     */
    public function scopeByManager($query, $id, $method = 'whereHas')
    {
        return $query->$method('pms', function($fieldManager) use($id){
                        $fieldManager->byKey( $id );
                    });
    }

    /**
     * Scope results to users who created
     *
     * @param  Buidler $query
     * @param  array|string|int $id
     * @return Builder
     */
    public function scopeByCreator($query, $id, $method="whereIn")
    {
        return $query->$method('creator', (array) $id);
    }


	/**
     * Scope results in a number of ways
     *
     * @param  Builder $query
     * @param  array  $attributes
     * @return Builder
     */
    public function scopeBymonth($query)
    {

		$cdate = Input::get('cdate');
		$cdate = ($cdate)? $cdate:date('Y-m');

		$cdatearr = explode('-', $cdate);

		$from_s      =  date("Y-m-d", mktime(0, 0, 0, $cdatearr[1], 1, $cdatearr[0]));
		$to_s        =  date("Y-m-d", mktime(0, 0, 0, $cdatearr[1]+1, 0, $cdatearr[0]));

		return $query->where('exibitor_start_date', '>=', $from_s)->where('exibitor_end_date', '<=', $to_s);


    }



    /**
     * Scope results in a number of ways
     *
     * @param  Builder $query
     * @param  array  $attributes
     * @return Builder
     */
    public function getConferenceDateRangeAttribute($query)
    {
        $edn_date = $this->exibitor_end_date ? $this->exibitor_end_date->format("F d, Y") : '';
        return $this->exibitor_start_date->format("F d").' - '.$edn_date;
    }

	public function scopeBydate($query)
    {

		$cdate = Input::get('cdate');
		$cdate = ($cdate)? $cdate:date('Y-m-d');


		return $query->where('exibitor_start_date', $cdate);


    }


    public function getIsMaterialUpdateAttribute($query)
    {

        if($this->exibitor_start_date){
            $c_date = $this->exibitor_start_date->timestamp;
            $n_date = Carbon::now()->addDays(10)->timestamp;

            # if conference date has 10 days from now then return true
            if($c_date > $n_date){
                return true;
            }
        }
        return false;
    }


    public function getFutureDateAttribute($query)
    {

        if($this->exibitor_start_date){
            $c_date = $this->exibitor_start_date->timestamp;
            $n_date = Carbon::now()->timestamp;

            # if conference date has 10 days from now then return true
            if($c_date > $n_date){
                return true;
            }
        }
        return false;
    }


	public function scopeBucket($query)
    {
		return $query->ByManager(Auth::user()->profile_id);
    }

    /*
    Brand Pending Approval Conference list
     */
    public function scopePendingapproval($query)
    {
        $groups = Auth()->user()->profile->groups->pluck('reference_name')->toArray();
        if(in_array('client.conference_manager', $groups) || in_array('client.brand_manager', $groups)){
            return $query->whereIn('conference_status_id', [7, 15]);
        }elseif(in_array('front.director', $groups)){
            # See task https://frictionless.teamwork.com/#tasks/16406364
            if( in_array( Auth()->user()->profile_id , [857, 851]) ) {
                return $query->where(function($q){
                    $q->whereIn('conference_status_id', [7])
                        ->orWhere(function($q){
                            $q->whereIn('conference_status_id', [6])->visible( Auth()->user()->profile->descendants_and_self->pluck('id')->push(Auth()->user()->profile_id)->all() );
                        });
                });
            } else {
                return $query->whereIn('conference_status_id', [6])->visible( Auth()->user()->profile->descendants_and_self->pluck('id')->push(Auth()->user()->profile_id)->all() );
            }
        }elseif(in_array('front.manager', $groups)) {
            return $query->whereIn('conference_status_id', [2])->visible( Auth()->user()->profile->descendants_and_self->pluck('id')->all() );
        }else{
            return $query->whereIn('conference_status_id', [2,6,7,14,15]);
        }
    }

    /**
   * Get the list of available BudgetTypes
   * @return Collection
   */
    public function getAvailableBudgetTypes()
    {
        $budgetTypes = [];
		if($this->brands->count() > 0){
			foreach($this->brands as $brand){
               	if($brand->BudgetTypes->count() > 0){
					foreach($brand->BudgetTypes as $brands){
						$budgetTypes[$brands->id] = $brands->label;
					}
				}
			}
		}
        return $budgetTypes;
    }


    /**
     * Get the Conference Start Date for the Form
     *
     * @param  string  $value
     * @return string
     */

    public function formStartDateAttribute($value)
    {
		if($value){
			return Carbon::parse($value)->format('m/d/Y');
		}
    }


    /**
     * Get the Conference LeadRep(Created By)
     *
     * @param  string  $value
     * @return string
     */

    public function getLeadRepAttribute()
    {
        return $this->createdBy->repProfile;
    }


    /**
     * Get the Conference LeadRep Manager(Manager of Created By)
     *
     * @param  string  $value
     * @return string
     */

    public function getLeadRepManagerAttribute()
    {
        return $this->createdBy->parent->repProfile;
    }





    /**
     * get Can Approve
     *
    *  @param  string  $value
     * @return string
     */
    public function getCanApproveAttribute()
    {
        if(!empty(Auth()->user()->profile->territory) && in_array($this->creator, Auth()->user()->profile->descendants->pluck('id')->all())){
            return true;
        }else{
            return false;
        }
    }


    /**
     * get Parent Role
     *
     * @param  string  $value
     * @return string
     */
    public function getCreatorParentRoleAttribute()
    {
        if(!empty($this->createdBy->territory)){
            if($this->createdBy->parent && in_array(18, $this->createdBy->parent->groups->pluck('id')->toArray()) ){
                return 'Manager';
            }elseif($this->createdBy->parent && in_array(19, $this->createdBy->parent->groups->pluck('id')->toArray()) ){
                return 'Director';
            }
        }
        return '';
    }


    /**
     * Get the Conference End Date for the Form
     *
     * @param  string  $value
     * @return string
     */
    public function formEndDateAttribute($value)
    {
		if($value){
			return Carbon::parse($value)->format('m/d/Y');
		}
    }

     /**
     * Get the Conference End Date for the Form
     *
     * @param  string  $value
     * @return string
     */
    public function formRegistrationDueDateAttribute($value)
    {
        if($value){
            return Carbon::parse($value)->format('m/d/Y');
        }
    }


    /**
     * Get the Conference Exibitor Start Date for the Form
     *
     * @param  string  $value
     * @return string
     */
    public function formExibitorStartDateAttribute($value)
    {
		if($value){
			return Carbon::parse($value)->format('m/d/Y');
		}
    }


    /**
     * Get the Conference Exibitor End Date for the Form
     *
     * @param  string  $value
     * @return string
     */
    public function formExibitorEndDateAttribute($value)
    {
		if($value){
		 return Carbon::parse($value)->format('m/d/Y');
		}
    }



    /**
     * Get the Conference SetUp Date for the Form
     *
     * @param  string  $value
     * @return string
     */
    public function formSetUpDateAttribute($value)
    {
		if($value){
			return Carbon::parse($value)->format('m/d/Y');
		}
    }


    /**
     * Get the Conference Dismantle Date for the Form
     *
     * @param  string  $value
     * @return string
     */
    public function formDismantleDateAttribute($value)
    {
		if($value){
			return Carbon::parse($value)->format('m/d/Y');
		}
    }

	private function DateSub($value, $days)
    {
		if($value){
			return Carbon::parse($value)->subDays($days);
		}
    }

	public function CancelationCostItem($conference_status_id)
    {
		$current_date = Carbon::now()->timestamp;
		$start_date = $this->DateSub($this->exibitor_start_date, 5);

		$material_status = $this->literatures->pluck('pivot.material_status')->toArray();

		if($current_date > $start_date->timestamp){
            #Less than 5 days from conf date
			return [static::LATE_CANCELLATION_CONFERENCES, 395];
		}elseif($conference_status_id > 1 && $material_status && in_array(1, $material_status)){
			#5 days or more than 5 days from conf date
            # status is confirmed and Material id ordered
            return [static::CANCELLATION_FEE_CONFERENCES, 296.25];
		}elseif($conference_status_id == 10){
			#5 days or more than 5 days from conf date
            # Status is Confirmed
            return [static::CANCELLATION_FEE_CONFERENCES, 197.50];
		}elseif($conference_status_id != 10 && $conference_status_id > 1){
			#5 days or more than 5 days from conf date
            # status not confirmed
            return [static::CANCELLATION_FEE_CONFERENCES, 118.50];
		}else{
			return [0, 0];
		}
    }

	/**
   * Get the list of available Programcost
   * @return Collection
   */
    public function Conferencecost()
    {
         $Costs = [
            static::CONVENIENCE_FEE,
            static::CHANGE_FEE,
            static::CHECK_PROCESSING,
            static::EXPEDITING_FEE,
            //static::PROGRAM_MANAGEMENT_CONFERENCES,
            static::EXPEDITING_FEE_CONFERENCES,
            static::CANCELLATION_FEE_CONFERENCES,
            static::LATE_CANCELLATION_CONFERENCES,
            static::CHECK_PROCESSING_CONFERENCES,

        ];
        return $this->morphMany(Cost::class, 'context')->whereNotIn('cost_item_id', $Costs);
    }

    /**
   * Get the list of available Programfee
   * @return Collection
   */
    public function Conferencefee()
    {
         $Costs = [
            static::CONVENIENCE_FEE,
            static::CHANGE_FEE,
            static::CHECK_PROCESSING,
            static::EXPEDITING_FEE,
            //static::PROGRAM_MANAGEMENT_CONFERENCES,
            static::EXPEDITING_FEE_CONFERENCES,
            static::CANCELLATION_FEE_CONFERENCES,
            static::LATE_CANCELLATION_CONFERENCES,
            static::CHECK_PROCESSING_CONFERENCES,
        ];
        return $this->morphMany(Cost::class, 'context')->whereIn('cost_item_id', $Costs);
    }

	/**
     * Get the Conference LeadRep Manager(Manager of Created By)
     *
     * @param  string  $value
     * @return string
     */

    public function getIsBrandManagerAttribute()
    {

       $brand = $this->createdby_primary_brand;
       if($brand && $brand->conferenceManager && $brand->conferenceManager->id == Auth()->user()->profile->id){

			return true;
	   }
	   return false;
    }





	public function getCreatedbyPrimaryBrandAttribute()
    {

        return empty($this->createdBy->validbrands) ? null : $this->createdBy->validbrands->filter(function ($brand) {
                    return $brand->is_valid && $brand->pivot->is_primary;
                })->first();

    }

	public function getIsConferenceManagerBrandAttribute()
    {


/*
        $brands = $this->brands->filter(function ($brand) {
                    return $brand->conferenceManager && $brand->conferenceManager->id == Auth()->user()->profile->id;
                })->count();
        return $brands > 0 ? true :false;
*/

        $brand = $this->createdby_primary_brand;

       if($brand && $brand->conferenceManager && $brand->conferenceManager->id == Auth()->user()->profile->id){
            return true;
        }
        return false;



    }

	public function getIsBoothDismantledAttribute()
    {
		if($this->conferencecloseout){
			$value = $this->conferencecloseout->is_booth_dismantled;
			return $value == 1 ? 'Yes' : 'No';
		}
    }

	public function getIsDamageIncurredAttribute()
    {
		if($this->conferencecloseout){
			$value = $this->conferencecloseout->is_damage_incurred;
			return $value == 1 ? 'Yes' : 'No';
		}
    }

	public function getIsLeftoverMarketingMaterialAttribute()
    {
		if($this->conferencecloseout){
			$value = $this->conferencecloseout->is_leftover_marketing_material;
			return $value == 1 ? 'Yes' : 'No';
		}
    }

	public function getIsShippedConferenceDisplayAttribute()
    {
		if($this->conferencecloseout){
			$value = $this->conferencecloseout->is_shipped_conference_display;
			return $value == 1 ? 'Yes' : 'No';
		}
    }

	public function getIsConferenceDisplayAttribute()
    {
		if($this->conferencecloseout){
			$value = $this->conferencecloseout->is_conference_display;
			return $value == 1 ? 'Yes' : 'No';
		}
    }

	public function getIsAttendConferenceAttribute()
    {
		if($this->conferencecloseout){
			$value = $this->conferencecloseout->is_attend_conference;
			return $value == 1 ? 'Yes' : 'No';
		}
    }

	public function getIsOrganizationExplainedAttribute()
    {
		if($this->conferencecloseout){
			$value = $this->conferencecloseout->is_organization_explained;
			return $value == 1 ? 'Yes' : 'No';
		}
    }

	public function getIsProvidedEnoughLiteratureAttribute()
    {
		if($this->conferencecloseout){
			$value = $this->conferencecloseout->is_provided_enough_literature;
			if($value == 1){
				return 'No';
			}elseif($value == 2){
				return 'Yes' ;
			}else{
				return 'Not Applicable';
			}
		}
    }

	public function getIsLargerPresenceAttribute()
    {
		if($this->conferencecloseout){
			$value = $this->conferencecloseout->is_larger_presence;
			if($value == 1){
				return 'No';
			}elseif($value == 2){
				return 'Yes' ;
			}else{
				return 'Not Applicable';
			}
		}
    }

	public function getCloseoutDateAttribute()
    {
		if($this->conferencecloseout && $this->conferencecloseout->created_at){

			return $this->conferencecloseout->created_at->format('m/d/Y');

		}
    }

	/**
     * Conference by Brand
     *
     * @param  Builder $query
     * @param  array|string|int $id
     * @return Builder
     */
    public function scopeByBrand($query, $id, $method='whereHas')
    {
        return $query->$method('brands', function($relation) use($id){
            $relation->byKey($id);
        });
    }

	 /**
     * Scope Conference with Conference Date
     *
     * @param  Builder $query
     * @param  Carbon $min
     * @param  Carbon $max
     * @return Builder
     */
    public function scopeBetweenDates($query, $min, $max)
    {
		if($min && $max){
			return $query->whereRaw("exibitor_start_date BETWEEN '{$min}' AND '{$max}'");
		}
    }

	/**
     * Scope results in a number of ways
     *
     * @param  Builder $query
     * @param  array  $attributes
     * @return Builder
     */
    public function scopeInYear($query, $inYear)
    {
		if($inYear != 'ALL'){
			return $query->whereYear('exibitor_start_date', $inYear);
		}

    }

	/**
     * Resolve Full Date as per settings
     *
     * @return string
     */
    public function getFullExibitorStartDateAttribute()
    {
        return $this->exibitor_start_date->format('m/d/Y');
    }

	/**
     * Resolve Full Date as per settings
     *
     * @return string
     */
    public function getFullExibitorEndDateAttribute()
    {
        return $this->exibitor_end_date->format('m/d/Y');
    }

	/**
     * Resolve Primary pms Attribute
     *
     * @return string
     */
    public function getPrimaryPmsAttribute()
    {
        $primary_pms =  $this->pms->where('pivot.is_primary', true)->first();

		if(empty($primary_pms)){
			$primary_pms =  $this->pms->first();
		}

		return $primary_pms;
    }

    /**
     * Resolve Primary reps Attribute
     *
     * @return string
     */
    public function getPrimaryRepAttribute()
    {
        $primary_reps =  $this->reps->where('pivot.is_primary', true)->first();

        if(empty($primary_reps)){
            $primary_reps =  $this->reps->first();
        }

        return $primary_reps;
    }

	/**
     * Return the label for the Program Type
     *
     * @return string
     */
    public function getStatusLabelAttribute()
    {
        return $this->is_on_hold ? 'On Hold' : object_get($this->conferenceStatus, 'label');
    }

    /**
     * get visible rooms
     *
     * @return Collection
     */
    public function getVisibleRoomsAttribute()
    {
        return $this->rooms->filter(function($room){
            return $room->is_visible;
        });
    }


    /**
     * Check if conference can be RD approved (status id 15) by the user
     *
     * @return bool
     */
    public function canBeRDApprovedByTheUser(Profile $profile)
    {
        if ( $territory = data_get($this, 'createdBy.territory.account_territory_id') ){
                if ( substr($territory, 0, 4) == 4103 || substr($territory, 0, 4) == 4104 || substr($territory, 0, 4) == 4100 ){
                    if($profile->id == 857) // Jason Hill
                        return true;
                    else
                        return false;
                }

                if ( substr($territory, 0, 4) == 4101 || substr($territory, 0, 4) == 4102 || substr($territory, 0, 4) == 4105 ){
                    if($profile->id == 851) // Mark Lanzoni
                        return true;
                    else
                        return false;
                }
        }
    }

    /**
     * Conference by Brand
     *
     * @param  Builder $query
     * @param  array|string|int $id
     * @return Builder
     */
    public function scopeOrdered($query, $method='whereHas')
    {
        return $query->$method('reps', function($relation) {
            $relation->where('badge_status', BadgeStatus::ORDERED);
        });
    }

    /**
     * Apply various scopes
     *
     * @param  Builder $query
     * @param  array $attributes
     * @return Builder
     */
    public function scopeAnyReport($query, $attributes)
    {
        # apply Brand scope
        if ($brands = array_get($attributes, 'inBrand', [])){
            $query->byBrand($brands);
        }
         # apply Conference scope
        if ($id = array_get($attributes, 'inConference', [])){
            $query->where('id', $id);
        }
        # apply date selector
        if($min = array_get($attributes, 'from') AND $max = array_get($attributes, 'to')){
            $query->betweenDates($min, $max);
        }
        # Return
        return $query;
    }

    /**
     * Get All Housing
     *
     * @return Collection
     */
    public function getAllHousingsAttribute()
    {
        $userInfo = Auth()->user();
        $profile_ids = array();
        $groups = $userInfo->profile->groups;
        if(!$userInfo->profile->in_rep_group){
            return $this->housings;
        }else{
            if(isset($groups)){
                foreach($groups as $group){
                    if(count($group->parents) AND ($userInfo->profile->id != $group->parents->last()->id)){
                        $profile_ids[] = $userInfo->profile->id;
                    }else{
                        $childProfile_ids = $group->profiles->pluck('id')->toArray();
                        $profile_ids = array_merge($profile_ids, $childProfile_ids);
                    }
                }
            }
        }
        return $this->housings->whereIn('creator', (array)$profile_ids, false);
    }

    /**
     * Get All Housing
     *
     * @return Collection
     */
    public function getFetchHousingsAttribute()
    {
        $userInfo = Auth()->user();
        $profileGroup = $userInfo->profile->groups();

        if($userInfo->profile->is_group_parent){
            $profileGroup = $profileGroup->notByReferenceName([ProfileGroup::CLIENT_VP]);
        }
        if(!$userInfo->profile->in_rep_group){
            $groups = $userInfo->profile->rep_groups;
            $profileGroup = ProfileGroup::whereIn('reference_name', $groups);
        }
        return $profileGroup->with(['housing' => function($query) use ($userInfo){
                   if($userInfo->profile->is_group_child){
                        $query->where('creator', $userInfo->profile_id);
                    }
                   $query->where('conference_id', $this->id);
                return  $query;
            }])->get();
    }


   /**
     * Sales Rep and Sales manager can Register just for self
     * Get Conference Registration Allow
     *
     * @return string
     */
    public function getRegistrationAllowAttribute()
    {
        $userInfo = Auth()->user();
        if($userInfo->profile->in_rep_group){
            return $this->housings()->where('creator', $userInfo->profile_id)->count();
        }else{
            return false;
        }
    }


    /**
    * Each Program Type may have its own survey type
    * @return Collection
    */
    public function surveyMap()
    {
       # conference Survey Type
       $surveyType =  SurveyType::find(1);

       # return
       return  ($surveyType) ? $surveyType->map() : new Collection;
    }

    /**
    * Each Conference Start Date to End Date
    * @return Array
    */
    public function getStartToEndDateRangeAttribute()
    {
        $period = CarbonPeriod::create($this->exibitor_start_date, $this->exibitor_end_date);
        // Iterate over the period
        $dateArr = array();
        foreach ($period as $date) {
            $dateArr[$date->format('Y-m-d')] = $date->format('l, M d, Y');
        }
        return $dateArr;
    }

    public function conferenceHousingHistory()
    {
        return $this->hasMany(ConferenceHousingHistory::class);
    }

    public function conferenceAffiliateHistory()
    {
        return $this->hasMany(ConferenceAffiliateHistory::class);
    }

    /**
     * Get All Affiliate Meeting
     *
     * @return Collection
     */
    public function getAllAffiliateMeetingAttribute()
    {
        $userInfo = Auth()->user();
        $profileGroup = $userInfo->profile->groups();

        if($userInfo->profile->is_group_parent){
            $profileGroup = $profileGroup->notByReferenceName([ProfileGroup::CLIENT_VP]);
        }
        if(!$userInfo->profile->in_rep_group){
            $groups = $userInfo->profile->rep_groups;
            $profileGroup = ProfileGroup::whereIn('reference_name', $groups);
        }
        return $profileGroup->with(['affiliate' => function($query) use ($userInfo){
                if($userInfo->profile->is_group_child){
                        $query->where('creator', $userInfo->profile_id);
                    }
                return $query->where('conference_id', $this->id);
            }])->get();
    }
}
