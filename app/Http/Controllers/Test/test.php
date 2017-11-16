<?php

namespace App\Http\Controllers\Test;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Jobs\SendReminderEmail;

class Test extends Controller
{
	public function index(Request $request,$id=null)
	{
		//这个任务类，直接调用其dispatch方法
		//就可以入队列了，至于队列的驱动是啥？那得看配置了，不用考虑。
		//我只知道这里创建了个SendReminderEmail任务，它已经进入到队列了。
		//后续队列何时执行？？php artisan queue:work就行了。
		//所以理解队列，就两步关键：
		//1 建立任务，任务入队列
		//2  执行队列里的任务。完事！
		SendReminderEmail::dispatch();
		return 'hello2';
	}

	public function create()
	{
	    echo __FUNCTION__;
	}
	
	public function show(Request $request)
	{
	    echo __FUNCTION__;
	}
	public function edit()
	{
	    echo __FUNCTION__;
	}
	public function build(Request $request)
	{
	}
	
	public function fileform(Request $request)
	{
	    return view('fileform');
	}
	
	//文件上传
	public function fileadd(Request $request)
	{
	}
	
	public function test(Request $request)
	{
	    
	}

}
