<?php

namespace App\Http\Middleware;

use Closure;
use Auth;//不使用预设的AuthController类认证，使用Laravel 的身份验证类
use URL;

/*
*   这个是验证的中间件，验证是否用户登录
*/
class LoginAuthen
{
    /*
    *   该方法是为了判断是否登录
    */
    public function handle($request, Closure $next)
    {
        //判断是否登录
        if(Auth::check() == false){
            return view('admin.login');
         }
        return $next($request);
    }
}
