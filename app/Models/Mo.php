<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
Use DB;
class Mo extends Model
{
    protected $guarded = [];
    protected $fillable = [
        'title',
        'address',
        'orientation',
        'type',
        'shortDescription',
        'Description',
        'profile_image',
        'website',
        'city',
        'region'
    ];
    public function users()
    {
        return $this->belongsToMany('App\Models\User', 'BIND_User_Organization', 'id_organization', 'id_user');
    }

    public function isUserPermitted($user_id){
        return ($this->users()->where('users.id_user',auth()->user()->id_user)->first() != null);
    }

    public function projects()
    {
        return $this->belongsToMany('App\Models\Project', 'BIND_Project_Organization', 'id_organization', 'id_project')->get();
    }

    public function posts()
    {
        return $this->hasMany('App\Models\Post', 'id_organization')->get();
    }

    /**
     * @param $permission_group 0 - Can view only
     *                          1 - Can post
     *                          3 - Can manage the project
     **/
    public function addOrSetPerson($user_id, $permission_group){
        DB::select(DB::raw("INSERT INTO BIND_User_Organization(id_user, id_organization, permission) VALUES(".$user_id.",".$this->id.",".$permission_group.") ON DUPLICATE KEY UPDATE permission=VALUES(permission)"));
    }


    public function getPermissions($user_id){
        $shit = DB::select(DB::raw("SELECT permission as result FROM BIND_User_Organization where id_organization =".$this->id." and id_user =".$user_id));
        if($shit == null)
            return 0; //Guest
        return $shit[0]->result;
    }

    // use HasFactory;
    protected $table = 'mos';
    public $primaryKey = 'id';
    public $timestamps = true;
}
