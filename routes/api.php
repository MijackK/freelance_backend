<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\Job;
use App\Models\Order;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:auth')->get('/user', function (Request $request) {
    //return $request->user();
//});

Route::middleware('auth:sanctum')->get('/job', 'JobController@index');
Route::middleware('auth:sanctum')->get('/jobs', 'JobController@show');
Route::middleware('auth:sanctum')->post('/job', 'JobController@store');
Route::middleware('auth:sanctum')->put('/job', 'JobController@update');
Route::middleware('auth:sanctum')->delete('/job/{job}', 'JobController@destroy');
Route::middleware('auth:sanctum')->get('/userjobs', 'JobController@userJobs');
Route::middleware('auth:sanctum')->get('/profilejobs/{id}', 'JobController@profilejobs');
Route::middleware('auth:sanctum')->get('/jobProfile/{id}', 'JobController@jobProfile');

Route::middleware('auth:sanctum')->get('/order', 'OrderController@index');
Route::middleware('auth:sanctum')->get('/hired', 'OrderController@showHired');
Route::middleware('auth:sanctum')->post('/order', 'OrderController@store');
Route::middleware('auth:sanctum')->post('/validattions', 'OrderController@validattions');
Route::middleware('auth:sanctum')->put('/request', 'OrderController@request');

Route::middleware('auth:sanctum')->get('/messages', 'MessageController@index');
Route::middleware('auth:sanctum')->post('/messages', 'MessageController@store');


Route::middleware('auth:sanctum')->get('/notification', 'NotificationController@index');
Route::middleware('auth:sanctum')->delete('/notification/{notification}', 'NotificationController@destroy');


Route::middleware('auth:sanctum')->get('/transaction', 'TransactionController@index');
Route::middleware('auth:sanctum')->get('/receipt/{transaction}', 'TransactionController@receipt');

Route::middleware('auth:sanctum')->post('/review', 'ReviewController@store');
Route::middleware('auth:sanctum')->get('/review/{id}', 'ReviewController@getReview');
Route::middleware('auth:sanctum')->get('/userReview/{id}', 'ReviewController@userReview');
Route::middleware('auth:sanctum')->get('/myReview', 'ReviewController@index');

Route::middleware('auth:sanctum')->put('/view/{id}', function($id){
    DB::table('jobs')->where('id',$id)->increment('views');

});


Route::get('/price/{id}',function ($id) {
    return Job::select('price')->find($id);
});

Route::get('/user',function () {
    return auth()->user()->id;
});

Route::get('/userinfo',function () {
    return [auth()->user()->id,auth()->user()->name,auth()->user()->avatar];
});

Route::get('/userinfo/{id}',function ($id) {
    return User::select('name','avatar')->find($id);
});