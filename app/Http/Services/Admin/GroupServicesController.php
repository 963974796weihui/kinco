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
            ->where('cut_off','=','0')
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
    public function unhmiInfoBind($domain_id, $group_id, $id)
    {
        $result = DB::table('ki_admin_user_hmi_group')
            ->where('domain_id', $domain_id)
            ->where('group_id', $group_id)
            ->whereIn('hmi_id', $id)
            ->delete();
        return true;
    }
    public function unhmiAddShell($group_id,$id){
        $hmi_id=array();
        $hmi_cert_name=array();
        $part_cert_name=array();
        $res = DB::table('ki_admin_user_hmi_group')
            ->where('group_id', $group_id)
            ->where('user_id', '0')
            ->select('hmi_id')
            ->get();//剩余的所有的屏
        foreach ($res as $key=>$value){
            $hmi_id[]=$res[$key]->hmi_id;//组下所有屏的id
        }
        $result=DB::table('ki_admin_hmi')->whereIn('id',$hmi_id)->select('cert_name')->get();
        foreach($result as $key=>$value){
            $hmi_cert_name[]=$value->cert_name;//剩余的所有的屏证书
        }
        $result=DB::table('ki_admin_hmi')->whereIn('id',$id)->select('cert_name')->get();
        foreach($result as $key=>$value){
            $part_cert_name[]=$value->cert_name;//解绑的屏
        }
        $this->unsystemShell($hmi_cert_name,$part_cert_name);//解绑的屏和所有的屏解绑
        $this->unUsrSystemShell($group_id,$part_cert_name);//要解绑的屏群组中的所有用户进行解绑
    }
    public function unUsrSystemShell($group_id,$hmi_cert_name){
        $user_id=array();
        $user_cert_name=array();
        $result=DB::table('ki_admin_user_hmi_group')->where('group_id',$group_id)->where('hmi_id','0')->select('user_id')->get();
        foreach($result as $key=>$value){
            $user_id[]=$value->user_id;
        }
        $result=DB::table('ki_admin_user')->whereIn('id',$user_id)->select('cert_name')->get();
        foreach($result as $key=>$value){
            $user_cert_name[]=$value->cert_name;//用户证书
        }
        for($i=0;$i<count($hmi_cert_name);$i++){
            for ($j=0;$j<count($user_cert_name);$j++){
                system('/root/openvpn_docker/release_1/deploy_map_related/script_dir/pf_related/authority_alloc.sh'.' '.$hmi_cert_name[$i].' '.'-del'.' '.$user_cert_name[$j]);
            }
        }
    }
    public function unsystemShell($hmi_cert_name,$part_cert_name){
        for($i=0;$i<count($hmi_cert_name);$i++){
            for ($j=0;$j<count($part_cert_name);$j++){
                system('/root/openvpn_docker/release_1/deploy_map_related/script_dir/pf_related/authority_alloc.sh'.' '.$hmi_cert_name[$i].' '.'-del'.' '.$part_cert_name[$j]);
                system('/root/openvpn_docker/release_1/deploy_map_related/script_dir/pf_related/authority_alloc.sh'.' '.$part_cert_name[$j].' '.'-del'.' '.$hmi_cert_name[$i]);
            }
        }
    }
    public function hmiAddShell($group_id,$id){
        $allhmiId=array();//所有屏id
        $allhmi=array();//所有屏
        $hmi_cert_name=array();//所有证书
        $result=DB::table('ki_admin_hmi')->whereIn('id',$id)->select('cert_name')->get();
        foreach($result as $key=>$value){
            $hmi_cert_name[]=$value->cert_name;
        }
        $this->usrSystemShell($group_id,$hmi_cert_name);//将新增设备与群组中的所有用户进行通讯
        $res=DB::table('ki_admin_user_hmi_group')->where('group_id',$group_id)->where('user_id','0')->select('hmi_id')->get();
        foreach($res as $key=>$value){
            $allhmiId[]=$value->hmi_id;
        }
        $res=DB::table('ki_admin_hmi')->whereIn('id',$allhmiId)->select('cert_name')->get();
        foreach($res as $key=>$value){
            $allhmi[]=$value->cert_name;
        }
        $this->SystemShell($allhmi,$hmi_cert_name);//新增的屏与组下面所有的屏进行通讯
    }

    public function usrSystemShell($group_id,$hmi_cert_name){
        $user_id=array();
        $user_cert_name=array();
        $result=DB::table('ki_admin_user_hmi_group')->where('group_id',$group_id)->where('hmi_id','0')->select('user_id')->get();
        foreach($result as $key=>$value){
            $user_id[]=$value->user_id;
        }
        $result=DB::table('ki_admin_user')->whereIn('id',$user_id)->select('cert_name')->get();
        foreach($result as $key=>$value){
            $user_cert_name[]=$value->cert_name;//用户证书
        }
        for($i=0;$i<count($hmi_cert_name);$i++){
            for ($j=0;$j<count($user_cert_name);$j++){
                system('/root/openvpn_docker/release_1/deploy_map_related/script_dir/pf_related/authority_alloc.sh' . ' ' . $hmi_cert_name[$i] . ' ' . '-add' . ' ' . $user_cert_name[$j]);
            }
        }
    }
    public function systemShell($allhmi,$hmi_cert_name){
        for($i=0;$i<count($allhmi);$i++){
            for ($j=0;$j<count($hmi_cert_name);$j++){
                system('/root/openvpn_docker/release_1/deploy_map_related/script_dir/pf_related/authority_alloc.sh'.' '.$allhmi[$i].' '.'-add'.' '.$hmi_cert_name[$j]);
                system('/root/openvpn_docker/release_1/deploy_map_related/script_dir/pf_related/authority_alloc.sh'.' '.$hmi_cert_name[$j].' '.'-add'.' '.$allhmi[$i]);
            }
        }
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
    public function unaddUserBind($domain_id, $group_id, $id)
    {
        $result = DB::table('ki_admin_user_hmi_group')
            ->where('domain_id', $domain_id)
            ->where('group_id', $group_id)
            ->whereIn('user_id', $id)
            ->delete();
        return true;
    }
    public function GunhmiAddShell($group_id,$id){
        $hmi_id=array();
        $hmi_cert_name=array();
        $user_cert_name=array();
        $result=DB::table('ki_admin_user')->whereIn('id',$id)->select('cert_name')->get()->toArray();//得到解绑用户下的证书
        foreach ($result as $key=>$value){
            $user_cert_name[]=$value->cert_name;
        }
        $res = DB::table('ki_admin_user_hmi_group')//得到组下面的所有人机
            ->where('group_id', $group_id)
            ->where('user_id', '0')
            ->select('hmi_id')
            ->get();
        foreach ($res as $key=>$value){
            $hmi_id[]=$res[$key]->hmi_id;
        }
        $result=DB::table('ki_admin_hmi')->whereIn('id',$hmi_id)->select('cert_name')->get();
        foreach($result as $key=>$value){
            $hmi_cert_name[]=$value->cert_name;
        }
        $this->GunsystemShell($user_cert_name,$hmi_cert_name);//调用shell脚本
    }
    public function GunsystemShell($user_cert_name,$hmi_cert_name){
        for($i=0;$i<count($user_cert_name);$i++){
            for($j=0;$j<count($hmi_cert_name);$j++){
                //system('/root/openvpn_docker/release_1/deploy_map_related/script_dir/pf_related/authority_alloc.sh'.' '.$user_cert_name[$i].' '.'-del'.' '.$hmi_cert_name[$j]);
                system('/root/openvpn_docker/release_1/deploy_map_related/script_dir/pf_related/authority_alloc.sh'.' '.$hmi_cert_name[$j].' '.'-del'.' '.$user_cert_name[$i]);
            }
        }
    }
    public function GhmiAddShell($group_id,$id){
        $hmi_id=array();
        $hmi_cert_name=array();
        $user_cert_name=array();
        $result=DB::table('ki_admin_user')->whereIn('id',$id)->select('cert_name')->get()->toArray();//得到解绑用户下的证书
        foreach ($result as $key=>$value){
            $user_cert_name[]=$value->cert_name;
        }
        $res = DB::table('ki_admin_user_hmi_group')//得到组下面的所有人机
        ->where('group_id', $group_id)
            ->where('user_id', '0')
            ->select('hmi_id')
            ->get();
        foreach ($res as $key=>$value){
            $hmi_id[]=$res[$key]->hmi_id;
        }
        $result=DB::table('ki_admin_hmi')->whereIn('id',$hmi_id)->select('cert_name')->get();
        foreach($result as $key=>$value){
            $hmi_cert_name[]=$value->cert_name;
        }
        $this->GsystemShell($user_cert_name,$hmi_cert_name);//调用shell脚本
    }
    public function GsystemShell($user_cert_name,$hmi_cert_name){
        for($i=0;$i<count($user_cert_name);$i++){
            for($j=0;$j<count($hmi_cert_name);$j++){
                //system('/root/openvpn_docker/release_1/deploy_map_related/script_dir/pf_related/authority_alloc.sh'.' '.$user_cert_name[$i].' '.'-del'.' '.$hmi_cert_name[$j]);
                system('/root/openvpn_docker/release_1/deploy_map_related/script_dir/pf_related/authority_alloc.sh'.' '.$hmi_cert_name[$j].' '.'-add'.' '.$user_cert_name[$i]);
            }
        }
    }
}