<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use App\Models\Project;
use App\Models\Mo;
use Intervention\Image\Facades\Image;

class PostprojectController extends Controller
{
    // user needs to be logged in
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

    public function mine()
    {
        if (auth()->check()) {
            // The user is logged in...
            $user_id = auth()->user()->id_user;
            //Obsolete, use accesses in models TODO
            $user = DB::table('projects')->where('user_id', $user_id)->get();
            return view('temp.viewproj')->with('projects', $user);
        }
        return view('temp.viewproj')->with('projects', []);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $projects = DB::table('projects')->orderBy('created_at', 'DESC')->limit(2)->get();

        $projects = DB::table('projects')->orderBy('created_at', 'DESC')->get();
        return view('temp.viewproj')->with('projects', $projects);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param $id The ID of project, under which
     *            the project is made. VERIFY
     *            if this project ID belongs to
     *            the requester.
     * @return \Illuminate\Http\Response
     */
    public function createProject($id)
    {
        return view('templates.project.create')->with('mosid', $id);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $this->validate($request, [
            'title' => ['required', 'unique:projects', 'max:255'],
            'image' => 'nullable|mimes:jpeg,jpg,png,gif', //max:1999 ak server nepodporuje viac ako 2mb subory
            'shortDescription' => 'required',
        ]);
        //Check if this new project belongs to organization the user has access to.
        $mos = Mo::findOrFail($request->input('mos_id'));
        $usr = $mos->users()->where('users.id_user',auth()->user()->id_user)->first();

        if($usr == null && (auth()->user()->Role_ID != 1))
        {
            return redirect('/project')->with(['errors' => ['Nepatríte k organizácií, do ktorej chcete vložiť projekt!']]);
        }

        //image handling
        if($request->hasFile('image'))
        {
            $fullFileName = $request->file('image')->getClientOriginalName();
            $noextFileName = pathinfo($fullFileName, PATHINFO_FILENAME);
            $extensionFileName = $request->file('image')->getClientOriginalExtension();
            $storeAs = $noextFileName.'_'.time().'.'.$extensionFileName;
            // dd($storeAs);
            $saved = $request->file('image')->storeAs('/project_images', $storeAs);

            $image = Image::make(public_path("/files/project_images/{$storeAs}"))->fit(800,800);
            $image->save();
        }
        else{
            $storeAs = 'default.jpg';
        }

        $updateProj = new Project;
        $updateProj->title = $request->input('title');
        $updateProj->shortDescription = $request->input('shortDescription');
        if($request->input('description'))
            $updateProj->description = $request->input('description');
        else
            $updateProj->description = "";
        $updateProj->from = $request->input('from');
        $updateProj->to = $request->input('to');
        if($request->input('volunteers') == null){
            $updateProj->volunteers = 0;
        }else{
            $updateProj->volunteers = $request->input('volunteers');
        }
        $updateProj->image = $storeAs;
        $updateProj->save();
        $updateProj->mos_req_set()->attach($mos->id);

        // $projects = Project::orderBy('created_at', 'desc'); //podla vytvorenia
        return Redirect::action('App\Http\Controllers\PostprojectController@show', $updateProj->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $projekt = Project::findOrFail($id);
        $posts = DB::table('posts')->where([
            ['id_project', $id]
        ])->get();
        // dd($posts);
        return view('templates.project.show', ['proj' => $projekt, 'posts' => $posts]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $proj = Project::findOrFail($id);

        if(!$proj->canEdit(auth()->user()->id_user) && (auth()->user()->role_id != 1))
        {
            return redirect('/project')->with(['errors' => ['Toto neni tvoje tak to prosim neupravuj']]);
        }
        return view('templates.project.edit')->with(['proj' => $proj]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'title' => 'required',
            'image' => 'nullable|mimes:jpeg,jpg,png,gif', //max:1999 ak server nepodporuje viac ako 2mb subory
        ]);

        if($request->hasFile('image'))
        {
            $fullFileName = $request->file('image')->getClientOriginalName();
            $noextFileName = pathinfo($fullFileName, PATHINFO_FILENAME);
            $extensionFileName = $request->file('image')->getClientOriginalExtension();
            $storeAs = $noextFileName.'_'.time().'.'.$extensionFileName;
            // dd($storeAs);
            $saved = $request->file('image')->storeAs('/project_images', $storeAs);

            $image = Image::make(public_path("/files/project_images/{$storeAs}"))->fit(800,800);
            $image->save();
        }

        // ulozi do projektov
        $updateProj = Project::find($id);
        if((!$updateProj->canEdit(auth()->user()->id_user)) || (auth()->user()->role_id != 1))
        {
            return redirect('/project')->with(['errors' => ['Toto neni tvoje tak to prosim neupravuj']]);
        }
        $updateProj->title = $request->input('title');
        $updateProj->shortDescription = $request->input('shortDescription');
        $updateProj->description = $request->input('description');
        if($request->input('volunteers') == null){
            $updateProj->volunteers = 0;
        }else{
            $updateProj->volunteers = $request->input('volunteers');
        }
        $updateProj->from = $request->input('from');
        $updateProj->to = $request->input('to');
        if($request->hasFile('image'))
        {
            $updateProj->image = $storeAs;
        }

        $updateProj->save();
        return Redirect::action('App\Http\Controllers\PostprojectController@show', $updateProj->id);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $proj = Project::find($id);
        if(!$proj->canEdit(auth()->user()->id_user) && (auth()->user()->role_id != 1))
        {
            return redirect('/project')->with(['errors' => ['Toto neni tvoje tak to prosim nemaz']]);
        }

        if ($proj->image != "default.jpg") {
            Storage::delete('/project_images/'.$proj->image);
        }

        $proj->delete();

        // $projs = Project::all();
        // return view('temp.viewproj')->with('projects', $projs);
        return Redirect::action('App\Http\Controllers\PostprojectController@index');

    }
}
