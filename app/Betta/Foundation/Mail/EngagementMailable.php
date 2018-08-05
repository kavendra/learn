<?php

namespace Betta\Foundation\Mail;

use Betta\Models\Engagement;

abstract class EngagementMailable extends AbstractMailable
{
    use LogsCommunications;

    /**
     * Inject instance
     *
     * @var Betta\Models\Engagement
     */
    public $engagement;

    /**
     * Variable name holding the Context Model
     *
     * @var string
     */
    protected $context = 'engagement';

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Engagement $engagement)
    {
        $this->engagement = $engagement;
    }
}
