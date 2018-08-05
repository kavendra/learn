<?php

namespace Betta\Foundation\Listeners;

use Illuminate\Mail\Mailer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Betta\Foundation\Events\AbstractBettaEvent;

abstract class AbstractBettaListener
{
    /**
     * Bind the implementation
     *
     * @var Illuminate\Mail\Mailer
     */
    protected $mail;

    /**
     * Bind the implementation
     *
     * @var Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(Mailer $mailer)
    {
        $this->mail = $mailer;
    }

    /**
     * Dismiss all the items that needs to be dismissed
     *
     * @return Void
     */
    protected function dismiss(AbstractBettaEvent $event)
    {
        $this->getModel()->alerts()->byEvent(get_class($event))->undismissed()
             ->dismiss(['is_dismissable'=>1, 'dismissed_by'=>$this->getProfileId()]);
    }

    /**
     * Alert all receipients that need to be notified
     *
     * @return Void
     */
    protected function alert()
    {
        # Implement inside theActual Event
    }

    /**
     * Set the Model
     *
     * @param  Model $model
     * @param  string| null $name
     * @return Instance
     */
    protected function setModel(Model $model, $name = null)
    {
        $this->model = $model;

        # if the $name is provided, assign the property
        if(!empty($name)){
            $this->$name = $model;
        }

        return $this;
    }

    /**
     * Access Model
     *
     * @return Model
     */
    protected function getModel()
    {
        return $this->model;
    }

    /**
     * Return CLU
     *
     * @return int
     */
    protected function getProfileId()
    {
        return data_get($this->getProfile(),'profile_id', config('betta.default_profile_id'));
    }

    /**
     * Resolve the User from the Currently Logged in State
     *
     * @return User | null
     */
    protected function getProfile()
    {
        return auth()->user();
    }
}
