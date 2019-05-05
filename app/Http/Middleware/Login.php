<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;

class Login
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $id=Cache::get('loginId');
       // dump($id);
        if(!isset($id)||$id=='null'){
            //return response()->json(['status' => 'F', 'code' => '201', 'message' => '请登录']);
            return redirect('http://39.104.56.173:5901/#/login');
        }
        return redirect('http://39.104.56.173:5901/#/login');
        return $next($request);
    }
}
