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
//超管模块
Route::post('/admin/register','Admin\IndexController@register');
Route::post('/admin/login','Admin\IndexController@login');
Route::post('/admin/registerDomain','Admin\IndexController@registerDomain');
//用户模块
Route::get('/user/userInfo/{id}','Admin\UserController@userInfo');
Route::post('/user/addUser','Admin\UserController@addUser');
Route::post('/user/supplyGroup','Admin\UserController@supplyGroup');
Route::post('/user/supplyGroupBind','Admin\UserController@supplyGroupBind');//设备组绑定接口
Route::post('/user/hmiGroup','Admin\UserController@hmiGroup');
Route::post('/user/hmiGroupBind','Admin\UserController@hmiGroupBind');//设备组绑定接口