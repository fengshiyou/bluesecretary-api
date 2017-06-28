<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});
//github hook
Route::post('/bluesecretaryHook','GithubHookController@index');

//用户登录
Route::post('/login','UserController@login');

//用户注册
Route::post('/register','UserController@register');



Route::group(['middleware' => 'checklogin'], function () {
    Route::get('/test','TestController@index');
});