<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class Hmi_status extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hmi_status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '人机在线状态写入数据库';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $online_id=array();
        DB::table('ki_admin_hmi')->where('cert_name','client0')->update(['password'=>'1']);
        $handle = file(public_path().'/a.log');
        DB::table('ki_admin_hmi')->where('cert_name','client0')->update(['password'=>'2']);
        $start_line=array_search("ROUTING TABLE\r\n",$handle)+2;//开始行数
        $end_line=array_search("GLOBAL STATS\r\n",$handle);//结束行数
        for($i=$start_line;$i<$end_line;$i++){
            $array=explode(',',$handle[$i]);
            $condition['virtual_address']=$array[0];
            $condition['real_address']=$array[2];
            $condition['hmi_status']=1;
            DB::table('ki_admin_hmi')->where('cert_name','client0')->update(['password'=>'3']);
            DB::table('ki_admin_hmi')->where('cert_name',$array[1])->update($condition);//更新在线人机状态
            $id=DB::table('ki_admin_hmi')->where('cert_name',$array[1])->select('id')->get()->toArray();//查找未在线人机
            DB::table('ki_admin_hmi')->where('cert_name','client0')->update(['password'=>'4']);
            if($id){
                $online_id[]=$id[0]->id;
            }
        }
        DB::table('ki_admin_hmi')->where('cert_name','client0')->update(['password'=>'5']);
        DB::table('ki_admin_hmi')->whereNotIn('id',$online_id)->update(['hmi_status'=>0]);//更新未在线人机状态为0
        DB::table('ki_admin_hmi')->where('cert_name','client0')->update(['password'=>'6']);
    }
}
