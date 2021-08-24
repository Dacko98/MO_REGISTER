<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use App\Models\Post;
use App\Models\Project;
use App\Models\Mo;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;

class PostsController extends Controller
{
    // user needs to be logged in
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

    public function createPost($id, $source) //id -> id modelu ku ktoremu to patri , source = {1:"projekt", 2:"organization"}
    {
        $data = [
            (int)$id,
            (int)$source
        ];
        return view('templates.post.create')->with('data', $data);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($pid, $id, $source)
    {
        $data = [
            (int)$id,
            (int)$source
        ];
        $post = Post::findOrFail($pid);
        return view('templates.post.edit')->with('data', $data)->with('post', $post);
    }

    public function mine()
    {
        if (auth()->check()) {
            // The user is logged in...
            $user_id = auth()->user()->id_user;
            $user = DB::table('posts')->where([
                                            ['user_id', $user_id],
                                            ['source', 1]                   //len posty pre projekty
                                            ])->get();
            return view('temp.viewpost')->with('posts', $user);
        }
        return view('temp.viewpost')->with('posts', []);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $posts = Post::all();
        return view('temp.viewpost')->with('posts', $posts);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('templates.post.create');
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
        $post = Post::findOrFail($id);
        return view('templates.post.show', ['post' => $post]);
    }



    private function create_or_update_post($request, $update_mode){
        //=====[VALIDATE]======
        $src = $request->input('source');
        $id = $request->input('id');
        $home = "/";
        if($src == 1){
            if(!Project::findOrFail($id)->canEdit(auth()->user()->id_user) && (auth()->user()->Role_ID != 1)){
                return redirect($home)->with(['errors' => ['Toto neni tvoje tak to prosim neupravuj']]);
            }
            $home = "/project/".$id;
        }
        else if($src == 2){
            if(Mo::findOrFail($id)->getPermissions(Auth::user()->id_user) < 1 && (auth()->user()->Role_ID != 1)){
                return redirect($home)->with(['errors' => ['Toto neni tvoje tak to prosim neupravuj']]);
            }
            $home = "/mo/".$id;
        }
        else{
            return redirect('/')->with(['errors' => ['Unknown parameter']]);
        }

        $this->validate($request, [
            'title' => 'required',
            'body' => 'required',
            'image.*' => 'nullable|mimes:jpeg,jpg,png,gif', //max:1999 ak server nepodporuje viac ako 2mb subory
            'image' => 'max:6'
        ]);
        //=====[MAIN LOGIC]======
        if($request->hasFile('image'))
        {
            foreach ($request->file('image') as $file) {
                $fullFileName = $file->getClientOriginalName();
                $noextFileName = pathinfo($fullFileName, PATHINFO_FILENAME);
                $extensionFileName = $file->extension();
                $storeAs = $id.'_'.$noextFileName.'_'.time().'.'.$extensionFileName;
                $saved = $file->storeAs('/post_images', $storeAs);

                $image = Image::make(public_path("/files/post_images/{$storeAs}"))->fit(800,800);
                $image->save();

                $data[] = $storeAs; //ulozene nazvy suborov
            }
        }
        if($update_mode){
            // ulozi post
            $post = Post::find($request->input('pid'));
            $post->title = $request->input('title');
            $post->body = $request->input('body');
            if($request->hasFile('image')){
                $post->filenames = json_encode($data);
            }
        }else{ //Store new
            if(!$request->hasFile('image')){
                $data = [];
            }

            $post = new Post();
            $post->title = $request->input('title');
            $post->body = $request->input('body');
            $post->filenames = json_encode($data);
            $post->user_id = auth()->user()->id_user;
            if($src == 1){ //Project
                $post->id_project = $request->input('id');
            }else{
                $post->id_organization = $request->input('id');
            }
        }

        //=====[EPILOGUE]======
        $post->save();
        if($src == 1) //je to projekt
        {
            return Redirect::action('App\Http\Controllers\PostprojectController@show', $post->id_project);
        }
        else
        {
            return Redirect::action('App\Http\Controllers\PostmoController@show', $post->id_organization);
        }
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
        $post = Post::find($id);
        if(($post->user_id !== auth()->user()->id_user) && (auth()->user()->role_id != 1))
        {
            return redirect('/post')->with(['errors' => ['Toto neni tvoje tak to prosim nemaz']]);
        }

        if (json_decode($post->filenames) != []) {
            $myFiles = json_decode($post->filenames);
            foreach($myFiles as $file) {
                if (! Storage::exists('/files/post_images/'.$file)) {
                    Storage::delete('/files/post_images/'.$file);
                }
            }
        }
        $redirect = null;
        if($post->id_organization == null) //je to projekt
        {
            $redirect = Redirect::action('App\Http\Controllers\PostprojectController@show', $post->id_project);
        }
        else {
            $redirect = Redirect::action('App\Http\Controllers\PostmoController@show', $post->id_organization);
        }
        $post->delete();
        return $redirect;
    }

    //===========[Interface functions]===========
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        return $this->create_or_update_post($request, false);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id){
        return $this->create_or_update_post($request, true);
    }
    //===========================================
}

