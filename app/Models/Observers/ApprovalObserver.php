<?php

namespace Betta\Models\Observers;

use Betta\Models\Approval;
use Betta\Foundation\Eloquent\AbstractObserver;

class ApprovalObserver extends AbstractObserver
{
    /**
     * Listen to the Approval creating event.
     *
     * @param  Approval  $model
     * @return void
     */
    public function creating(Approval $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
    }

    /**
     * Listen to the Approval saving() event.
     *
     * @param  Approval  $model
     * @return void
     */
    public function saving(Approval $model)
    {
        if($model->isDirty('is_approved')){
            $model->setAttribute('acted_at', $this->now());
        }
    }

    /**
     * Listen to the Approval saved() event.
     *
     * @param  Approval  $model
     * @return void
     */
    public function saved(Approval $model)
    {
        if($model->isDirty('is_approved')){
            $this->fireContextEvent($model);
        }
    }

    /**
     * First Event of the Context
     *
     * @param  Approval $model
     * @return
     */
    protected function fireContextEvent(Approval $model)
    {
        # fire event of the context
        # like new \Betta\Models\MaxCapIncrease\Approved ($context)
        # it will listen to Approved and seek approval from the next, like
        # mail->to->next approver
        # like new \Betta\Models\MaxCapIncrease\Denied -> will:
        # - delete the unused approvals
        # - delete the unused action_urls
        # - decline upper MaxCapIncrease and notify all involved
        # maybe new \Betta\Models\MaxCapIncrease\Defer -> will:
        # - insert new approval before current
        # - send an email?

        # guess action based on the value
        $action = $this->getAction($model);
        # Compile Event Class Name
        $event = implode('\\', ['\App\Events\Approval', class_basename($model->context_type), $action]);
        # fire if exists
        if(class_exists($event)){
            event( new $event($model->context) );
        }
    }

    /**
     * Value of the action based on 'is_approved' field
     *
     * @todo  If more answers are necessary, use switch or even a $map[]
     * @param  Approval $model
     * @return string
     */
    protected function getAction(Approval $model)
    {
        # no change
        if(is_null($model->is_approved)) return 'NoChange';

        # Approved
        if($model->is_approved == 1) return 'Approved';

        # NotApproved
        return 'NotApproved';
    }
}
