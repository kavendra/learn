<?php

namespace Betta\Models\Observers;

use Betta\Foundation\Eloquent\AbstractModel;
use Betta\Foundation\Eloquent\AbstractObserver;
use Betta\Models\Interfaces\ProgressionStatusInterface as Status;

trait ProgressionEvents
{
    /**
     * Event Namespace
     *
     * @var string
     */
    protected $eventNamespace = 'App\Events';

    /**
     * Create the lsit of Nomination events
     *
     * @var Array
     */
    protected $progressionEvents = [
        Status::REQUESTED => 'Requested',
        Status::INITIATED => 'Initiated',
        Status::DECLINED => 'Declined',
        Status::ACCEPTED => 'Accepted',
        Status::CONFIRMED => 'Confirmed',
        Status::NOT_QUALIFIED => 'NotQualified',
        Status::QUALIFIED => 'Qualified',
        Status::CANCELLED => 'Cancelled',
        Status::NOT_NEEDED => 'NotNeeded',
        Status::NOT_REQUIRED => 'NotRequired',
    ];

    /**
     * Expected functionality
     * 1. Base event, fro the model that is being called,
     *    as in for Travel: App\Events\{Travel}\Requested
     *
     * 2. Context event, for each of the $context fields ['context'] .. class_basename(get_class($model->context))
     *    as in for Travel in Engagement: App\Events\{Travel}\{Engagement}\Requested
     */
}
