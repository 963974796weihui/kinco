<?php

namespace App\Http\Services\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplyServicesController extends Controller
{
    public function hmi_cert(){
        $hmi_cert=DB::table('ki_user_hmi_cert')->select('hmi_cert')->where('user_cert','=','')->orderby('id','desc')->limit(1)->get()->toArray();
        if(!$hmi_cert){
            $hmi_cert='vir_1001';
            $res=DB::table('ki_user_hmi_cert')->insertGetId(['hmi_cert'=>$hmi_cert]);
            return $hmi_cert;
        }
        else{
            $hmi_cert=$hmi_cert[0]->hmi_cert;
            $code=substr($hmi_cert,4,strlen($hmi_cert)-4)+1;
            $hmi_cert='vir_'.$code;
            $res=DB::table('ki_user_hmi_cert')->insertGetId(['hmi_cert'=>$hmi_cert]);
            return $hmi_cert;
        }
    }
}