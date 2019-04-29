<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Services\Admin\GroupServicesController;

class GroupController extends Controller
{

    protected $userServices;

    public function __construct(GroupServicesController $GroupServicesController)
    {
        $this->groupServices = $GroupServicesController;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 添加设备
     */
    public function addGroup(Request $request)
    {
        $request = $request->all();
        $request['time'] = time();
        $result = DB::table('ki_admin_group')->where('group_name', $request['group_name'])->where('domain_id',$request['domain_id'])->where('cut_off','0')->get()->toArray();
        if ($result) {
            return response()->json(['status' => 'F', 'code' => '201', 'message' => '该组已存在']);
        }
        DB::table('ki_admin_group')->insertGetId($request);
        return response()->json(['status' => 'S', 'code' => '200', 'message' => '成功']);
    }

    /**
     * @param Request $request
     * 域下设备群组接口
     */
    public function supplyInfo(Request $request)
    {
        $id = $request->input('id');
        $limit = $request->input('limit');
        $group_name = $request->input('group_name');
        $result = $this->groupServices->supplyInfo($id, $limit, $group_name);//获得组内的人机
        return response()->json(['status' => 'S', 'code' => '200', 'message' => $result['data'],'total'=>$result['total']]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 更改组名
     */
    public function updateGroup(Request $request)
    {
        $id = $request->input('id');
        $group_name = $request->input('group_name');
        DB::table('ki_admin_group')->where('id', $id)->update(['group_name' => $group_name]);
        return response()->json(['status' => 'S', 'code' => '200', 'message' => '成功']);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 删除组
     */
    public function deleteGroup(Request $request)
    {
        $id = $request->input('id');
        DB::table('ki_admin_group')->whereIn('id', $id)->update(['cut_off' => 1]);
        return response()->json(['status' => 'S', 'code' => '200', 'message' => '成功']);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *管理组成员
     */
    public function hmiInfo(Request $request)
    {
        $domain_id = $request->input('domain_id');
        $id = $request->input('id');
        $result = $this->groupServices->hmiInfo($domain_id, $id);
        return response()->json(['status' => 'S', 'code' => '200', 'message' => $result]);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 绑定用户
     */
    public function addUser(Request $request)
    {
        $domain_id = $request->input('domain_id');
        $id = $request->input('id');
        $result = $this->groupServices->addUser($domain_id, $id);
        return response()->json(['status' => 'S', 'code' => '200', 'message' => $result]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 绑定用户确认
     */
    public function addUserBind(Request $request)
    {
        $id = $request->input('id');//用户 id
        $group_id = $request->input('group_id');//组id
        $domain_id = $request->input('domain_id');//域id
        $result = $this->groupServices->addUserBind($domain_id, $group_id, $id);
        $this->groupServices->GhmiAddShell($group_id,$id);//在添加绑定
        return response()->json(['status' => 'S', 'code' => '200', 'message' => '组绑定用户成功']);
    }
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 解绑用户确认
     */
    public function unaddUserBind(Request $request)
    {
        $id = $request->input('id');//用户 id
        $group_id = $request->input('group_id');//组id
        $domain_id = $request->input('domain_id');//域id
        $this->groupServices->GunhmiAddShell($group_id,$id);//先解绑
        $result = $this->groupServices->unaddUserBind($domain_id, $group_id, $id);
        return response()->json(['status' => 'S', 'code' => '200', 'message' => '解绑成功']);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 管理组成员确认
     */
    public function hmiInfoBind(Request $request)
    {
        $id = $request->input('id');//hmi id
        $group_id = $request->input('group_id');//组id
        $domain_id = $request->input('domain_id');//域id
        $this->groupServices->hmiAddShell($group_id,$id);//在添加绑定
        $result = $this->groupServices->hmiInfoBind($domain_id, $group_id, $id);
        return response()->json(['status' => 'S', 'code' => '200', 'message' => '组绑定屏成功']);
    }
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 管理组成员解绑
     */
    public function unhmiInfoBind(Request $request)
    {
        $id = $request->input('id');//hmi id
        $group_id = $request->input('group_id');//组id
        $domain_id = $request->input('domain_id');//域id
        //$this->groupServices->unhmiAddShell($group_id,$id);//先解绑
        $result = $this->groupServices->unhmiInfoBind($domain_id, $group_id, $id);
        $this->groupServices->unhmiAddShell($group_id,$id);//在解除绑定
        return response()->json(['status' => 'S', 'code' => '200', 'message' => '组解绑屏成功']);
    }

    /**
     * @param Request $request
     * 获得组下的人机名
     */
    public function hmiDetail(Request $request){
        $id=$request->input('id');
        $hmi_name = DB::table('ki_admin_hmi')
            ->leftjoin('ki_admin_user_hmi_group', 'ki_admin_hmi.id', '=', 'ki_admin_user_hmi_group.hmi_id')
            ->where('group_id', $id)
            ->where('user_id', '0')
            ->where('cut_off','1')
            ->get()
            ->toArray();
        return response()->json(['status' => 'S', 'code' => '200', 'message' => $hmi_name]);
    }
}