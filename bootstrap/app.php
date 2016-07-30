<?php

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new Laravel application instance
| which serves as the "glue" for all the components of Laravel, and is
| the IoC container for the system binding all of the various parts.
|
*/
/**
 * 这一步做了非常多的事情,概要一下。
 * 1实例化$app对象
 * 2RoutingServiceProviders尚没有细看。
 * 2为$app对象的属性数组alias添加了几个键值对，以类名全称为键（带命名空间），别名为值。例如 
 * 'Illuminate\Foundation\Application'=>'app'
 * 3为$app对象执行三个register开头的方法，给其属性数组instances填入了很多东西，一些路径啊，和一些服务Providers.
 * 也遇到了几个问题，不太明白，需要问龙哥
 * @var unknown
 */
$app = new Illuminate\Foundation\Application(
    realpath(__DIR__.'/../')
);

/*
|--------------------------------------------------------------------------
| Bind Important Interfaces
|--------------------------------------------------------------------------
|
| Next, we need to bind some important interfaces into the container so
| we will be able to resolve them when needed. The kernels serve the
| incoming requests to this application from both the web and CLI.
|
*/
//singleton方法，是Application对象继承Container类而来的。都放到了bindings数组里
$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,    //接口名
    App\Http\Kernel::class                      //实现上述接口的类
);
//这里bind方法里，都是把接口名作为数组下标进行绑定的，且把类包裹成一个回调。问题1：为什么包裹成回调？
$app->singleton(
    Illuminate\Contracts\Console\Kernel::class, //接口名
    App\Console\Kernel::class                   //实现上述接口的类
);
//绑定接口到具体实现的类，这样如果有新的类A实现了相同的接口ExceptionHandler，则只需绑定新类A即可。非常方便
$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class, //接口名
    App\Exceptions\Handler::class       //实现上述接口的类
);
/*
|--------------------------------------------------------------------------
| Return The Application
|--------------------------------------------------------------------------
|
| This script returns the application instance. The instance is given to
| the calling script so we can separate the building of the instances
| from the actual running of the application and sending responses.
|
*/

return $app;
