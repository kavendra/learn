<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    public function setPasswordAttribute($password)
    {   
        $this->attributes['password'] = bcrypt($password);
    }

    public function roles()
    {
      return $this->belongsToMany(Role::class);
    }
    
    public function projects()
    {
        return $this->belongsToMany(Project::class, 'user_to_project');
    }

    public function getDevelopAttribute(){

        return $this->projects->where('type_id', 1);
    }

    public function getUatDevelopAttribute(){
        return $this->projects->where('type_id', 2);
    }

    public function getProductionDevelopAttribute(){
        return $this->projects->where('type_id', 3);
    }

    public function userRole()
    {
        return $this->hasOne('App\RoleUser');
    }

     public function getRoleAttribute()
    {
        if($this->userRole){
        return $this->userRole->role->name;
        }
    }

    /**
    * @param string|array $roles
    */
    public function authorizeRoles($roles)
    {
      if (is_array($roles)) {
          return $this->hasAnyRole($roles) || 
                 abort(401, 'This action is unauthorized.');
      }
      return $this->hasRole($roles) || 
             abort(401, 'This action is unauthorized.');
    }
    /**
    * Check multiple roles
    * @param array $roles
    */
    public function hasAnyRole($roles)
    {
      return null !== $this->roles()->whereIn('name', $roles)->first();
    }
    /**
    * Check one role
    * @param string $role
    */
    public function hasRole($role)
    {
      return null !== $this->roles()->where('name', $role)->first();
    }
    
}
