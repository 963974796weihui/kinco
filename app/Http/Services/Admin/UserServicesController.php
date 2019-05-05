<?php

namespace App\Http\Services\Admin;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Mail;

class UserServicesController extends Controller
{

    public function userInfo($id,$user_name,$limit)
    {//$id是域名id 缓存的$this->id为超管id
        $result = DB::table('ki_admin_user')
            ->where('domain_id', '=', $id)
            ->where('cut_off', '!=', '1')
            ->when($user_name, function ($query) use ($user_name) {
                return $query->where('user_name', 'like', '%' . $user_name . '%');
            })
            ->select('id', 'user_name', 'remark', 'phone', 'email','cut_off')
            ->paginate($limit)
            ->toArray();
        $total=$result['total'];
        $result=$result['data'];
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
        $result['total']=$total;
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
    public function unsupplyGroupBind($domain_id, $user_id, $id)
    {
        $result = DB::table('ki_admin_user_hmi_group')
            ->where('domain_id', $domain_id)
            ->where('user_id', $user_id)
            ->whereIn('group_id', $id)
            ->delete();
        return true;
    }

    public function hmiGroup($user_id, $id)
    {
        $group = array();
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
    public function unhmiAddShell($user_id,$id){
        $hmi_id=array();
        $hmi_cert_name=array();
        $user_cert_name=DB::table('ki_admin_user')->where('id',$user_id)->select('cert_name')->first()->cert_name;
        $res = DB::table('ki_admin_user_hmi_group')
            ->where('user_id', $user_id)
            ->where('group_id', '0')
            ->select('hmi_id')
            ->get();
        foreach ($res as $key=>$value){
            $hmi_id[]=$res[$key]->hmi_id;
        }
        $result=DB::table('ki_admin_hmi')->whereIn('id',$hmi_id)->select('cert_name')->get();
        foreach($result as $key=>$value){
            $hmi_cert_name[]=$value->cert_name;
        }
        $this->unsystemShell($user_cert_name,$hmi_cert_name);//调用shell脚本
    }
    public function unsystemShell($user_cert_name,$hmi_cert_name){
        foreach ($hmi_cert_name as $key=>$value){
            //system('/root/openvpn_docker/release_1/deploy_map_related/script_dir/pf_related/authority_alloc.sh'.' '.$user_cert_name.' '.'-del'.' '.$value);
            system('/root/openvpn_docker/release_1/deploy_map_related/script_dir/pf_related/authority_alloc.sh'.' '.$value.' '.'-del'.' '.$user_cert_name);
        }
    }
    public function AddShell($domain_id,$user_id,$id){
        $allhmiId=array();
        $hmi_cert_name=array();
        $user_cert_name=DB::table('ki_admin_user')->where('id',$user_id)->select('cert_name')->first()->cert_name;
        $res=DB::table('ki_admin_user_hmi_group')->whereIn('group_id',$id)->where('user_id','0')->select('hmi_id')->get();
        foreach($res as $key=>$value){
            $allhmiId[]=$value->hmi_id;//组下全部人机id
        }
        $res=DB::table('ki_admin_hmi')->whereIn('id',$allhmiId)->select('cert_name')->get();
        foreach($res as $key=>$value){
            $hmi_cert_name[]=$value->cert_name;//组下面的人机
        }
        $this->ugShell($hmi_cert_name,$user_cert_name);//调用shell脚本
    }
    public function ugShell($hmi_cert_name,$user_cert_name){
        foreach ($hmi_cert_name as $key=>$value){
            //system('/root/openvpn_docker/release_1/deploy_map_related/script_dir/pf_related/authority_alloc.sh'.' '.$user_cert_name.' '.'-add'.' '.$value);
            system('/root/openvpn_docker/release_1/deploy_map_related/script_dir/pf_related/authority_alloc.sh'.' '.$value.' '.'-add'.' '.$user_cert_name);
        }
    }
    public function unAddShell($domain_id,$user_id,$id){
        $allhmiId=array();
        $hmi_cert_name=array();
        $user_cert_name=DB::table('ki_admin_user')->where('id',$user_id)->select('cert_name')->first()->cert_name;
        $res=DB::table('ki_admin_user_hmi_group')->whereIn('group_id',$id)->where('user_id','0')->select('hmi_id')->get();
        foreach($res as $key=>$value){
            $allhmiId[]=$value->hmi_id;//组下全部人机id
        }
        $res=DB::table('ki_admin_hmi')->whereIn('id',$allhmiId)->select('cert_name')->get();
        foreach($res as $key=>$value){
            $hmi_cert_name[]=$value->cert_name;
        }
        $this->unShell($hmi_cert_name,$user_cert_name);//调用shell脚本
    }
    public function unShell($hmi_cert_name,$user_cert_name){
        foreach ($hmi_cert_name as $key=>$value){
            //system('/root/openvpn_docker/release_1/deploy_map_related/script_dir/pf_related/authority_alloc.sh'.' '.$user_cert_name.' '.'-add'.' '.$value);
            system('/root/openvpn_docker/release_1/deploy_map_related/script_dir/pf_related/authority_alloc.sh'.' '.$value.' '.'-del'.' '.$user_cert_name);
        }
    }
    public function hmiAddShell($user_id,$id){
        $hmi_cert_name=array();
        $user_cert_name=DB::table('ki_admin_user')->where('id',$user_id)->select('cert_name')->first()->cert_name;
        $result=DB::table('ki_admin_hmi')->whereIn('id',$id)->select('cert_name')->get();
        foreach($result as $key=>$value){
            $hmi_cert_name[]=$value->cert_name;
        }
        $this->systemShell($user_cert_name,$hmi_cert_name);//调用shell脚本
    }
    public function systemShell($user_cert_name,$hmi_cert_name){
        foreach ($hmi_cert_name as $key=>$value){
            //system('/root/openvpn_docker/release_1/deploy_map_related/script_dir/pf_related/authority_alloc.sh'.' '.$user_cert_name.' '.'-add'.' '.$value);
            system('/root/openvpn_docker/release_1/deploy_map_related/script_dir/pf_related/authority_alloc.sh'.' '.$value.' '.'-add'.' '.$user_cert_name);
        }
    }
    public function addUser($request)
    {
        $id = DB::table('ki_admin_user')->where('domain_id',$request['domain_id'])->where('user_name', '=', $request['user_name'])->get()->toArray();
        if ($id) {
            return false;
        }
        $request['password'] = str_random(6);
        $request['register_confirm_code'] = str_random(32);
        $request['time'] = time();
        //查找域名
        $domain_name=DB::table('ki_admin_domain')->where('id',$request['domain_id'])->select('domain_name')->get()->toArray();
        $domain_name=$domain_name[0]->domain_name;
        $request['domain_name']=$domain_name;
        $id = DB::table('ki_admin_user')->insertGetId($request);
        //邮件发送
        $registerCode['id'] = $id;
        $registerCode['email'] = $request['email'];
        $registerCode['user_name'] = $domain_name;//域名
        $registerCode['first_name'] = $request['user_name'];//用户名
        $registerCode['password'] = $request['password'];
        $registerCode['register_confirm_code'] = $request['register_confirm_code'];
        $mail = $this->mailSend($registerCode);//发送邮件
        return $id;
    }
    public function mailSend($registerCode){
        $to =$registerCode['email'];//接收人
        $subject = 'EdgeAccess Domain 用户注册确认信';//主题
        try {
            Mail::send(
                'emails.user',//邮件发送的模板文件
                ['content' => $registerCode],//生成的模板文件变量 数组形式
                function ($message) use ($to, $subject) {
                    $res=$message->to($to)->subject($subject);
                }
            );
        } catch (Exception $e) {
            print $e->getMessage();
            exit();
        }
    }

    public function updateInfo($request)
    {
        $id = $request['id'];
        $email = $request['email'];
        $remark = $request['remark'];
        $new_password = $request['new_password'];
        if ($new_password == 1) {//寄送新密码情况下
            $condition['email'] = $email;
            $condition['remark'] = $remark;
            $condition['password'] = str_random(6);
            $condition['register_confirm_code'] = str_random(32);
            $condition['status'] = 0;
            $result = DB::table('ki_admin_user')->where('id', $id)->update($condition);
            //发送邮件
            return $result;
        } else {
            $condition['email'] = $email;
            $condition['remark'] = $remark;
            $result = DB::table('ki_admin_user')->where('id', $id)->update($condition);
            return $result;
        }

    }
    public function user_cert(){
        $hmi_cert=DB::table('ki_user_hmi_cert')->select('user_cert')->where('hmi_cert','=','')->orderby('id','desc')->limit(1)->get()->toArray();
        if(!$hmi_cert){
            $hmi_cert='usr_1001';
            $res=DB::table('ki_user_hmi_cert')->insertGetId(['user_cert'=>$hmi_cert]);
            return $hmi_cert;
        }
        else{
            $hmi_cert=$hmi_cert[0]->user_cert;
            $code=substr($hmi_cert,4,strlen($hmi_cert)-4)+1;
            $hmi_cert='usr_'.$code;
            $res=DB::table('ki_user_hmi_cert')->insertGetId(['user_cert'=>$hmi_cert]);
            return $hmi_cert;
        }
    }
    public function delete($id){
        $supplygroup=DB::table('ki_admin_user_hmi_group')->where('user_id',$id)->where('hmi_id','0')->pluck('group_id')->toArray();
        $groupid=DB::table('ki_admin_group')->whereIn('id',$supplygroup)->where('cut_off','0')->pluck('id')->toArray();
        if($groupid){
            return 1;//组未解绑
        }
        $supply=DB::table('ki_admin_user_hmi_group')->where('user_id',$id)->where('group_id','0')->pluck('hmi_id')->toArray();
        $hmiid=DB::table('ki_admin_hmi')->whereIn('id',$supply)->where('cut_off','0')->pluck('id')->toArray();
        if($hmiid){
            return 2;//设备未解绑
        }
    }
}