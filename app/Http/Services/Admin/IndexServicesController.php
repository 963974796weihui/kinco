<?php

namespace App\Http\Services\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class IndexServicesController extends Controller
{
    public function registerServices($request)
    {
        $rules = [
            'user_name' => 'required|min:2|max:20',
            'first_name' => 'required',
            'email' => 'required|email',
            'address' => 'required',
            'company_name' => 'required',
            'province' => 'required',
            'city' => 'required',
            'area' => 'required',
            'phone' => 'nullable',
        ];
        $messages = [
            'required' => ':attribute 为必填项',
            'min' => ':attribute 长度不符合要求',
            'max' => ':attribute 长度不符合要求',
            'email' => ':attribute 格式错误'
        ];

        $validator = \Validator::make($request, $rules, $messages, [
            'user_name' => '用户名称',
            'email' => 'Email',
            'password' => '密码',
            'first_name' => '姓名',
            'address' => '地址',
            'company_name' => '公司名称',
            'province' => '省份',
            'city' => '城市',
            'area' => '地区',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->getMessages();
            foreach ($errors as $key => $value) {
                return $value[0];
            }
        }
        return $request;
    }

    public function mailSend($registerCode)
    {
        $to =$registerCode['email'];//接收人
        $subject = 'EdgeAccess Domain 用户注册确认信';//主题
        Mail::send(
            'emails.test',//邮件发送的模板文件
            ['content' => $registerCode],//生成的模板文件变量 数组形式
            function ($message) use ($to, $subject) {
                $res=$message->to($to)->subject($subject);
            }
        );
    }

    public function registerCode()
    {
        $string = str_random(32);
        return $string;
    }

    public function login($condition)
    {
        $result = DB::table('ki_admin_administrtor')->where($condition)->pluck('id')->toArray();
        $status = DB::table('ki_admin_administrtor')->where($condition)->pluck('status')->toArray();
        if ($result) {
            if($status[0]==0){//未通过邮箱确认
                return $status[0];
            }
            DB::table('ki_admin_administrtor')->where($condition)->update(['last_time' => time()]);//更新最后一次登录时间
            Cache::put('loginId', $result, 3600);//缓存登录ip
            $domain_name = DB::table('ki_admin_administrtor')
                ->leftjoin('ki_admin_domain', 'ki_admin_administrtor.id', '=', 'ki_admin_domain.uid')
                ->where('ki_admin_administrtor.id', '=', $result[0])
                ->select('ki_admin_domain.id as id', 'domain_name')
                ->get()
                ->toArray();
            return $domain_name;
        } else {
            return false;
        }
    }

    public function registerDomain($condition)
    {
        $result=DB::table('ki_admin_domain')
            ->where('domain_name','=',$condition['domain_name'])
            ->where('uid','=',$this->id)
            ->get()
            ->toArray();
        if($result){
            return false;
        }
        $condition['time']=time();
        $condition['uid']=$this->id;//缓存登录ip
        DB::table('ki_admin_domain')->insert($condition);
        return true;
    }
}
