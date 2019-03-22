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
    public function search(Request $request)
    {
        $hmi_name = $request->input('hmi_name');
        $domain_id = $request->input('domain_id');
        $result = DB::table('ki_admin_hmi')->where('hmi_name', 'like', '%' . $hmi_name . '%')->where('cut_off', '!=', '1')->where('domain_id', $domain_id)->get()->toArray();
        return response()->json(['status' => 'S', 'code' => '200', 'message' => $result]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 搜索设备
     */
    public function supplyInfo(Request $request)
    {
        $domain_id = $request->input('domain_id');
        $result = DB::table('ki_admin_hmi')->where('domain_id', $domain_id)->where('cut_off', '!=', '1')->get()->toArray();
        return response()->json(['status' => 'S', 'code' => '200', 'message' => $result]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 禁用设备
     */
    public function forbid($id)
    {
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
        $result = DB::table('ki_admin_hmi')->where('sncode', $request['sncode'])->get()->toArray();
        if ($result) {
            return response()->json(['status' => 'F', 'code' => '201', 'message' => '设备已存在']);
        }
        DB::table('ki_admin_hmi')->insertGetId($request);
        return response()->json(['status' => 'S', 'code' => '200', 'message' => '成功']);
    }
}