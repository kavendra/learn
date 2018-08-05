<?php

namespace Betta\Models\Observers;

use Betta\Models\Nomination;
use Betta\Models\NominationStatus;
use Betta\Foundation\Eloquent\AbstractObserver;

class NominationObserver extends AbstractObserver
{
    /**
     * Create the lsit of Nomination events
     *
     * @var Array
     */
    protected $statusEvents = [
        NominationStatus::INITIATED => 'App\Events\Nomination\Initiated',
        NominationStatus::SUBMITTED => 'App\Events\Nomination\Submitted',
        NominationStatus::MANAGER_APPROVED => 'App\Events\Nomination\ManagerApproved',
        NominationStatus::MANAGER_REJECTED => 'App\Events\Nomination\ManagerRejected',
        NominationStatus::REGIONAL_MANAGER_APPROVED => 'App\Events\Nomination\RegionalManagerApproved',
        NominationStatus::REGIONAL_MANAGER_REJECTED => 'App\Events\Nomination\RegionalManagerRejected',
        NominationStatus::PENDING_ASSESSMENT => 'App\Events\Nomination\PendingAssessment',
        NominationStatus::PENDING_MEDICAL_REVIEW => 'App\Events\Nomination\PendingMedicalReview',
        NominationStatus::PENDING_LEGAL_REVIEW => 'App\Events\Nomination\PendingLegalReview',
        NominationStatus::PENDING_COMPLIANCE_REVIEW => 'App\Events\Nomination\PendingComplicanceReview',
        NominationStatus::COMPLIANCE_APPROVED => 'App\Events\Nomination\ComplicanceApproved',
        NominationStatus::COMPLIANCE_DENIED => 'App\Events\Nomination\ComplicanceDenied',
        NominationStatus::PENDING_BRAND_APPROVAL => 'App\Events\Nomination\PendingBrandApproval',
        NominationStatus::BRAND_APPROVED => 'App\Events\Nomination\BrandApproved',
        NominationStatus::BRAND_DENIED => 'App\Events\Nomination\BrandDenied',
        NominationStatus::PENDING_CONTRACT => 'App\Events\Nomination\PendingContract',
        NominationStatus::NOT_APPROVED => 'App\Events\Nomination\NotApproved',
    ];

    /**
     * Register Optimization Events
     *
     * @var Array
     */
    protected $optimizationStatusEvents = [
        NominationStatus::INITIATED => 'App\Events\Optimization\Initiated',
        NominationStatus::PENDING_OWNER => 'App\Events\Optimization\PendingOwner',
        NominationStatus::OWNER_APPROVED => 'App\Events\Optimization\OwnerApproved',
        NominationStatus::OWNER_REJECTED => 'App\Events\Optimization\OwnerRejected',
        NominationStatus::PENDING_MANAGER_APPROVAL => 'App\Events\Optimization\PendingManagerApproval',
        NominationStatus::MANAGER_APPROVED => 'App\Events\Optimization\ManagerApproved',
        NominationStatus::MANAGER_REJECTED => 'App\Events\Optimization\ManagerRejected',
        NominationStatus::PENDING_REGIONAL_MANAGER_APPROVAL => 'App\Events\Optimization\PendingDirector',
        NominationStatus::REGIONAL_MANAGER_APPROVED => 'App\Events\Optimization\DirectorApproved',
        NominationStatus::REGIONAL_MANAGER_REJECTED => 'App\Events\Optimization\DirectorRejected',
        NominationStatus::PENDING_ASSESSMENT => 'App\Events\Optimization\PendingAssessment',
        NominationStatus::PENDING_MEDICAL_REVIEW => 'App\Events\Optimization\PendingMedicalReview',
        NominationStatus::PENDING_LEGAL_REVIEW => 'App\Events\Optimization\PendingLegalReview',
        NominationStatus::PENDING_COMPLIANCE_REVIEW => 'App\Events\Optimization\PendingComplicanceReview',
        NominationStatus::COMPLIANCE_APPROVED => 'App\Events\Optimization\ComplicanceApproved',
        NominationStatus::COMPLIANCE_DENIED => 'App\Events\Optimization\ComplicanceDenied',
        NominationStatus::PENDING_BRAND_APPROVAL => 'App\Events\Optimization\PendingBrandApproval',
        NominationStatus::BRAND_APPROVED => 'App\Events\Optimization\BrandApproved',
        NominationStatus::BRAND_DENIED => 'App\Events\Optimization\BrandDenied',
        NominationStatus::PENDING_CONTRACT => 'App\Events\Optimization\PendingContract',
        NominationStatus::SUSPENDED => 'App\Events\Optimization\Suspended',
        NominationStatus::NOT_APPROVED => 'App\Events\Optimization\NotApproved',
    ];

    /**
     * Listen to the Nomination creating event.
     *
     * @param  Nomination  $model
     * @return void
     */
    public function creating(Nomination $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );

        # Set Current User as Creator
        $model->setAttribute('nomination_status_id', $model->getAttribute('nomination_status_id') ?: NominationStatus::INITIATED );

        # Set Current User as Creator
        $model->setAttribute('valid_from', $model->getAttribute('valid_from') ?: $this->now() );

        # Set the Expiry Date to 1 year from now
        $model->setAttribute('valid_to', $model->getAttribute('valid_to') ?: $this->now()->addYear() );

        # Set the Owner ID to current user
        $model->setAttribute('owner_id', $model->getAttribute('owner_id') ?: $this->getUserId() );
    }

    /**
     * Listen to the Nomination created event.
     *
     * @param  Nomination  $model
     * @return void
     */
    public function created(Nomination $model)
    {
        # Void
    }

    /**
     * Listen to the Nomination History updated event
     *
     * @param  Nomination  $model
     * @return void
     */
    public function saved(Nomination $model)
    {
        if($model->isDirty('nomination_status_id')){
            $this->fireStatusEvents($model);
        }

        if($model->isDirty('tier_id')){
            $this->recordTierChange($model);
        }
    }

    /**
     * Record Tier Change
     *
     * @param  Nomination $model
     * @return Void
     */
    protected function recordTierChange(Nomination $model)
    {
        # we have Previous Tier
        $from_tier_id = $model->getOriginal('tier_id');
        # we have next Tier
        $to_tier_id = $model->getAttribute('tier_id');
        # so what was the action?
        $action = empty($from_tier_id) ? "Assigned" : "Updated to";
        # What is the tier?
        $model->load('tier');
        # there is a tier
        if($model->tier){
            # do the note
            $model->addPrivateNote("{$action} {$model->tier_label} Tier.");
        }
    }

    /**
     * Fire events
     *
     * @param  Program $model
     * @return Void
     */
    protected function fireStatusEvents(Nomination $model)
    {
        # we have Previous status
        $from_status_id = $model->getOriginal('nomination_status_id');

        # we have next status
        $to_status_id = $model->getAttribute('nomination_status_id');

        # Inject history
        $model->histories()->create(compact('from_status_id','to_status_id'));

        # Map the status to events and fire them all
        if ($event = array_get($this->getStatusEvents($model), $to_status_id)){
            event (new $event($model));
        }
    }

    /**
     * Decide what events should take palce
     *
     * @param  Nomination $model
     * @return Array
     */
    protected function getStatusEvents(Nomination $model)
    {
        return  $model->is_optimization ? $this->optimizationStatusEvents : $this->statusEvents;
    }
}
