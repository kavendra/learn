<?php

namespace Betta\Foundation\Eloquent;

use Betta\Models\Profile;
use Betta\Models\Traits\CreatedByTrait;
use Betta\Foundation\Eloquent\AbstractModel;

abstract class AbstractExtendedProfile extends AbstractModel
{
    /**
     * All Extended profiles have a profile that created them
     */
    use CreatedByTrait;

    /**
     * Record extends Profile
     *
     * @return Relation
     */
    public function profile()
    {
        return $this->belongsTo( Profile::class );
    }
}
