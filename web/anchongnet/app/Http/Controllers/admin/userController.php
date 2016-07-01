<?php

namespace App\Http\Controllers\admin;
use Request;
use App\Users;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;

class userController extends Controller
{
	private $user;
	public function __construct(){
		$this->user=new Users();
	}
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        //提供 <普通会员>和<商家>的筛选功能,分页的参数，全部交给paginate()就OK了
		$keyPhone=Request::input("phone");
		$keyLevel=Request::input("users_rank");
        if($keyPhone=="" && $keyLevel==""){
		    $datas=$this->user->paginate(8);//paginate方法看起来是user类的，其实是Builder类的
		    //且自带解析page参数，只这一行代码就完成了分页
		}else if(empty($keyLevel)){
			$datas = Users::Phone($keyPhone)->paginate(8);
		}else if(empty($keyPhone)){
			$datas = Users::Level($keyLevel)->paginate(8);
		}else{
			$datas = Users::Phone($keyPhone)->Level($keyLevel)->paginate(8);
		}
		$args=array("phone"=>$keyPhone,"users_rank"=>$keyLevel);
		return view('admin/users/index',array("datacol"=>compact("args","datas")));		
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
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
