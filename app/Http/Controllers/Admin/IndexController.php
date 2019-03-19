<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Services\Admin\IndexServicesController;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;

/**
 * Class IndexController
 * @package App\Http\Controllers\Admin
 * 超级管理员登录注册模块
 * time:2019/3/18
 */
class IndexController extends Controller
{
    protected $IndexServicesController;

    /**
     * IndexController constructor.
     * @param IndexServicesController $IndexServicesController
     * 构造方法
     */
    public function __construct(IndexServicesController $IndexServicesController)
    {
        $this->IndexServicesController = $IndexServicesController;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 超级管理员注册
     */
    public function register(Request $request)
    {
        $request = $request->all();
        $result = $this->IndexServicesController->registerServices($request);
        if (!is_array($result)) {
            return response()->json(['status' => 'F', 'code' => '201', 'message' => $result]);
        }
        $request['time'] = time();
        $request['register_confirm_code'] = str_random(32);
        $res = DB::table('ki_admin_administrtor')->insertGetId($request);
        if ($res) {
            return response()->json(['status' => 'S', 'code' => '200', 'message' => '注册成功']);
        }
//        $registerCode['id']=$res;
//        $registerCode['register_confirm_code']=$request['register_confirm_code'];
//        $mail=$IndexServicesController->mailSend($registerCode);//发送邮件
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 管理员登录
     */
    public function login(Request $request)
    {
        $request = $request->all();
        $result = $this->IndexServicesController->login($request);
        if ($result) {
            return response()->json(['status' => 'S', 'code' => '200', 'message' => $result]);
        } elseif($result==0) {
            return response()->json(['status' => 'F', 'code' => '202', 'message' => '请前往邮箱进行确认']);
        }else{
            return response()->json(['status' => 'F', 'code' => '201', 'message' => '账号密码不正确']);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 新建域
     */
    public function registerDomain(Request $request){
        $request=$request->all();
        $result=$this->IndexServicesController->registerDomain($request);
        if($result){
            return response()->json(['status' => 'S', 'code' => '200', 'message' => '成功']);
        }
        return response()->json(['status' => 'F', 'code' => '201', 'message' => '该域已存在']);
    }
}
