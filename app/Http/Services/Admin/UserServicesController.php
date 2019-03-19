<?php

namespace App\Http\Services\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserServicesController extends Controller{

    public function userInfo($id){
     //   DB::table('')
        dd($id);
    }
    public function addUser($request){
        $id=DB::table('ki_admin_user')->where('user_name','=',$request['user_name'])->get()->toArray();
        if($id){
            return false;
        }
        $request['password']='123456';
        $request['register_confirm_code']=str_random(32);
        $request['time']=time();
        $id=DB::table('ki_admin_user')->insertGetId($request);
        return $id;
    }
}