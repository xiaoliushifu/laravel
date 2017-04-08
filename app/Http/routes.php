<?php
//全局路由加正则

//Route::pattern('id', '[0-9]+');
Route::get('/','TaskController@index');
Route::get('/welcome',function(){
    //Auth::loginUsingId(4);
    #return view('welcome')->with('fullname','LiuMingWei');
    return view('welcome')->withFullname('LiuMingWeiw');
}); 
// Authentication routes...

// Registration routes...
Route::get('auth/register', 'Auth\AuthController@getRegister');
Route::post('auth/register', 'Auth\AuthController@postRegister');



//Route::group(['domain'=>'{win}.laravel.com','middleware'=>['login'],'prefix'=>'','namespace'=>''],function(){
        //Route::get('/welcome', function () {
        //    return 'OK,your get domain';
       // });
        
        /* //默认首页
        Route::get('/',function(){
           return 'hello world';   
        }); */
        
    
        //参数，是按照出现顺序定义的。
        //$id,代表第一个参数,也就是{win}，$b代表第二个参数，也就是下一行的{id}
        /* Route::get('{id}', function ($win,$id) {
            return 'OK,your second: '.$id."----------First: $win";
        }); */
    
        //Route::controller('task','TaskController');
        /* Route::get('{url}', function ($id) {
            return 'OK,your get 2   '.$id;
            //给路由加正则
        })->where('url','[a-z]+'); */
//});

//匹配路由，第一个参数是http方法
Route::match(['get', 'post'], '/add', function()
{
    return 'Hello World';
});
//与match是一类的是any路由,也就是省略了第一个参数，
/*Route::any('',function(){});
 * 
 * */


Route::get('/test', function () {
    /* $b=$this->app->make('B');
     echo $b->a->title;
     $b->a->title='OK';
     $b=$this->app->make('B');
     echo $b->a->title; */
    //dd($b);
    Z::setTitle('你好222222224');
    echo Z::getTitle();
});



//路由缓存  当routes.php基本固定的时候，可以使用路由缓存机制

//Route::get('/test/test','Test@test');
//Route::resource('/home','HomeController',['only'=>['index','create']]);
 /* Route::get('/test',function (){
			$userModel = new User();
			//$userModel->userAdd();
			$userModel->userUpdate();
			return $userModel->userRead();

}); */

/* Route::any('{any}', function ($id) {
            return view('errors.503');
            //给路由加正则
        })->where('any','.+'); */


