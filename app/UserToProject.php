<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserToProject extends Model
{
    protected $table = 'user_to_project';

    protected $fillable = [
        'user_id', 'project_id',
    ];
   
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
