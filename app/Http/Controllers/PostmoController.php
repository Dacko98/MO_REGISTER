<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;

class PostmoController extends Controller
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
            //Obsolete, use accesses in Models TODO
            $user = DB::table('mos')->where('user_id', $user_id)->get();
            return view('temp.viewmo')->with('mos', $user);
        }
        return view('temp.viewmo')->with('mos', []);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $mos = DB::table('mos')->orderBy('created_at', 'DESC')->get();
        return view('temp.viewmo')->with('mos', $mos);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('templates.mo.create');
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
        // dd($request);
        $data = request()->validate([
            'title' => 'required|unique:mos|max:255',
            'profile_image' => 'nullable|mimes:jpeg,jpg,png,gif', //max:1999 ak server nepodporuje viac ako 2mb subory
            'zameranie' => 'required',
            'druh' => 'required',
            'kraj' => 'required',
            'address' => 'required|max:255',
            'city' => 'required|max:255',
            'shortDescription' => 'required',
            'Description' => 'required',
        ]);
        // dd($request->input('kraj'));
        //image handling
        if($request->hasFile('profile_image'))
        {
            $fullFileName = $request->file('profile_image')->getClientOriginalName();
            $noextFileName = pathinfo($fullFileName, PATHINFO_FILENAME);
            $extensionFileName = $request->file('profile_image')->getClientOriginalExtension();
            $storeAs = $noextFileName.'_'.time().'.'.$extensionFileName;
            $saved = $request->file('profile_image')->storeAs('/mo_images', $storeAs);

            $image = Image::make(public_path("/files/mo_images/{$storeAs}"))->fit(800,800);
            $image->save();
        }
        else{
            $storeAs = 'default.jpg';
        }

        $updatedMo = new Mo;
        $updatedMo->title = $request->input('title');
        $updatedMo->region = $request->input('kraj');
        $updatedMo->orientation = $request->input('zameranie');
        $updatedMo->type = $request->input('druh');
        $updatedMo->address = $request->input('address');
        $updatedMo->city = $request->input('city');
        $updatedMo->shortDescription = $request->input('shortDescription');
        $updatedMo->Description = $request->input('Description');
        if ($request->get('website'))
            $updatedMo->website = $request->get('website');
        else
            $updatedMo->website = " ";
        $updatedMo->profile_image = $storeAs;

        $updatedMo->save();
        $updatedMo->addOrSetPerson(auth()->user()->id_user, 2);
        //$updatedMo->users()->attach();
        // dd($updatedMo);

        return Redirect::action('App\Http\Controllers\PostmoController@show', $updatedMo->id);
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
        $mo = Mo::findOrFail($id);
        $posts = DB::table('posts')->where([
            ['id_organization', $id]
        ])->get();
        return view('templates.mo.show', ['mo' => $mo, 'posts' => $posts]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $mo = Mo::findOrFail($id);
        //check user
        if($mo->getPermissions(Auth::user()->id_user) < 2 && (auth()->user()->role_id != 1))
        {
            return redirect('/mo')->with(['errors' => ['Toto neni tvoje tak to prosim neupravuj']]);
        }
        return view('templates.mo.edit')->with(['mo' => $mo]);
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
        //
        $data = $request->validate([
            'title' => 'required|max:255',
            'profile_image' => 'mimes:jpeg,jpg,png,gif|nullable', //max:1999 ak server nepodporuje viac ako 2mb subory
            'zameranie' => 'required',
            'druh' => 'required',
            'kraj' => 'required',
            'address' => 'required|max:255',
            'city' => 'required|max:255',
            // 'region' => 'required|max:255',
            'shortDescription' => 'required',
            'Description' => 'required',
        ]);

        if($request->hasFile('profile_image'))
        {
            $fullFileName = $request->file('profile_image')->getClientOriginalName();
            $noextFileName = pathinfo($fullFileName, PATHINFO_FILENAME);
            $extensionFileName = $request->file('profile_image')->getClientOriginalExtension();
            $storeAs = $noextFileName.'_'.time().'.'.$extensionFileName;
            // dd($storeAs);
            $saved = $request->file('profile_image')->storeAs('/mo_images', $storeAs);

            $image = Image::make(public_path("/files/mo_images/{$storeAs}"))->fit(800,800);
            $image->save();
        }

        // ulozi do mociek
        $updatedMo = Mo::find($id);
        $updatedMo->title = $request->input('title');
        $updatedMo->region = $request->input('kraj');
        $updatedMo->orientation = $request->input('zameranie');
        $updatedMo->type = $request->input('druh');
        $updatedMo->address = $request->input('address');
        $updatedMo->city = $request->input('city');
        $updatedMo->shortDescription = $request->input('shortDescription');
        $updatedMo->Description = $request->input('Description');
        $updatedMo->website = $request->input('website');

        if($request->hasFile('profile_image'))
        {
            $updatedMo->profile_image = $storeAs;
        }

        $updatedMo->save();
        return Redirect::action('App\Http\Controllers\PostmoController@show', $updatedMo->id);
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
        $mo = Mo::find($id);
        if($mo->getPermissions(Auth::user()->id_user) < 2 && (auth()->user()->role_id != 1)){
            return redirect('/mo')->with(['errors' => ['Toto neni tvoje tak to prosim neupravuj']]);
        }

        if ($mo->profile_image != "default.jpg")
        {
            Storage::delete('/mo_images/'.$mo->profile_image);
        }

        $mo->delete();

        // $mos = Mo::all();
        // return view('temp.viewmo')->with('mos', $mos);
        return Redirect::action('App\Http\Controllers\PostmoController@index');

    }
}
