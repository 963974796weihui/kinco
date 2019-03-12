<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Services\Admin\IndexServicesController;

class IndexController extends Controller
{
    public function index(IndexServicesController $IndexServicesController,Request $request){
        $request=$request->all();
        $result=$IndexServicesController->registerServices($request);
        if(!is_array($result)) {
            return response()->json(['status' => 'F', 'code' => '201', 'message' => $result]);
        }
        $request['time']=time();
        $res=DB::table('ki_administrtor')->insert($request);
        dd($res);
    }
}
