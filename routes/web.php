<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('ajax',function() {
    return view('message');
});

Route::get('/post/{id}/{source}/create', 'App\Http\Controllers\PostsController@createPost');
Route::get('/post/{prispevok_id}/{id}/{source}/edit', 'App\Http\Controllers\PostsController@edit');

Route::get('/mo/{id}/createproject', 'App\Http\Controllers\PostprojectController@createProject');

Route::post('/getmsg',[App\Http\Controllers\AjaxController::class, 'index']);


Route::post('/sp', 'App\Http\Controllers\PostprojectController@store');
Route::post('/sm', 'App\Http\Controllers\PostmoController@store');
Route::post('/mo/create', 'App\Http\Controllers\PostmoController@create');

Route::get('project/mine', 'App\Http\Controllers\PostprojectController@mine');
Route::resource('project', 'App\Http\Controllers\PostprojectController', ['middleware' => ['auth.timeout']]);

Route::get('post/mine', 'App\Http\Controllers\PostsController@mine');
Route::resource('post', 'App\Http\Controllers\PostsController', ['middleware' => ['auth.timeout']]);

Route::get('mo/mine', 'App\Http\Controllers\PostmoController@mine');
Route::resource('mo', 'App\Http\Controllers\PostmoController', ['middleware' => ['auth.timeout']]);

Auth::routes();

Route::get(User::$profilePath."/{id}", [App\Http\Controllers\MyProfileController::class, 'administer_account']);
Route::get(User::$profilePath, [App\Http\Controllers\MyProfileController::class, 'index'])->name('home');
Route::get('/moDescription', [App\Http\Controllers\NewsController::class, 'moDescription']);
Route::get('/moMembers', [App\Http\Controllers\NewsController::class, 'moMembers']);
Route::get('/moProjects', [App\Http\Controllers\NewsController::class, 'moProjects']);
Route::get('/moPosts', [App\Http\Controllers\NewsController::class, 'moPosts']);
Route::get('/projectDescription', [App\Http\Controllers\NewsController::class, 'projectDescription']);
Route::get('/projectPosts', [App\Http\Controllers\NewsController::class, 'projectPosts']);
Route::get('/projectMo', [App\Http\Controllers\NewsController::class, 'projectMo']);

Route::get('/admin', 'App\Http\Controllers\NewsController@adminSite');

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::post('/search', 'App\Http\Controllers\SearchController@find');
Route::post('/searchMo', 'App\Http\Controllers\SearchController@searchMo');
Route::post('/searchProject', 'App\Http\Controllers\SearchController@searchProject');
Route::post('/remove', 'App\Http\Controllers\MyProfileController@destroy');
