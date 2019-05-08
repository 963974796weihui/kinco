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

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 查找全部未绑定人机
     */
    public function allHmi(Request $request){
        $domain_id=$request->input('domain_id');
        $allhmi=$this->AuthCodeServicesController->allhmi($domain_id);//全部未绑定人机
        return response()->json(['status' => 'S', 'code' => '200', 'data' => $allhmi]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 绑定人机操作
     */
    public function bind(Request $request){
        $request = $request->all();
        $request['time'] = time();
        //授权码不存在
        if(!$request['sncode']){
            return response()->json(['status' => 'F', 'code' => '204', 'data' => '请选择要绑定的设备']);
        }
        if($request['auth_code']) {
            $bind = DB::table('ki_admin_code')->where('sncode', $request['auth_code'])->select('bind','activate_time','long')->get()->toArray();
            if (!$bind) {
                return response()->json(['status' => 'F', 'code' => '201', 'message' => '授权码不存在']);
            }
            if (strlen($bind[0]->bind)>1) {
                return response()->json(['status' => 'F', 'code' => '202', 'message' => '授权码已被使用']);
            }
            $time=$bind[0]->activate_time+$bind[0]->long*60*60*24-time();
            if($time<0 && $bind[0]->activate_time){
                return response()->json(['status' => 'F', 'code' => '203', 'message' => '授权码已过期 请重新购买']);
            }
            $activate_time = DB::table('ki_admin_code')->where('sncode', $request['auth_code'])->select('activate_time')->get()->toArray();
            if($activate_time[0]->activate_time){
                $result = DB::table('ki_admin_code')->where('sncode', $request['auth_code'])->update(['bind' => $request['sncode']]);
            }else {
                $result = DB::table('ki_admin_code')->where('sncode', $request['auth_code'])->update(['bind' => $request['sncode'], 'activate_time' => $request['time']]);
            }
        }
        $res=DB::table('ki_admin_hmi')->where('id',$request['id'])->update(['auth_code'=>$request['auth_code']]);
        return response()->json(['status' => 'S', 'code' => '200', 'data' => '绑定成功']);
    }

    /**
     * @param Request $request
     * 解绑
     */
    public function unbind(Request $request){
        $id=$request->input('id');
        $codeid=DB::table('ki_admin_code')
            ->leftJoin('ki_admin_hmi', 'ki_admin_code.sncode', '=', 'ki_admin_hmi.auth_code')
            ->where('ki_admin_code.id',$id)
            ->select('ki_admin_hmi.id')
            ->first();
        try{
            DB::table('ki_admin_hmi')->where('id',$codeid->id)->update(['auth_code'=>'0']);
            DB::table('ki_admin_code')->where('id',$id)->update(['bind'=>0]);
        }catch (\Exception $e){
            $e->getMessage();
        }
        return response()->json(['status' => 'S', 'code' => '200', 'data' => '解绑成功']);
    }
}