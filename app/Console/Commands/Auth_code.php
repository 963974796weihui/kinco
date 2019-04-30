<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;

class Auth_code extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Auth_code';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '授权码到期取消人机与授权码的绑定';

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
        $total=array();
        $result=DB::table('ki_admin_code')->select('long','activate_time','bind')->where('activate_time','!=','null')->get()->toArray();
        foreach ($result as $key=>$value){
            $total[$key]['time']=$value->long*24*60*60+$value->activate_time-time();//
            $total[$key]['bind']=$value->bind;
        }
        foreach ($total as $key=>$value){
            if($value['time']<0){//授权码已到期
                DB::table('ki_admin_hmi')->where('sncode',$value['bind'])->update(['auth_code'=>0]);
            }
        }
    }
}
