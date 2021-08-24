<?php

namespace App\Http\Controllers;

use App\Models\Mo;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
Use DB;

class AjaxController extends Controller {

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request) {
        if($request->input('recpx') != null){
            if($request->input('recpx') == 1){ //Get list of users
                $shit = DB::select(DB::raw("select name, id_user from users where name like \"".$request->input('input')."%\" and name not in (select name from users natural join BIND_User_Organization where id_organization=".$request->input("orgid").") limit 10"));
                return response()->json(array($shit), 200);
            }else if($request->input('recpx') == 2){ //Add user to organization
                $mo = Mo::find($request->input('orgid'));
                if($mo->getPermissions(Auth::user()->id_user) < 2){
                    return response()->json(array('response'=> 'Do not hack!'), 502);
                }
                $uid = $request->input('usr');//DB::select(DB::raw("select id_user from users where name like \"".$request->input('usr')."\""));
                if($uid != null){
                    if($request->input('perm') >= 0 && $request->input('perm') <= 2){
                        $mo->addOrSetPerson($uid, $request->input('perm'));
                    }else{
                        return response()->json(array('response'=> 'Do not hack!'), 502);
                    }
                    return response()->json(array(), 200);
                }
                return response()->json(array(), 502);
            }else if($request->input('recpx') == 3){ //Request membership
                $mo = Mo::find($request->input('orgid'));
                if($mo->getPermissions(Auth::user()->id_user) == 0 && $request->input('perm') > 0 && $request->input('perm') <= 2){
                    $mo->addOrSetPerson(Auth::user()->id_user, -$request->input('perm'));
                    return response()->json(array(), 200);
                }else{
                    return response()->json(array('response'=> 'Do not hack!'), 502);
                }
            }else if($request->input('recpx') == 4){ //List membership requests
                $mo = Mo::find($request->input('orgid'));
                if($mo->getPermissions(Auth::user()->id_user) > 1){ //If admin
                    $shit = DB::select(DB::raw("select name, id_user, permission from users natural join BIND_User_Organization where id_organization=".$request->input("orgid")." and permission < 0"));
                    return response()->json(array($shit), 200);
                }else{
                    return response()->json(array('response'=> 'Do not hack!'), 502);
                }
            }else if($request->input('recpx') == 5){ //Accept membership requests
                $mo = Mo::find($request->input('orgid'));
                if($mo->getPermissions(Auth::user()->id_user) > 1){ //If admin
                    $shit = DB::update(DB::raw("UPDATE BIND_User_Organization SET `permission` = abs(`permission`) where id_user = ".$request->input('usr')." and id_organization = ".$request->input('orgid')));
                    return response()->json(array(), 200);
                }else{
                    return response()->json(array('response'=> 'Do not hack!'), 502);
                }
            }
        }
        $uid = $request->input('uid');
        $usr = ($uid == null)? Auth::user() : User::find($uid);

        if (sizeof($request->files) > 0) {
            //  Let's do everything here

            if ($request->file('file')->isValid()) {
                $extension = $request->file->extension();
                //$fm = $request->file->getClientOriginalName();
                $filename = time().'.'.$extension;

                if($usr != null)
                {
                    $pth = $usr->profile_picture;
                    Storage::delete('./userassets/'.$pth);
                }
                $request->file->storeAs('./userassets/', $filename);

                $usr->update(['profile_picture' => $filename]);
                //Session::flash('success', "Success!");
                return response()->json(array('msg'=> User::$picturesPath.$filename), 200);
            }
        }else{
            $id = $request->input('id');
            $val = $request->input('valuer');
            if($id == 'user_description'){
                $usr->update(['info' => $val]);
            }else if($id == 'user_address'){
                $usr->update(['address' => $val]);
            }else if($id == 'user_birthday'){
                try{
                $validatedData = $request->validate([
                    'valuer' => 'date|date_format:Y-m-d',
                ]);
                    $usr->update(['birthday' => $val]);
                } catch (\Illuminate\Validation\ValidationException $e ) {
                    return response()->json(array('color'=> 'red'), 201);
                }
            }else if($id == 'user_email'){
                $usr->update(['email' => $val]);
            }else{
                return response()->json(array(), 500);
            }
            return response()->json(array('color'=> 'lime'), 200);
        }
    }
}
