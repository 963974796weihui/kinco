<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IndexController extends Controller
{
    public function index(Request $request){
        $request=$request->input('name');
        $users = DB::table('test')->get();
        dd($users);
    }
}
