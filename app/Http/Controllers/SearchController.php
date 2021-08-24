<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Mo;
use App\Models\User;
use App\Models\Post;
use DB;

class SearchController extends Controller
{
    //
    public function find(Request $request)
    {
        $ret = [];
        if ($request->get('Project')) {
            $results = Project::where('title', 'LIKE', '%' . $request->get('Project') . '%')->get();//->orWhere('','LIKE','%'.$p.'%')->get();
            return view('temp.viewproj')->with('projects', $results);
        }

        if ($request->get('Mo')) {
            $results = Mo::where('title', 'LIKE', '%' . $request->get('Mo') . '%')->get();//->orWhere('','LIKE','%'.$p.'%')->get();
            return view('temp.viewmo')->with('mos', $results);
        }
        return view('temp.viewproj')->with('projects', $ret);
    }


    public function searchMo(Request $request)
    {
        if ($request->ajax()) {
            $query1 = $request->input('query1');
            $query2 = $request->input('query2');
            $query3 = $request->input('query3');
            $query4 = $request->input('query4');
            $query5 = $request->input('query5');

            $data = DB::table('mos');
            if ($query1 != '') {
                $data = $data->where('region', 'like', '%' . $query1 . '%');
            }

            if ($query2 != '') {
                $data = $data->where('type', 'like', '%' . $query2 . '%');
            }
            if ($query3 != '') {
                $data = $data->where('orientation', 'like', '%' . $query3 . '%');
            }

            \Log::info(" ------------------------------------------------------" . $query1 . " ------------------------------------------------------");
            if ($query4 != '') {
                $data = $data->where('city', 'like', '%' . $query4 . '%');
            }

            if ($query5 != '') {
                $data = $data->where('title', 'like', '%' . $query5 . '%');
            }

            $data = $data->get();

            $output = '';
            $output = '';
            if ($data->count() > 0) {
                foreach ($data as $row) {
                    $output .= '
            <a class="lnk" href="/mo/' . $row->id . '">
                <div class="org">
                    <h2>' . $row->title . '</h2>
                    <img id="img"
                         src="/files/mo_images/' . $row->profile_image . '">
                    <p><b>Mesto: </b> ' . $row->city . '</p>
                    <p class="desc"><b>Popis: </b> ' . $row->shortDescription . '</p>
                </div>
            </a>';
                }
            } else {
                $output = '<p class="centerText"> Nenašla sa žiadna organizácia vyhovujúca kritériám </p >';
            }


            return response()->json(array('msg' => $output), 200);
        }
    }

    public function searchProject(Request $request)
    {

        if ($request->ajax()) {
            $query1 = $request->input('query1');
            $query2 = $request->input('query2');
            $query3 = $request->input('query3');

            $data = DB::table('projects');
            if ($query1 == 1) {
                $data = $data->where('volunteers', 1);
            }

            if ($query2 == 1) {
                $data = $data->whereDate('to', '>=', date('Y-m-d'));
            }

            if ($query3 != '') {
                $data = $data->where('title', 'like', '%' . $query3 . '%');
            }


            $data = $data->get();

            $output = '';
            if ($data->count() > 0) {
                foreach ($data as $row) {
                    if ($row->volunteers) {
                        $output .= '
            <a class="lnk" href="/project/' . $row->id . '">
                <div class="org">
                    <img id="img"
                         src="/files/project_images/' . $row->image . '">
                         <div class="dobrovol1">
                            <h3>' . $row->title . '</h3>
                            <p><b>Od: </b> ' . $row->from . '</p>
                            <p><b>Do: </b> ' . $row->to . '</p>
                         </div>
                        <div class="dobrovol">
                            <h4>Hľadáme dobrovoľníkov</h4>
                        </div>
                </div>
            </a>';
                    } else {
                        $output .= '
            <a class="lnk" href="/project/' . $row->id . '">
                <div class="org">
                    <img id="img"
                         src="/files/project_images/' . $row->image . '">
                         <div class="dobrovol1">
                            <h3>' . $row->title . '</h3>
                            <p><b>Od: </b> ' . $row->from . '</p>
                            <p><b>Do: </b> ' . $row->to . '</p>
                         </div>
                </div>
            </a>';
                    }
                }
            } else {
                $output = '<p class="centerText"> Nenašiel sa žiadny projekt vyhovujúci kritériám </p >';
            }


            return response()->json(array('msg' => $output), 200);
        }
    }
}
