<?php

namespace App\Models\Traits;

use Betta\Models\Profile;

trait CreatedByTrait
{

    /**
     * Record is created by the Profile
     *
     * @return Relation
     */
    public function createdBy()
    {
        return $this->belongsTo(Profile::class, 'creator');
    }
}
