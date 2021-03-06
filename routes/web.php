<?php
Route::get('/', function () {
    return 'Hello World';
});
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
    Route::post('/admin/register','Admin\IndexController@register');
    Route::post('/admin/login','Admin\IndexController@login');
    Route::get('/admin/confirm','Admin\IndexController@confirm');//邮箱确认
    Route::get('/admin/confirmer','Admin\IndexController@confirmer');//邮箱确认
    Route::get('/admin/logout','Admin\IndexController@logout');//退出登录
//超管模块
Route::group(['middleware' => ['Login']], function () {
    Route::post('/admin/registerDomain','Admin\IndexController@registerDomain');
    Route::post('/admin/region','Admin\IndexController@region');
    //用户模块
    Route::get('/user/userInfo','Admin\UserController@userInfo');
    Route::post('/user/addUser','Admin\UserController@addUser');
    Route::post('/user/supplyGroup','Admin\UserController@supplyGroup');//设备组接口
    Route::post('/user/supplyGroupBind','Admin\UserController@supplyGroupBind');//设备组绑定接口
    Route::post('/user/unsupplyGroupBind','Admin\UserController@unSupplyGroupBind');//设备组解绑接口
    Route::post('/user/hmiGroup','Admin\UserController@hmiGroup');//设备接口
    Route::post('/user/hmiGroupBind','Admin\UserController@hmiGroupBind');//设备绑定接口
    Route::get('/user/info/{id}','Admin\UserController@info');//获得用户信息
    Route::post('/user/updateInfo','Admin\UserController@updateInfo');//编辑用户信息
    Route::get('/user/delete','Admin\UserController@delete');//删除用户
    Route::post('/user/forbid','Admin\UserController@forbid');//禁用用户
    Route::post('/user/unforbid','Admin\UserController@unforbid');//解禁用户
    //设备模块
    Route::post('/supply/supplyInfo','Admin\SupplyController@supplyInfo');//搜索设备
    Route::post('/supply/forbid','Admin\SupplyController@forbid');//禁用设备
    Route::post('/supply/unforbid','Admin\SupplyController@unforbid');//解禁设备
    Route::post('/supply/addSupply','Admin\SupplyController@addSupply');//新增设备
    Route::post('/supply/deleteSupply','Admin\SupplyController@deleteSupply');//新增设备
    //设备组模块
    Route::post('/group/addGroup','Admin\GroupController@addGroup');//新增设备组
    Route::post('/group/supplyInfo','Admin\GroupController@supplyInfo');//域下设备组信息
    Route::post('/group/updateGroup','Admin\GroupController@updateGroup');//更新组名
    Route::post('/group/deleteGroup','Admin\GroupController@deleteGroup');//删除组
    Route::post('/group/hmiInfo','Admin\GroupController@hmiInfo');//管理组成员
    Route::post('/group/hmiInfoBind','Admin\GroupController@hmiInfoBind');//管理组成员确认
    Route::post('/group/unhmiInfoBind','Admin\GroupController@unhmiInfoBind');//管理组成员确认
    Route::post('/group/addUser','Admin\GroupController@addUser');//绑定用户
    Route::post('/group/addUserBind','Admin\GroupController@addUserBind');//绑定用户确认
    Route::post('/group/unaddUserBind','Admin\GroupController@unaddUserBind');//绑定用户确认
    Route::post('/group/hmidetail','Admin\GroupController@hmiDetail');//获得组下人机名
    //授权码模块
    Route::post('/AuthCode/codeInfo','Admin\AuthCodeController@codeInfo');//授权码信息
    Route::post('/AuthCode/allhmi','Admin\AuthCodeController@allhmi');//授权码信息
    Route::post('/AuthCode/bind','Admin\AuthCodeController@bind');//授权码信息
    Route::post('/AuthCode/unbind','Admin\AuthCodeController@unbind');//授权码信息

    //支付宝支付处理路由
    Route::get('alipay','Admin\AlipayController@Alipay');  // 发起支付请求
    Route::any('notify','Admin\AlipayController@AliPayNotify'); //服务器异步通知页面路径
    Route::any('return','Admin\AlipayController@AliPayReturn');  //页面跳转同步通知页面路径

    //测试模块
    Route::get('test','Admin\IndexController@test');  // 发起支付请求
});