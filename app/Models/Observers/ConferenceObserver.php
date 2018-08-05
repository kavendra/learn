<?php

namespace App\Models\Observers;

use App\Models\Conference;
use Betta\Foundation\Eloquent\AbstractObserver;

class ConferenceObserver extends AbstractObserver
{
    /**
     * Create the lsit of events
     *
     * @var Array
     */
    protected $statusEvents = [
        '2' => 'App\Events\Conference\Submitted',
        '3' => 'App\Events\Conference\Denied',
        '4' => 'App\Events\Conference\Deniedrm',
        '5' => 'App\Events\Conference\Deniedbrand',
        '6' => 'App\Events\Conference\Approved',
        '7' => 'App\Events\Conference\Approvedrm',
        '8' => 'App\Events\Conference\Approvedbrand',
        '10' => 'App\Events\Conference\Confirmed',
        '11' => 'App\Events\Conference\Cancelled',
        '14' => 'App\Events\Conference\Deniedrd',
        '15' => 'App\Events\Conference\Approvedrd',

    ];

    /**
     * Listen to the Conference creating event.
     *
     * @param  Conference  $model
     * @return void
     */
    public function creating(Conference $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );

        # Set Label
        $model->setAttribute('label', $model->getAttribute('label') ?: $model->getAttribute('associated_conference') );
		$model->setAttribute('display_type_id', $model->getAttribute('display_type_id') ?: 0 );
	}

    /**
     * Listen to the Conference created event.
     *
     * @param  Conference  $model
     * @return void
     */
    public function created(Conference $model)
    {
        # Log creation
        $model->histories()->create(['to_status_id'=>$model->conference_status_id]);
    }

    /**
     * Listen to Conference update event
     *
     * @param  Conference $model
     * @return void
     */
    public function updated(Conference $model)
    {
		$model->histories()->create(['to_status_id'=>$model->conference_status_id, 'from_status_id'=>$model->getOriginal('conference_status_id')]);

		if($model->isDirty('conference_status_id')){
            $this->fireStatusEvents($model);
        }
		 $model->setAttribute('display_type_id', $model->getAttribute('display_type_id') ?: 0 );
        # Update the costs
        # Update the distance
    }

    /**
     * Fire events
     *
     * @param  Conference $model
     * @return Void
     */
    protected function fireStatusEvents(Conference $model)
    {
        # we have Previous status
        $previous = $model->getOriginal('conference_status_id');
        # we have next status
        $next = $model->getAttribute('conference_status_id');

        # Map the status to events and fire them all
        if ($event = array_get($this->statusEvents, $next)){
            event (new $event($model));
        }
    }
}
