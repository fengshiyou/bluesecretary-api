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
Route::post('/register/captcha','UserController@register_captcha');//注册验证码
//忘记密码
//重置密码

Route::get('/test','TestController@index');

Route::group(['middleware' => 'checklogin'], function () {

});