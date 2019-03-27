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
Route::get('/admin/confirm','Admin\IndexController@confirm');//邮箱确认
//用户模块
Route::get('/user/userInfo','Admin\UserController@userInfo');
Route::post('/user/addUser','Admin\UserController@addUser');
Route::post('/user/supplyGroup','Admin\UserController@supplyGroup');//设备组接口
Route::post('/user/supplyGroupBind','Admin\UserController@supplyGroupBind');//设备组绑定接口
Route::post('/user/hmiGroup','Admin\UserController@hmiGroup');//设备接口
Route::post('/user/hmiGroupBind','Admin\UserController@hmiGroupBind');//设备绑定接口
Route::get('/user/info/{id}','Admin\UserController@info');//获得用户信息
Route::post('/user/updateInfo','Admin\UserController@updateInfo');//编辑用户信息
Route::get('/user/delete/{id}','Admin\UserController@delete');//删除用户
Route::get('/user/forbid/{id}','Admin\UserController@forbid');//禁用用户
//设备模块
Route::post('/supply/supplyInfo','Admin\SupplyController@supplyInfo');//搜索设备
Route::post('/supply/forbid/{id}','Admin\SupplyController@forbid');//禁用设备
Route::post('/supply/addSupply','Admin\SupplyController@addSupply');//新增设备
//设备组模块
Route::post('/group/addGroup','Admin\GroupController@addGroup');//新增设备组
Route::post('/group/supplyInfo','Admin\GroupController@supplyInfo');//域下设备组信息
Route::post('/group/updateGroup','Admin\GroupController@updateGroup');//更新组名
Route::post('/group/deleteGroup','Admin\GroupController@deleteGroup');//删除组
Route::post('/group/hmiInfo','Admin\GroupController@hmiInfo');//管理组成员
Route::post('/group/hmiInfoBind','Admin\GroupController@hmiInfoBind');//管理组成员确认
Route::post('/group/addUser','Admin\GroupController@addUser');//绑定用户
Route::post('/group/addUserBind','Admin\GroupController@addUserBind');//绑定用户确认
//授权码模块
Route::post('/AuthCode/codeInfo','Admin\AuthCodeController@codeInfo');//授权码信息
