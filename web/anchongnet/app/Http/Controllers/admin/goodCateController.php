<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\GoodCat;
use Illuminate\Support\Facades\Log;

class goodCateController extends Controller
{
    private $cat;
    /*
     * 构造方法
     * */
    public function __construct()
    {
        $this->cat=new GoodCat();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $req)
    {
        $keyName=$req->get('keyName');
        if($keyName==""){
            $datas=$this->cat->paginate(8);
        }else{
            $datas = GoodCat::Name($keyName)->paginate(8);
        }
        $args=array("keyName"=>$keyName);
        return view('admin/cate/index',array("datacol"=>compact("args","datas")));
    }

    /*
     * 获取某个二级分类的所有兄弟分类的方法
     * 即获取同一个一级分类下的所有二级分类的方法
     * */
    public function newgetSiblings(Request $request){
        $cid=$request['cid'];
        $pid=$this->cat->find($cid)->parent_id;
        $datas=$this->cat->Level($pid)->get()->toArray();
        $result['cnum']=$request['id'];
        $result['datas']=$datas;
        $result['cid']=$cid;
        $result['parent_id']=$pid;
        return $result;
    }

    /*
     * 获取指定一级或二级分类的方法
     * */
    public function newgetLevel(Request $request){
        $pid=$request['pid'];
        $datas = GoodCat::Level($pid)->get();
        $result['cnum']=$request['id'];
        $result['datas']=$datas;
        return $result;
    }

    /*
    * 获取某个二级分类的所有兄弟分类的方法
    * 即获取同一个一级分类下的所有二级分类的方法
    * */
   public function getSiblings(Request $request){
       $cid=$request['cid'];
       //先找到它的父类
       $pid=$this->cat->find($cid)->parent_id;
       //再找到它的子类
       //也就实现了找同级分类  ScoreLevel方法
       $datas=$this->cat->Level($pid)->get();
       return $datas;
   }

   /*
    * 获取指定一级或二级分类的方法
    * */
   public function getLevel(Request $request){
       $pid=$request['pid'];
       //下面的方法，会在laravel.log文件里打印一条日志记录
       //[2016-06-26 11:24:07] local.INFO: pid:0
       //Log::info('pid:'.$pid);
       
       $datas = GoodCat::Level($pid)->get(array('cat_id','cat_name'));
       return $datas;
   }


    /*
     * 获取所有二级分类的方法
     * */
    public function getLevel2(){
        $datas = GoodCat::Level2()->get(array('cat_id','cat_name'));
        return $datas;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("admin.cate.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //暂时没有错误检查吗？
        $this->cat->cat_name=$request['catname'];
        $this->cat->keyword=$request['keyword'];
        $this->cat->cat_desc=$request['description'];
        $this->cat->is_show=$request['ishow'];
        $this->cat->parent_id=$request['parent'];
        $result=$this->cat->save();
        if($result){
            return redirect()->back();
        }else{
            dd("添加失败，请返回重试");
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data=$this->cat->find($id);
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
        //find应该也可以select指定列吧，是不是find方法的第二个参数呢？果然是。
        //而且使用find找到的是一个对象，
        //如果使用get，则返回的是一个集合的对象
        $data=$this->cat->find($id,array('cat_name','keyword','is_show','cat_desc'));
        return $data;
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
        $data=$this->cat->find($id);
        $data->cat_name=$request['catname'];
        $data->keyword=$request['keyword'];
        $data->cat_desc=$request['description'];
        $data->is_show=$request['ishow'];
        $data->parent_id=$request['parent'];
        $result=$data->save();
        if($result){
            return redirect()->back();
        }else{
            dd("修改失败，请返回重试");
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $result=$this->cat->find($id)->delete();
        if($result){
            return "删除成功";
        }else{
            return "删除失败";
        }
    }
}
