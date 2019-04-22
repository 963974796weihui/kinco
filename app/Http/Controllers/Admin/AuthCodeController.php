<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Services\Admin\AuthCodeServicesController;
use Illuminate\Support\Facades\Cache;


class AuthCodeController extends Controller
{

    protected $AuthCodeServices;
    protected $id;

    public function __construct(AuthCodeServicesController $AuthCodeServicesController)
    {
        $this->AuthCodeServicesController = $AuthCodeServicesController;
        $this->id = Cache::get('loginId')[0];
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 授权码信息
     */
    public function codeInfo(Request $request)
    {
        $sncode = $request->input('sncode');
        $limit = $request->input('limit');
        $result = $this->AuthCodeServicesController->codeInfo($limit, $sncode);
        $total=$result['total'];
        unset($result['total']);
        return response()->json(['status' => 'S', 'code' => '200', 'data' => $result,'total' =>$total]);
    }
    public function allHmi(Request $request){
        $domain_id=$request->input('domain_id');
        $allhmi=$this->AuthCodeServicesController->allhmi($domain_id);//全部未绑定人机
        return response()->json(['status' => 'S', 'code' => '200', 'data' => $allhmi]);
    }
    public function bind(Request $request){
        $request = $request->all();
        $request['time'] = time();
        //授权码不存在
        if($request['auth_code']) {
            $bind = DB::table('ki_admin_code')->where('sncode', $request['auth_code'])->select('bind')->get()->toArray();
            if (!$bind) {
                return response()->json(['status' => 'F', 'code' => '201', 'message' => '授权码不存在']);
            }
            if ($bind[0]->bind != 0) {
                return response()->json(['status' => 'F', 'code' => '201', 'message' => '授权码已被使用']);
            }
            $result = DB::table('ki_admin_code')->where('sncode', $request['auth_code'])->update(['bind' => $request['sncode'], 'activate_time' => $request['time']]);
        }
        $res=DB::table('ki_admin_hmi')->where('id',$request['id'])->update(['auth_code'=>$request['auth_code']]);
        return response()->json(['status' => 'S', 'code' => '200', 'data' => '绑定成功']);
    }
}