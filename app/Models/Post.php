<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    public function get_mo_if_exists(){
        return $this->hasOne('App\Models\Mo', "id_organization")->get();
    }

    public function get_proj_if_exists(){
        return $this->hasOne('App\Models\Project', "id_project")->get();
    }

    public function user(){
        return $this->belongsTo('App\Models\User', 'user_id')->get();
    }
}
