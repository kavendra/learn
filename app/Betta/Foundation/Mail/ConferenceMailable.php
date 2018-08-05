<?php

namespace Betta\Foundation\Mail;

use Betta\Models\Conference;

abstract class ConferenceMailable extends AbstractMailable
{
    use LogsCommunications;

    /**
     * Inject instance
     *
     * @var Betta\Models\Conference
     */
    public $conference;

    /**
     * Variable name holding the Context Model
     *
     * @var string
     */
    protected $context = 'conference';

    /**
     * Create a new message instance.
     *
     * @param  Betta\Models\Conference $conference
     * @param  Array $attributes
     * @return void
     */
    public function __construct(Conference $conference, $attributes = [])
    {
        $this->conference = $conference;

        $this->with($attributes);
    }
}
