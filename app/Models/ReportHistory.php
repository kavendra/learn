<?php

namespace App\Models;

use Betta\Foundation\Eloquent\AbstractModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReportHistory extends AbstractModel
{
    use SoftDeletes;
    use Traits\CreatedByTrait;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'function_class',
        'function_name',
        'fuction_arguments',
        'report_name',
        'report_uri',
        'creator',
    ];


    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['fuction_arguments' => 'array'];


    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];


    /**
     * Scope results to type of the Report
     *
     * @param  Buidler $query
     * @param  String  $type
     * @return Builder
     */
    public function scopeOfType($query, $type)
    {
        return $this->whereFunctionName($type);
    }


    /**
     * Scope latest by Profile
     *
     * @param  Builder  $query
     * @param  int $profile
     * @param  int $limit
     * @return Builder
     */
    public function scopeLatestBy($query, $profile, $limit = 10)
    {
        return $query->latest()->whereCreator($profile)->limit($limit);
    }


    /**
     * Public link to the Report
     *
     * @return string
     */
    public function getLinkAttribute()
    {
        return link_to($this->url, $this->report_name);
    }


    /**
     * Resolve the Url for the Document
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return route('download', object_get($this->latest_document, 'md5'));
    }
}
