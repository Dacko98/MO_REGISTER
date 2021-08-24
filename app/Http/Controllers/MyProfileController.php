<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class MyProfileController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        /*$a = 1;
        foreach (auth()->user()->mos() as $mo) {
            $v = $mo->title;
            $a = 1;
        }*/
        //$user = DB::table('projects')->where('user_id', $user_id)->get();
        return view('myprofile')->with(['mos'=>auth()->user()->mos()->sortByDesc('created_at'),'usr'=>Auth::user()]);//->with('projects',$user);
    }

    public function administer_account($usrid){
        if(auth()->user()->role_id == 1){
            $user = User::findOrFail($usrid);
            return view('myprofile')->with(['mos'=>$user->mos()->sortByDesc('created_at'),'usr'=>$user]);//->with('projects',$user);
        }
        else{
            return redirect('/');
        }
    }

    public function destroy(Request $request)
    {
        $user = User::findOrFail($request->id);
        $user->delete();
        return redirect('/login');
    }
}
