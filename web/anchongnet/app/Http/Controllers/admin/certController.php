<?php

namespace App\Http\Controllers\admin;

use Request;
use App\Auth;
use App\Qua;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;

class certController extends Controller
{
	private $auth;
	private $qua;
	public function __construct(){
		$this->auth=new Auth();
		$this->qua=new Qua();
	}
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $keyId=Request::input("id");
		$keyStatus=Request::input("auth_status");
        if($keyId=="" && $keyStatus==""){
		    $datas=$this->auth->paginate(4);
		}else if(empty($keyStatus)){
		//注意，这里的Auth不是Facade机制的Auth，而是Model类Auth，其中定义了scorexx方法
			$datas = Auth::Ids($keyId)->paginate(4);
		}else if(empty($keyId)){
			$datas = Auth::Status($keyStatus)->paginate(4);
		}else{
			$datas = Auth::Ids($keyId)->Status($keyStatus)->paginate(4);
		}
		$args=array("id"=>$keyId,"auth_status"=>$keyStatus);
		return view('admin/users/cert',array("datacol"=>compact("args","datas")));	
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *目前是由列表页的，ajax调用
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
		/*$auth=Auth::Users($id)->get();
		$auth_name=$auth[0]['auth_name'];
		$auth_con=[];
		$auth_obj=[];
		for($i=0;$i<count($auth);$i++){
			$auth_obj['qua_name']=$auth[$i]['qua_name'];
			$auth_obj['explanation']=$auth[$i]['explanation'];
			$auth_obj['credentials']=$auth[$i]['credentials'];
			array_push($auth_con,$auth_obj);
		}
		return response()->json([
		    'auth_name' => $auth_name,
			'auth_con' => $auth_con
		]);*/
        //应该只查询想要的信息，而不是一个get()了事。
        //只查询三个字段，就写三个就行了get(array())
		$data=Qua::Ids($id)->get(array('credentials','qua_name','explanation'));
        return $data;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
