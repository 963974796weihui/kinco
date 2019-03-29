<?php
use Illuminate\Support\Facades\DB;
$result=DB::table('ki_admin_region')->get()->toArray();
var_dump($result);
