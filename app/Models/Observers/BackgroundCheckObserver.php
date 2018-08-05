<?php

namespace Betta\Models\Observers;

use Betta\Models\ProfileGroup;
use Betta\Models\BackgroundCheck;
use Betta\Foundation\Eloquent\AbstractObserver;

class BackgroundCheckObserver extends AbstractObserver
{
    /**
     * Once the BG Check is complete fire these events
     *
     * @var array
     */
    protected $completionEvents = [
        'App\Events\Assessment\BackgroundCheckComplete',
    ];

    /**
     * List the Groups in which the assessors reside
     *
     * @var array
     */
    protected $assessors = [
        ProfileGroup::FLS_ASSESSMENT_COORDINATOR,
    ];

    /**
     * Listen to the BackgroundCheck creating event.
     *
     * @param  BackgroundCheck  $model
     * @return void
     */
    public function creating(BackgroundCheck $model)
    {
        # Set Current User as Creator
        $model->setAttribute('creator', $model->getAttribute('creator') ?: $this->getUserId() );
        # Set Valid From to now, unless it is provided
        $model->setAttribute('valid_from', $model->getAttribute('valid_from') ?: $this->now() );
        # Set Default evaluator
        $model->setAttribute('evaluator_id', $this->evaluator($model));
    }

    /**
     * Listen to the BackgroundCheck saved event.
     *
     * @param  BackgroundCheck  $model
     * @return void
     */
    public function saved(BackgroundCheck $model)
    {
        if ($model->isDirtyFromNull('completed_at')){
            # Set Current User as Creator
            $model->setAttribute('evaluator_id', $this->getUserId());
            # fire the event
            $this->fireCompletionEvents($model);
        }
    }

    /**
     * Fire completion events
     *
     * @param  BackgroundCheck $model
     * @return [type]
     */
    protected function fireCompletionEvents(BackgroundCheck $model)
    {
        foreach ($this->completionEvents as $event){
            event(new $event($model) );
        }
    }

    /**
     * Return evaluator
     *
     * @param  BackgroundCheck $model
     * @return int | null
     */
    protected function evaluator(BackgroundCheck $model)
    {
        return $model->getAttribute('evaluator_id') ?: $this->defaultEvaluator();
    }

    /**
     * Default evaluator for the BG
     *
     * @return int | null
     */
    protected function defaultEvaluator()
    {
        if ($group = app(ProfileGroup::class)->setEagerLoads([])->byReferenceName($this->assessors)->first()){
            return data_get($group->profiles()->setEagerLoads([])->first(), 'id');
        }
    }
}
