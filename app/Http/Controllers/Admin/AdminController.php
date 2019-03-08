<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index(){
        $users = DB::table('test')->get()->toArray();
        echo 1;
        dd($users);
    }
}
