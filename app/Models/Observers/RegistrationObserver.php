<?php

namespace Betta\Models\Observers;

use Betta\Models\Registration;
use Betta\Models\RegistrationStatus;
use Betta\Foundation\Eloquent\AbstractObserver;

class RegistrationObserver extends AbstractObserver
{
    /**
     * In what status we shall create progression
     *
     * @var integer
     */
    protected $initialState = Registration::REGISTERED;

    /**
     * Define the events
     *
     * @var Array
     */
    protected $statusEvents = [
        RegistrationStatus::TARGET => 'App\Events\Registration\Target',
        RegistrationStatus::INVITED => 'App\Events\Registration\Invited',
        RegistrationStatus::WAITLIST => 'App\Events\Registration\Waitlist',
        RegistrationStatus::WAITLIST_OTHER => 'App\Events\Registration\WaitlistOther',
        RegistrationStatus::PENDING => 'App\Events\Registration\Pending',
        RegistrationStatus::REGISTRATION_PENDING => 'App\Events\Registration\RegistrationPending',
        RegistrationStatus::REGISTERED => 'App\Events\Registration\Registered',
        RegistrationStatus::ATTENDED => 'App\Events\Registration\Attended',
        RegistrationStatus::NO_SHOW => 'App\Events\Registration\NoShow',
        RegistrationStatus::ONSITE => 'App\Events\Registration\OnSite',
        RegistrationStatus::DECLINED => 'App\Events\Registration\Declined',
        RegistrationStatus::DUPLICATE => 'App\Events\Registration\Duplicate',
        RegistrationStatus::CANCELLED => 'App\Events\Registration\Cancelled',
        RegistrationStatus::OTHER => 'App\Events\Registration\Other',
        RegistrationStatus::PROGRAM_CANCELLED => 'App\Events\Registration\ProgramCancelled',
    ];

    /**
     * Model status field name
     * @var string
     */
    protected $statusField = 'registration_status_id';

    /**
     * Listen to the Registration creating event.
     *
     * @param  Registration  $model
     * @return void
     */
    public function creating(Registration $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
        # set Initial Status
        $model->setAttribute($this->statusField,
            ($model->getAttribute($this->statusField)) ? $model->getAttribute($this->statusField) : $this->initialState );
    }

    /**
     * Listen to the Registration saving event.
     *
     * @param  Registration  $model
     * @return void
     */
    public function saving(Registration $model)
    {
        # Clean up the Cell Phone
        $model->setAttribute('phone', $this->numbersOnly($model->getAttribute('phone')) );
        # Clean up the Phone
        $model->setAttribute('cell_phone', $this->numbersOnly($model->getAttribute('cell_phone')) );
        # Clean up the Fax
        $model->setAttribute('fax', $this->numbersOnly($model->getAttribute('fax')) );
        # Clean up the Fax
        $model->setAttribute('npi', empty($value = $model->getAttribute('npi')) ? null : $value  );
    }

    /**
     * Listen to Registration update event
     *
     * @param  Registration $model
     * @return void
     */
    public function saved(Registration $model)
    {
        if($model->isDirty($this->statusField)){
            # Record Histories
            $this->recordHistories($model);
            # Fire the events
            $this->fireStatusEvents($model);
        }
    }

    /**
     * Record Histories
     *
     * @param  Registration $model
     * @return void
     */
    protected function recordHistories(Registration $model)
    {
        # get the next status
        $to_status_id = data_get($model, $this->statusField);
        # record the history
        $model->histories()->create(compact('to_status_id'));
    }

    /**
     * Fire events
     *
     * @param  Registration $model
     * @return Void
     */
    protected function fireStatusEvents(Registration $model)
    {
        # we have Previous status
        $previous = $model->getOriginal($this->statusField);
        # we have next status
        $next = $model->getAttribute($this->statusField);
        # Map the status to events and fire them all
        if ($event = array_get($this->statusEvents, $next)){
            event (new $event($model));
        }
    }
}
