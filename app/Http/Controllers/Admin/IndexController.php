<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Services\Admin\IndexServicesController;
use Illuminate\Support\Facades\Mail;

class IndexController extends Controller
{
    public function index(IndexServicesController $IndexServicesController,Request $request){
        $request=$request->all();
        $result=$IndexServicesController->registerServices($request);
        if(!is_array($result)) {
            return response()->json(['status' => 'F', 'code' => '201', 'message' => $result]);
        }
        $request['time']=time();
        $request['register_confirm_code']=str_random(32);
        $res=DB::table('ki_administrtor')->insertGetId($request);
        if($res){
            return response()->json(['status' => 'S', 'code' => '200', 'message' => '注册成功']);
        }
//        $registerCode['id']=$res;
//        $registerCode['register_confirm_code']=$request['register_confirm_code'];
//        $mail=$IndexServicesController->mailSend($registerCode);//发送邮件
    }
}
