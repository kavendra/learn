<?php

namespace App\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;

use App\Models\Pivots\ProgramBrandPivot;
use App\Models\Pivots\ProgramLocationPivot;
use App\Models\Pivots\ProgramSpeakerPivot;
use App\Models\Pivots\ProfileSegmentPivot;
use App\Models\Pivots\EngagementNeedPivot;
use App\Models\Pivots\ProfileInstitutionPivot;
use App\Models\Pivots\ProfileEducationFacilityPivot;

class Pivotable extends Model
{
    /**
     * Replacement for the newPivot function
     *
     * @param  Model  $parent
     * @param  array  $attributes
     * @param  String $table
     * @param  Boolean $exists
     * @return Pivot
     */
    public function newPivot(Model $parent, array $attributes, $table, $exists, $using = NULL)
    {
        # Program -> ProgramLocation
        if (str_is($table, 'programs_to_locations') )
        {
            return new ProgramLocationPivot($parent, $attributes, $table, $exists, $using);
        }

        # Program -> ProgramSpeaker
        if (str_is($table, 'programs_to_speakers') )
        {
            return new ProgramSpeakerPivot($parent, $attributes, $table, $exists, $using);
        }

        # Profile -> Segment
        if (str_is($table, 'profile_segments') )
        {
            return new ProfileSegmentPivot($parent, $attributes, $table, $exists, $using);
        }

        # Engagement > Needs
        if (str_is($table, 'engagement_needs') )
        {
            return new EngagementNeedPivot($parent, $attributes, $table, $exists, $using);
        }

        # Program > Brand
        if (str_is($table, 'programs_to_brands') )
        {
            return new ProgramBrandPivot($parent, $attributes, $table, $exists, $using);
        }

        # Profile > Research Institution
        if (str_is($table, 'profile_to_institutions') )
        {
            return new ProfileInstitutionPivot($parent, $attributes, $table, $exists, $using);
        }

        # Profile > Education Facility
        if (str_is($table, 'profile_to_education_facilities') )
        {
            return new ProfileEducationFacilityPivot($parent, $attributes, $table, $exists, $using);
        }

        # Profile > Education Facility
        if (str_is($table, 'profile_to_brand') )
        {
            return new ProfileBrandPivot($parent, $attributes, $table, $exists, $using);
        }

        # map to a Parent Pivoted Model
        return parent::newPivot($parent, $attributes, $table, $exists, $using);
    }
}
