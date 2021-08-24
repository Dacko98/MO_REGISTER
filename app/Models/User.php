<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Carbon\Carbon as BaseCarbon;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    //app/providers/RouteServiceProvider
    public static $profilePath = '/myprofile';
    public static $picturesPath = '/files/userassets/';

    protected $table = 'users';
    protected $primaryKey = 'id_user';

    //protected $dateFormat = 'DD-MM-YYYY';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'address',
        'password',
        'email',
        'info',
        'birthday',
        'profile_picture',
        'email_verified_at',
        'remember_token',
        'settings',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getAuthPassword(){
        return $this->password;
    }

    public function mos(){
        return $this->belongsToMany('App\Models\Mo', 'BIND_User_Organization',  'id_user','id_organization')->withPivot('id_user')->get();
    }

    public function projects(){
        return $this->hasMany('App\Models\Project', 'user_id')->get();
    }

    public function posts(){
        return $this->hasMany('App\Models\Post', 'user_id')->get();
    }

    public function getProfilePicture(){
        if($this->profile_picture == ""){
            return User::$picturesPath.'default.png';
        }else{
            return User::$picturesPath.$this->profile_picture;
        }
    }
}
