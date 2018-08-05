<?php

namespace Betta\Models\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

trait PivotableTrait
{
    /**
     * Map table to pivot classes
     *
     * @var array
     */
    protected $customPivots = [
        #'program_to_field'          => 'Betta\Models\Pivots\ProgramFieldPivot',
        #'program_to_brand'          => 'Betta\Models\Pivots\ProgramBrandPivot',
        #'profile_to_brand'          => 'Betta\Models\Pivots\ProfileBrandPivot',
        #'program_to_speaker'        => 'Betta\Models\Pivots\ProgramSpeakerPivot',
        #'program_type_to_brand'     => 'Betta\Models\Pivots\ProgramTypeBrandPivot',
        #'profile_to_speaker_bureau' => 'Betta\Models\Pivots\ProfileSpeakerBureauPivot',
    ];


    /**
     * Create a new pivot model instance.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $parent
     * @param  array  $attributes
     * @param  string  $table
     * @param  bool  $exists
     * @return \Illuminate\Database\Eloquent\Relations\Pivot
     */
    public function newPivot(Model $parent, array $attributes, $table, $exists, $using = NULL)
    {
        $pivotHandler = array_get($this->customPivots, $table, Pivot::class);

        return new $pivotHandler($attributes, $table, $exists, $using);
    }
}
