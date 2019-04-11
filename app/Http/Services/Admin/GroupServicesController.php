<?php

namespace App\Http\Services\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GroupServicesController extends Controller
{
    public function supplyInfo($id, $limit, $group_name)
    {
        $result = DB::table('ki_admin_group')
            ->where('domain_id', '=', $id)
            ->when($group_name, function ($query) use ($group_name) {
                return $query->where('group_name', 'like', '%' . $group_name . '%');
            })
            ->select('id', 'group_name', 'domain_id')
            ->paginate($limit)
            ->toArray();
        foreach ($result['data'] as $key => $value) {//匹配设备组
            $res = DB::table('ki_admin_user_hmi_group')
                ->where('domain_id', $id)
                ->where('group_id', $value->id)
                ->where('user_id', '0')
                ->get()
                ->toArray();
            $result['data'][$key]->hmi_num = count($res);
        }
        return $result;
    }

    public function hmiInfo($domain_id, $id)
    {
        $group = array();
        $allGroup = DB::table('ki_admin_hmi')//域下的全部人机
        ->where('domain_id', $domain_id)
            ->where('cut_off', '0')
            ->select('id', 'hmi_name')
            ->get()
            ->toArray();
        $result = DB::table('ki_admin_user_hmi_group')
            ->where('domain_id', $domain_id)
            ->where('group_id', $id)
            ->where('user_id', '0')
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

    public function hmiInfoBind($domain_id, $group_id, $id)
    {
        $result = DB::table('ki_admin_user_hmi_group')
            ->where('domain_id', $domain_id)
            ->where('group_id', $group_id)
            ->where('user_id', '0')
            ->delete();
        foreach ($id as $key => $value) {
            if ($value) {
                $condition['group_id'] = $group_id;
                $condition['domain_id'] = $domain_id;
                $condition['hmi_id'] = $value;
                $result = DB::table('ki_admin_user_hmi_group')->insertGetId($condition);
            }
        }
        return true;
    }

    public function addUser($domain_id, $id)
    {
        $group = array();
        $allGroup = DB::table('ki_admin_user')//域下的全部人机
        ->where('domain_id', $domain_id)
            ->where('cut_off', '0')
            ->select('id', 'user_name')
            ->get()
            ->toArray();
        $result = DB::table('ki_admin_user_hmi_group')
            ->where('domain_id', $domain_id)
            ->where('group_id', $id)
            ->where('hmi_id', '0')
            ->select('user_id')
            ->get()
            ->toArray();
        foreach ($result as $key => $value) {
            $group[] = $value->user_id;
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

    public function addUserBind($domain_id, $group_id, $id)
    {
        $result = DB::table('ki_admin_user_hmi_group')
            ->where('domain_id', $domain_id)
            ->where('group_id', $group_id)
            ->where('hmi_id', '0')
            ->delete();
        foreach ($id as $key => $value) {
            if ($value) {
                $condition['group_id'] = $group_id;
                $condition['domain_id'] = $domain_id;
                $condition['user_id'] = $value;
                $result = DB::table('ki_admin_user_hmi_group')->insertGetId($condition);
            }
        }
        return true;
    }
}