<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Services\Admin\SupplyServicesController;

class SupplyController extends Controller
{

    protected $supplyServicesController;

    public function __construct(SupplyServicesController $supplyServicesController)
    {
        $this->supplyServicesController = $supplyServicesController;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 搜索设备
     */
    public function supplyInfo(Request $request)
    {
        $domain_id = $request->input('domain_id');
        $hmi_name = $request->input('hmi_name');
        $limit = $request->input('limit');
        $result = DB::table('ki_admin_hmi')
            ->where('domain_id', $domain_id)
            ->where('cut_off', '!=', '1')
            ->when($hmi_name, function ($query) use ($hmi_name) {
                return $query->where('hmi_name', 'like', '%' . $hmi_name . '%');
            })
            ->paginate($limit)
            ->toArray();
        return response()->json(['status' => 'S', 'code' => '200', 'message' => $result]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 禁用设备
     */
    public function forbid(Request $request)
    {
        $id=$request->input('id');
        DB::table('ki_admin_hmi')->where('id', $id)->update(['cut_off' => 2]);
        return response()->json(['status' => 'S', 'code' => '200', 'message' => '成功']);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 新增设备
     */
    public function addSupply(Request $request)
    {
        $request = $request->all();
        $request['time'] = time();
        //授权码不存在
        $bind = DB::table('ki_admin_code')->where('sncode', $request['auth_code'])->select('bind')->get()->toArray();
        if(!$bind){
            return response()->json(['status' => 'F', 'code' => '201', 'message' => '授权码不存在']);
        }
        if($bind[0]->bind!=0){
            return response()->json(['status' => 'F', 'code' => '201', 'message' => '授权码已被使用']);
        }
        $result = DB::table('ki_admin_code')->where('sncode', $request['auth_code'])->update(['bind'=>$request['sncode']]);
        $res=DB::table('ki_admin_hmi')->insertGetId($request);
        if($res&&$result){
            return response()->json(['status' => 'S', 'code' => '200', 'message' => '成功']);
        }
        return response()->json(['status' => 'S', 'code' => '501', 'message' => '服务器内部错误']);
    }
}