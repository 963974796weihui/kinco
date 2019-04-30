<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
        Commands\Hmi_status::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
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
        // $schedule->command('inspire')
        //          ->hourly();
        //DB::table('ki_admin_hmi')->where('cert_name','client0')->update(['password'=>'5']);
        //$schedule->command('hmi_status')->everyMinute();
        $schedule->command('Auth_code')->everyMinute();
      //  ->daily()
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
