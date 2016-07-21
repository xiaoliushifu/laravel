<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

//该类扩展了Illuminate\Founddation\Http\Kernel
class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *laravel中所有中间件都在app/Http/Kernel中注册
     * @var array
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \App\Http\Middleware\EncryptCookies::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
		//该中间件提供csrf的认证，就是csrf——token的认证
        \App\Http\Middleware\VerifyCsrfToken::class,
    ];

    /**
     * The application's route middleware.
     *在这里注册路由中间件，然后在路由routes.php里就可以使用了
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'test' =>\App\Http\Middleware\testMiddleware::class,
    ];
}
