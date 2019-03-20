<?php

namespace App\Http\Services\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserServicesController extends Controller
{

    public function userInfo($id)
    {//$id是域名id 缓存的$this->id为超管id
        $result = DB::table('ki_admin_user')->where('domain_id', '=', $id)->select('id', 'user_name', 'remark', 'phone', 'email')->get()->toArray();
        foreach ($result as $key => $value) {//匹配设备组
            $res = DB::table('ki_admin_user_hmi_group')
                ->where('domain_id', $id)
                ->where('user_id', $value->id)
                ->where('hmi_id', '0')
                ->get()
                ->toArray();
            $result[$key]->group = $res;
        }
        foreach ($result as $key => $value) {//匹配设备
            $res = DB::table('ki_admin_user_hmi_group')
                ->where('domain_id', $id)
                ->where('user_id', $value->id)
                ->where('group_id', '0')
                ->get()
                ->toArray();
            $result[$key]->hmi = $res;
        }
        foreach ($result as $keys => $value) {//未分组人机
            $count = 0;
            $res = DB::table('ki_admin_user_hmi_group')
                ->where('domain_id', $id)
                ->where('user_id', $value->id)
                ->where('group_id', '0')
                ->get()
                ->toArray();
            if ($res) {//用户有hmi的情况下
                foreach ($res as $key => $value) {
                    $hmi[] = $value->hmi_id;
                    $empty = DB::table('ki_admin_user_hmi_group')
                        ->where('domain_id', $id)
                        ->where('user_id', '0')
                        ->where('hmi_id', $value->hmi_id)
                        ->get()
                        ->toArray();
                    if (empty($empty)) {
                        $count++;
                    }
                }
            }
            $result[$keys]->count = $count;
        }
        return $result;
    }

    public function supplyGroup($user_id, $id)
    {
        $group = array();
        $allGroup = DB::table('ki_admin_group')//$groupName域下全部的组
        ->where('domain_id', $id)
            ->where('cut_off', '0')
            ->select('id', 'group_name')
            ->get()
            ->toArray();
        $result = DB::table('ki_admin_user_hmi_group')
            ->where('domain_id', $id)
            ->where('user_id', $user_id)
            ->where('hmi_id', '0')
            ->select('group_id')
            ->get()
            ->toArray();
        foreach ($result as $key => $value) {
            $group[] = $value->group_id;
        }
        foreach ($allGroup as $key => $value) {
            if (in_array($value->id, $group)) {
                $allGroup[$key]->status = 1;//状态为1说名用户已经绑定了这个组
            } else {
                $allGroup[$key]->status = 0;
            }
        }
        return $allGroup;
    }

    public function supplyGroupBind($domain_id, $user_id, $id)
    {
        $result = DB::table('ki_admin_user_hmi_group')
            ->where('domain_id', $domain_id)
            ->where('user_id', $user_id)
            ->where('hmi_id', '0')
            ->delete();
        foreach ($id as $key => $value) {
            if ($value) {
                $condition['user_id'] = $user_id;
                $condition['domain_id'] = $domain_id;
                $condition['group_id'] = $value;
                $result = DB::table('ki_admin_user_hmi_group')->insertGetId($condition);
            }
        }
        return true;
    }

    public function hmiGroup($user_id, $id)
    {
        $allGroup = DB::table('ki_admin_hmi')//$groupName域下全部的人机
        ->where('domain_id', $id)
            ->where('cut_off', '0')
            ->select('id', 'hmi_name')
            ->get()
            ->toArray();
        $result = DB::table('ki_admin_user_hmi_group')
            ->where('domain_id', $id)
            ->where('user_id', $user_id)
            ->where('group_id', '0')
            ->select('hmi_id')
            ->get()
            ->toArray();
        foreach ($result as $key => $value) {
            $group[] = $value->hmi_id;
        }
        foreach ($allGroup as $key => $value) {
            if (in_array($value->id, $group)) {
                $allGroup[$key]->status = 1;//状态为1说名用户已经绑定了这个组
            } else {
                $allGroup[$key]->status = 0;
            }
        }
        return $allGroup;
    }

    public function hmiGroupBind($domain_id, $user_id, $id)
    {
        $result = DB::table('ki_admin_user_hmi_group')
            ->where('domain_id', $domain_id)
            ->where('user_id', $user_id)
            ->where('group_id', '0')
            ->delete();
        foreach ($id as $key => $value) {
            if ($value) {
                $condition['user_id'] = $user_id;
                $condition['domain_id'] = $domain_id;
                $condition['hmi_id'] = $value;
                $result = DB::table('ki_admin_user_hmi_group')->insertGetId($condition);
            }
        }
        return true;
    }

    public function addUser($request)
    {
        $id = DB::table('ki_admin_user')->where('user_name', '=', $request['user_name'])->get()->toArray();
        if ($id) {
            return false;
        }
        $request['password'] = '123456';
        $request['register_confirm_code'] = str_random(32);
        $request['time'] = time();
        $id = DB::table('ki_admin_user')->insertGetId($request);
        return $id;
    }
}