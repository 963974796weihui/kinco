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
Route::post('/admin/region','Admin\IndexController@region');
//用户模块
Route::get('/user/userInfo/{id}','Admin\UserController@userInfo');
Route::post('/user/addUser','Admin\UserController@addUser');
Route::post('/user/supplyGroup','Admin\UserController@supplyGroup');//设备组接口
Route::post('/user/supplyGroupBind','Admin\UserController@supplyGroupBind');//设备组绑定接口
Route::post('/user/hmiGroup','Admin\UserController@hmiGroup');//设备接口
Route::post('/user/hmiGroupBind','Admin\UserController@hmiGroupBind');//设备绑定接口
Route::get('/user/info/{id}','Admin\UserController@info');//获得用户信息
Route::post('/user/updateInfo','Admin\UserController@updateInfo');//编辑用户信息
Route::post('/user/search','Admin\UserController@search');//搜索用户
Route::get('/user/delete/{id}','Admin\UserController@delete');//删除用户
Route::get('/user/forbid/{id}','Admin\UserController@forbid');//禁用用户
