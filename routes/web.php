<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test','Test\test@create');
//删除指定商机图片
Route::get('/delimg','admin\busimgController@delpic');
//编辑商机的时候添加商机图片
Route::post('/addbusimg','admin\businessController@addpic');

//权限管理 隐式路由
//Route::controller('/Test','Test\test');
Route::resource('/Test','Test\test');