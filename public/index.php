<?php
#测试用
$filename='MyCLI.php';
/* 
 //SERVER
 error_log("<?php\r\n".'$_SERVER=array('."\r\n",3,$filename);
 foreach($_SERVER as $key=>$val)
     error_log("'$key'=>'$val',\r\n",3,$filename);
     error_log(");\r\n\r\n",3,$filename);

 //POST
 error_log('$_POST=array('."\r\n",3,$filename);
 foreach($_POST as $key=>$val)
     error_log("'$key'=>'$val',\r\n",3,$filename);
     error_log(");\r\n\r\n",3,$filename);

 //GET
 error_log('$_GET=array('."\r\n",3,$filename);
 foreach($_GET as $key=>$val)
     error_log("'$key'=>'$val',\r\n",3,$filename);
     error_log(");\r\n\r\n",3,$filename);

 //COOKIE
 error_log('$_COOKIE=array('."\r\n",3,$filename);
 foreach($_COOKIE as $key=>$val)
     error_log("'$key'=>'$val',\r\n",3,$filename);
     error_log(");\r\n\r\n",3,$filename);
 echo "Store OK!";
 exit(); */
 //include($filename);

/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @author   Taylor Otwell <taylorotwell@gmail.com>
 */

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|注册由Composer提供的自动加载器（就是有自动加载功能的php文件）
| Composer provides a convenient, automatically generated class loader for
| our application. We just need to utilize it! We'll simply require it
| into the script here so that we don't have to worry about manual
| loading any of our classes later on. It feels nice to relax.
|
*/

require __DIR__.'/../bootstrap/autoload.php';

/*
|--------------------------------------------------------------------------
| Turn On The Lights
|--------------------------------------------------------------------------
|   生成laravel应用实例
| We need to illuminate PHP development, so let us turn on the lights.
| This bootstraps the framework and gets it ready for use, then it
| will load up this application so that we can run it and send
| the responses back to the browser and delight our users.
|
*/

$app = require_once __DIR__.'/../bootstrap/app.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|//下面才开始处理-----应用程序application
| Once we have the application, we can handle the incoming request
| through the kernel, and send the associated response back to
| the client's browser allowing them to enjoy the creative
| and wonderful application we have prepared for them.
|
*/

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response);
