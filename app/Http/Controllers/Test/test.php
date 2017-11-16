<?php

namespace App\Http\Controllers\Test;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Test extends Controller
{
	public function index(Request $request,$id=null)
	{
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
