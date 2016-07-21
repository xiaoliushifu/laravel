<?php
namespace App\Http\Middleware;
use Closure;

class testMiddleware {

    //中间件
    public function handle($request, Closure $next)
    {
        echo "经过了中间件".__FUNCTION__."<BR />";
        /* if ($request->input('age') < 200)
        {
            return redirect('welcome');
        } */
        
        //一般当通过中间件的时候，请求继续向下执行，一般就到控制器里了
        return $next($request);
    }
}