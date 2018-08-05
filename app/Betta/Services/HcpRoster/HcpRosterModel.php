<?php

namespace Betta\Services\HcpRoster;

use Betta\Models\Degree;
use Betta\Models\Profile;
use Betta\Models\Specialty;
use Illuminate\Database\Eloquent\Model;

class HcpRosterModel extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = false;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'customer_master_id';

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [
        'profile.addresses',
        'profile.hcpProfile',
        'profile.speakerProfile',
    ];

    /**
     * Record has Profile
     *
     * @return Relation
     */
    public function profile()
    {
        return $this->hasOne(Profile::class, 'customer_master_id');
    }

    /**
     * Record has Degree
     *
     * @return Relation
     */
    public function degree()
    {
        return $this->belongsTo(Degree::class, null, 'professional_degree');
    }

    /**
     * Record has Specialty
     *
     * @return Relation
     */
    public function specialty()
    {
        return $this->belongsTo(Specialty::class, null, 'label');
    }
}
