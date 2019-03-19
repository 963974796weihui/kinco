<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Services\Admin\UserServicesController;

class UserController extends Controller{

    protected $userServices;

    public function __construct(UserServicesController $userServicesController)
    {
        $this->userServices=$userServicesController;
    }

    public function userInfo($id){
        $this->userServices->userInfo($id);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 域下新增用户信息接口：
     */
    public function addUser(Request $request){
        $request=$request->all();
        $result=$this->userServices->addUser($request);
        if($result){
            return response()->json(['status' => 'S', 'code' => '200', 'message' => '用户添加成功']);
        }else{
            return response()->json(['status' => 'F', 'code' => '201', 'message' => '该用户已存在']);
        }
    }
}