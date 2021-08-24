<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Mo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NewsController extends Controller
{
    public function index()
    {
        $projects = DB::table('projects')->orderBy('created_at', 'DESC')->get();
        return view('news')->with('projects', $projects);
    }

    public function moDescription(Request $request)
    {

        if ($request->ajax()) {
            $query1 = $request->input('query');
        }
        $mo = $mo = Mo::findOrFail($query1);
        return view('templates.ajax.moDescription')->with('mo', $mo);
    }

    public function projectDescription(Request $request)
    {

        if ($request->ajax()) {
            $query1 = $request->input('query');
        }
        $project = Project::findOrFail($query1);
        return view('templates.ajax.projectDescription')->with('project', $project);
    }

    public function moMembers(Request $request)
    {

        if ($request->ajax()) {
            $query1 = $request->input('query');
        }
        $users = DB::select(DB::raw("SELECT u.* FROM users u, BIND_User_Organization b WHERE u.id_user = b.id_user AND b.id_organization =" . $query1));
        return view('templates.ajax.members')->with('users', $users);
    }

    public function moPosts(Request $request)
    {

        if ($request->ajax()) {
            $query1 = $request->input('query');
        }
        $posts = DB::table('posts')->where([
            ['id_organization', $query1]
        ])->get();
        return view('templates.ajax.moPosts')->with('posts', $posts)->with('mo', $query1);
    }

    public function projectPosts(Request $request)
    {

        if ($request->ajax()) {
            $query1 = $request->input('query');
        }
        $posts = DB::table('posts')->where([
            ['id_project', $query1]
        ])->get();
        return view('templates.ajax.projectPosts')->with('posts', $posts)->with('project', $query1);
    }

    public function moProjects(Request $request)
    {

        if ($request->ajax()) {
            $query1 = $request->input('query');
        }
        $projects = DB::select(DB::raw("SELECT p.* FROM projects p, BIND_Project_Organization b WHERE p.id = b.id_project AND b.id_organization =" . $query1));
        return view('templates.ajax.moProjects')->with('projects', $projects);
    }

    public function projectMo(Request $request)
    {

        if ($request->ajax()) {
            $query1 = $request->input('query');
        }
        $mo = DB::select(DB::raw("SELECT m.* FROM mos m, BIND_Project_Organization b WHERE m.id = b.id_organization AND b.id_project =" . $query1));
        return view('templates.ajax.projectMo')->with('mo', $mo);
    }

    public function adminSite()
    {   
        if (auth()->check()){
            if (auth()->user()->role_id == 1){
                $users = User::all();
                return view('adminSite')->with('users', $users);
            }
            else{
                return redirect('/');
            }
        }
        else{
            return redirect('/');
        }
    }


    public function ide()
    {
        if (!Auth::guest()) {
            Auth::logout();
        }
    }
}
