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


Route::post('/login','UserController@login');//用户登录
Route::post('/register','UserController@register');//用户注册
Route::post('/captcha/register','UserController@register_captcha');//注册验证码
Route::post('/captcha/forgetpass','UserController@captcha_forgetpass');//忘记密码验证码
Route::post('/resetpass/forget','UserController@resetpass_forget');//忘记密码->重置密码


//重置密码

Route::get('/test','TestController@index');

Route::group(['middleware' => 'checklogin'], function () {
    Route::post('/resetpass/set','UserController@resetpass_set');//忘记密码->重置密码
});