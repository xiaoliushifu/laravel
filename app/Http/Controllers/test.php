<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use DB;
use App\Http\Controllers\Controller;

class Test extends Controller
{
    private $fields;

	private function getfields()
	{
		$tmparr=DB::select("desc users");
		foreach($tmparr as $colobj)
		{
			$fields[]=$colobj->Field;
			if($colobj->Key=="PRI")
				$fields['PRI']=$colobj->Field;
		}
		$this->fields = $fields;
	}
	public function index(Request $request,$id=null)
	{
	    $this->getfields();
	    $fields=$this->fields;
	    unset($fields['PRI']);
		return view('test.index', [
			'fields' => $fields,
		]);
	}

	//这就叫依赖注入（Request这个类写在参数的位置上，而不是在方法体里进行new。
	public function store(Request $request)
	{
		//var_dump($request->all());
		//var_dump($request->headers);
		//$request->flash();
		$request->getSession();
		//var_dump($request->cookies);
		die();
		$sql="insert into users(id,name,email,password,created_at,updated_at) values(
		     null,'".$_POST['name']."','".$_POST['email']."','".$_POST['password']."',now(),now())";
		DB::select($sql);
		echo "你好，{$_POST['name']},已经入库了吧";
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
	    echo $request->old('name');
	    var_dump($request->getSession());
	    echo __FUNCTION__;
	}
	
	public function fileform(Request $request)
	{
	    return view('fileform');
	}
	
	//文件上传
	public function fileadd(Request $request)
	{
	    //是否有文件
	    if($request->hasFile('uile')){
	        $file = $request->file('uile');
	        //文件是否有效
	        if($file->isValid()){
	            //如何确保移动成功
	            $file->move('D:\wampServer\tmp','lara_upload_file.php');
	            echo "文件上传成功";
	        }else{
	            echo "文件无效";
	        }
	    }else 
	        echo "not file";
	}
	
	public function test(Request $request)
	{
	    
	    //视图
	    return view()->file('D:\wampServer\tmp\aaa.php');
	    
	    //返回json数据
	    //response()->json(array('aaa'=>'bbb'));
	    //文件下载,下载后是否删除服务器端文件，文件名不能含中文
	    //return response()->download('D:\wampServer\tmp\lara_upload_好.php');
	    
	    /* return Response('你好了', '404')
	    ->header('Content-Type', 'text/html;charset=utf-8')
	    ->header('X-Powered-By','PHP/5.7.12'); */
	    
	    /* return response()
	    ->view('welcome')
	    ->header('Cache','no-cache'); */
	    
	    /* var_dump($request->path());
	    var_dump($request->ajax());
	    var_dump($request->method());
	    var_dump($request->isMethod('get'));
	    var_dump($request->is('test/*'));
	    var_dump($request->url('test/*')); */
	}

}
