<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Services\Admin\UserServicesController;

class UserController extends Controller
{

    protected $userServices;

    public function __construct(UserServicesController $userServicesController)
    {
        $this->userServices = $userServicesController;
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * 查看域下用户信息
     */
    public function userInfo(Request $request)
    {
        $domain_id=$request->input('domain_id');
        $user_name = $request->input('user_name');
        $limit = $request->input('limit');
        $result = $this->userServices->userInfo($domain_id,$user_name,$limit);//获得匹配组，人机，未分组人机
        $total=$result['total'];
        unset($result['total']);
        foreach ($result as $key => $value) {
            $result[$key]->group = count($value->group);
            $result[$key]->hmi = count($value->hmi);
        }
        return response()->json(['status' => 'S', 'code' => '200', 'message' => $result,'total'=>$total]);
    }

    /**
     * @param Request $request
     * 获得设备组接口
     */
    public function supplyGroup(Request $request)
    {
        $id = $request->input('id');
        $user_id = $request->input('user_id');
        $result = $this->userServices->supplyGroup($user_id, $id);
        return response()->json(['status' => 'S', 'code' => '200', 'message' => $result]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 域下设备组确认（用户绑定组）
     */
    public function supplyGroupBind(Request $request)
    {
        $id = $request->input('id');
        $user_id = $request->input('user_id');
        $domain_id = $request->input('domain_id');
        $this->userServices->supplyGroupBind($domain_id, $user_id, $id);
        return response()->json(['status' => 'S', 'code' => '200', 'message' => '用户绑定组成功']);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 获得设备接口
     */
    public function hmiGroup(Request $request)
    {
        $id = $request->input('id');
        $user_id = $request->input('user_id');
        $result = $this->userServices->hmiGroup($user_id, $id);
        return response()->json(['status' => 'S', 'code' => '200', 'message' => $result]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 域下设备绑定
     */
    public function hmiGroupBind(Request $request)
    {
        $id = $request->input('id');
        $user_id = $request->input('user_id');
        $domain_id = $request->input('domain_id');
        $this->userServices->hmiGroupBind($domain_id, $user_id, $id);
        $this->userServices->unhmiAddShell($user_id,$id);//先解绑
        $this->userServices->hmiAddShell($user_id,$id);//在添加绑定
        return response()->json(['status' => 'S', 'code' => '200', 'message' => '用户绑定成功']);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 域下新增用户信息接口：
     */
    public function addUser(Request $request)
    {
        $request = $request->all();
        $user_cert=$this->userServices->user_cert();
        $request['cert_name']=$user_cert;
        $result = $this->userServices->addUser($request);
        if ($result) {
            return response()->json(['status' => 'S', 'code' => '200', 'message' => '用户添加成功']);
        } else {
            return response()->json(['status' => 'F', 'code' => '201', 'message' => '该用户已存在']);
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * 域下用户编辑接口
     */
    public function info($id)
    {
        $result = DB::table('ki_admin_user')->where('id', $id)->where('cut_off','!=','0')->get()->toArray();
        return response()->json(['status' => 'S', 'code' => '200', 'message' => $result]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 修改用户信息
     */
    public function updateInfo(Request $request)
    {
        $request = $request->all();
        $result = $this->userServices->updateInfo($request);
        return response()->json(['status' => 'S', 'code' => '200', 'message' => '修改成功']);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * 删除用户
     */
    public function delete(Request $request)
    {
        $id=$request->input('user_id');
        DB::table('ki_admin_user')->whereIn('id', $id)->update(['cut_off' => 1]);
        return response()->json(['status' => 'S', 'code' => '200', 'message' => '成功']);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * 禁用用户
     */
    public function forbid(Request $request)
    {
        $id=$request->input('id');
        DB::table('ki_admin_user')->where('id', $id)->update(['cut_off' => 2]);
        return response()->json(['status' => 'S', 'code' => '200', 'message' => '成功']);
    }
    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * 解禁用户
     */
    public function unforbid(Request $request)
    {
        $id=$request->input('id');
        DB::table('ki_admin_user')->where('id', $id)->update(['cut_off' => 0]);
        return response()->json(['status' => 'S', 'code' => '200', 'message' => '成功']);
    }
}