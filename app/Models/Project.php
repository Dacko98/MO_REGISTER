<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
Use DB;

class Project extends Model
{
    protected $guarded = array(
        'shortDescription',
    );

    public function mos()
    {
        return $this->belongsToMany('App\Models\Project', 'BIND_Project_Organization', 'id_project', 'id_organization')->get();
    }

    public function mos_req_set()
    {
        return $this->belongsToMany('App\Models\Project', 'BIND_Project_Organization', 'id_project', 'id_organization');
    }

    public function posts()
    {
        return $this->hasMany('App\Models\Post', 'id_project')->get();
    }

    public function users()
    {
        return $this->hasManyThrough('App\Models\User', 'BIND_Project_Organization', 'country_id', 'user_id', '', '');
    }

    public function canEdit($user_id){
        $shit = DB::select(DB::raw("SELECT count(id_user) as result FROM (SELECT id_organization FROM BIND_Project_Organization where id_project =".$this->id.") as L1 natural join BIND_User_Organization where permission > 1 and id_user =".$user_id));
        if($shit[0]->result > 0){
            return true;
        }
        return false;
    }

    //Functionality removed as of commit 29.11.2020 2:25
    /*public function user(){
        return $this->belongsTo('App\Models\User', 'user_id');
    }*/
    // use HasFactory;
    protected $table = 'projects';
    public $primaryKey = 'id';
    public $timestamps = true;
}
