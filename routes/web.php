<?php

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('api/user','Api\UserApiController@userInfo');
Route::any('api/reg','Api\UserApiController@register');
Route::any('api/login','Api\UserApiController@login');
//个人中心
Route::any('user/center','Api\UserApiController@userCenter')->middleware('check_login');

Route::get('api/base64','Api\UserApiController@base64');
Route::get('api/testbase64','Api\UserApiController@testBase64');


Route::resource('goods',GoodsController::class);