<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Task;
use App\User;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Gate;
use Auth;

class TaskController extends Controller
{
    //中间件也可以注册到指定的控制器中
    //protected  $middleware=['auth'];
    public function __construct()
    {
        
        //$this->middleware('auth');
    }

	public function index(Request $request)
	{
	    $config = app('config');
	    //var_dump($config);exit;
	    $a =$config->get('app.debug');
	    $a =$config->get('app.url');
	    print_r($_ENV);
	    $a = env('alioss');
	    // To something like this:
	    //config('services.bugsnag.key')
	    //$a =$config->all();
	    \Log::info($a);
	    return $a;
	    echo 'aaaaaaaaaaaa<br />';
	    //手动指定当前登录用户是xxx
	    //Auth::loginUsingId(4);
	    //dump($request->user());
	    //一种用法 cannot ,can ，由model类来调用
	    //if ($request->user()->cannot('delete-post')) {
	    
	    //第二种用法  allows,denies,其中check是allows的别名，直接使用gate服务来调用
	    //if (Gate::check('delete-post')) {//底层还是调用了check方法
	    
	    //第四种方法，使用当前控制器继承的trait方法authorize,如果使用策略类的方式，必须传递第二个参数
	    /* if($this->authorize('upload')){
	       exit('deny');
         //还有第三种方法，是直接在视图文件中使用@can @else
        //表单请求中，也可以使用
	    }
	    exit('yes'); */
	    //注意，无论如何使用，都必须先定义才行
		//取得数据库中user_id是xx的用户的所有tasks,并交给task.index视图渲染
		//$tasks = Task::where('user_id', $request->user()->id)->get();
		$tasks=['a'=>'1','b'=>'2','c'=>'3'];
		
		return response()->view('tasks.index', [
			'tasks' => $tasks,
		])->header('contEnt-tYpe','text/html;charset=utf-8');
	}

	public function store(Request $request)
	{
		$this->validate($request, ['name' => 'required|max:255',]
						);


		$request->user()->tasks()->create([
			'name' => $request->name,
		]);

		return redirect('/tasks');
	}
	
	public function getIndex()
	{
	    $user = new \App\User();
	    return $user->paginate(4);
	    echo __FUNCTION__;
	    print_r(func_get_args());
	}
	public function putIndex2($id)
	{
	    echo __FUNCTION__;
	    echo $id;
	}
	public function patchIndex2($id)
	{
	    echo __FUNCTION__;
	    echo $id;
	}
	public function deleteIndex2($id)
	{
	    echo __FUNCTION__;
	    echo $id;
	}
	public function optionsIndex2($id)
	{
	    echo __FUNCTION__;
	    echo $id;
	}
	public function trackIndex2($id)
	{
	    echo __FUNCTION__;
	    echo $id;
	}
	public function connectIndex2($id)
	{
	    echo __FUNCTION__;
	    echo $id;
	}
}