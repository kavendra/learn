<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class Permission extends Model
{
    public static function defaultPermissions()
    {
        return [           
            'viewPost',
            'addPost',
            'editPost',
            'deletePost',
        ];
    }
}
