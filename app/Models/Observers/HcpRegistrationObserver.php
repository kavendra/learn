<?php

namespace Betta\Models\Observers;

use Betta\Models\Registration;
use Betta\Models\RegistrationStatus;
use Betta\Foundation\Eloquent\AbstractObserver;

class HcpRegistrationObserver extends AbstractObserver
{
    /**
     * Model status field name
     * @var string
     */
    protected $statusField = 'registration_status_id';

    /**
     * Define the events
     *
     * @var Array
     */
    protected $statusEvents = [
        RegistrationStatus::REGISTERED => 'App\Events\Registration\Hcp\Registered',
        RegistrationStatus::ATTENDED => 'App\Events\Registration\Hcp\Attended',
        RegistrationStatus::NO_SHOW => 'App\Events\Registration\Hcp\NoShow',
        RegistrationStatus::ONSITE => 'App\Events\Registration\Hcp\OnSite',
        RegistrationStatus::CANCELLED => 'App\Events\Registration\Hcp\Cancelled',
    ];

    /**
     * Listen to Registration update event
     *
     * @param  Registration $model
     * @return void
     */
    public function updated(Registration $model)
    {
        if($model->isDirty('email')){
            # Fire the events
            $this->fireStatusEvents($model);
        }
    }

    /**
     * Fire events
     * The method will check if the Registration is HCP, and will not fire the events, if it is not so.
     *
     * @param  Registration $model
     * @return Void
     */
    protected function fireStatusEvents(Registration $model)
    {
        # if the Registration is not for HCP, we do not care
        if(!$model->is_hcp) return;
        # we have final state
        $next = $model->getAttribute($this->statusField);
        # Map the status to events and fire them all
        if ($event = array_get($this->statusEvents, $next)){
            event (new $event($model));
        }
    }
}
