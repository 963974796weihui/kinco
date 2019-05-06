<?php

namespace App\Http\Services\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthCodeServicesController extends Controller
{
    public function codeInfo($limit, $sncode)
    {
        $result = DB::table('ki_admin_code')
            ->where('cut_off', 0)
            ->where('user_id', $this->id)
            ->when($sncode, function ($query) use ($sncode) {
                return $query->where('sncode', 'like', '%' . $sncode . '%');
            })
            ->paginate($limit)
            ->toArray();
        $data = $result['data'];
        foreach ($data as $key => $val) {
            if ($val->bind) {
                $hmi_name = DB::table('ki_admin_hmi')->where('sncode', $val->bind)->where('cut_off','0')->get()->toArray();
                $data[$key]->bind = $hmi_name[0]->hmi_name;
            } else {
                $data[$key]->bind = '未绑定';
            }
            if ($val->activate_time == null) {
                $data[$key]->activate_time = '未激活';
                $data[$key]->end_time='';
            } else {
                $data[$key]->activate_time = date('Y-m-d H:i:s', $val->activate_time+8*60*60);
                $data[$key]->end_time = date('Y-m-d H:i:s', Strtotime($data[$key]->activate_time) + $data[$key]->long * 24 * 60 * 60);
            }
        }
        $data['total'] = $result['total'];
        return $data;
    }
    public function allhmi($domain_id){
        $allhmi=DB::table('ki_admin_hmi')->where('auth_code','=','0')->where('domain_id',$domain_id)->where('cut_off','0')->get();
        return $allhmi;
    }
}