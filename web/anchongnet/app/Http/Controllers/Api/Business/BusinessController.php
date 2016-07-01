<?php

namespace App\Http\Controllers\Api\Business;

//use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Request;
use Validator;
use DB;

/*
*   该控制器包含了商机模块的操作
*/
class BusinessController extends Controller
{
    /*
    *   该方法提供了商机发布的功能
    */
    public function release(Request $request)
    {
        //获得app端传过来的json格式的数据转换成数组格式
        $data=$request::all();
        $param=json_decode($data['param'],true);
        //验证用户传过来的数据是否合法
        $validator = Validator::make($param,
            [
                'type' => 'required',
                'title' => 'required|max:126',
                'content' => 'required|min:4',
                'tag' => 'required',
                'pic' => 'array',
            ]
        );
        //如果出错返回出错信息，如果正确执行下面的操作
        if ($validator->fails())
        {
            return response()->json(['serverTime'=>time(),'ServerNo'=>8,'ResultData'=>['Message'=>'请填写完整，并且标题长度不能超过60个字，工程简介不能低于4个字']]);
        }else{
            //创建用户表通过电话查询出用户电话
            $users=new \App\Users();
            $users_phone=$users->quer('phone',['users_id'=>$data['guid']])->toArray();
            //判断用户数据表中是否有电话联系方式
            if($users_phone[0]['phone']){
                $users_message=new \App\Usermessages();
                $users_contact=$users_message->quer('contact',['users_id'=>$data['guid']])->toArray();
                //判断用户信息表中是否有联系人姓名
                if($users_contact){
                    $tags_arr=explode(' ',$param['tags']);
                    $tags="";
                    if(!empty($tags_arr)){
                        foreach ($tags_arr as $tag_arr) {
                            $tags.=bin2hex($tag_arr)." ";
                        }
                    }
                    $business_data=[
                        'users_id' => $data['guid'],
                        'title' => $param['title'],
                        'type' => $param['type'],
                        'created_at' => date('Y-m-d H:i:s',$data['time']),
                        'content' => $param['content'],
                        'tag' => $param['tag'],
                        'tags' => $param['tags'],
                        'tags_match' => $tags,
                        'phone' => $users_phone[0]['phone'],
                        'contact' => $users_contact[0]['contact'],
                    ];
                    //开启事务处理
                    DB::beginTransaction();
                    //创建插入方法
                    $business=new \App\Business();
                    $id=$business->add($business_data);
                    //插入成功继续插图片，插入失败则返回错误信息
                    if(!empty($id)){
                        if($param['pic']){
                            $ture=false;
                            foreach ($param['pic'] as $pic) {
                                $business_img=new \App\Business_img();
                                $ture=$business_img->add(['bid'=>$id,'img'=> $pic]);
                                //假如有一张图片插入失败就返回错误
                                if(!$ture){
                                    //假如失败就回滚
                                    DB::rollback();
                                    return response()->json(['serverTime'=>time(),'ServerNo'=>8,'ResultData'=>['Message'=>'发布商机失败，请重新发布']]);
                                }
                            }
                            //orm模型操作数据库会返回true或false,如果操作失败则返回错误信息
                            if($ture){
                                //假如成功就提交
                                DB::commit();
                                return response()->json(['serverTime'=>time(),'ServerNo'=>0,'ResultData'=>['Message'=>'发布信息成功']]);
                            }else{
                                //假如失败就回滚
                                DB::rollback();
                                return response()->json(['serverTime'=>time(),'ServerNo'=>8,'ResultData'=>['Message'=>'请重新发布信息']]);
                            }
                        }else{
                            //假如成功就提交
                            DB::commit();
                            return response()->json(['serverTime'=>time(),'ServerNo'=>0,'ResultData'=>['Message'=>'发布信息成功']]);
                        }
                    }else{
                        //假如失败就回滚
                        DB::rollback();
                        return response()->json(['serverTime'=>time(),'ServerNo'=>8,'ResultData'=>['Message'=>'请重新发布信息']]);
                    }
                }else{
                    return response()->json(['serverTime'=>time(),'ServerNo'=>8,'ResultData'=>['Message'=>'请完善个人信息中的联系方式']]);
                }
            }else{
                return response()->json(['serverTime'=>time(),'ServerNo'=>8,'ResultData'=>['Message'=>'请完善个人信息中的联系方式']]);
            }
        }
    }

    /*
    *   该方法是向APP提供类型与标签
    */
    public function typetag()
    {
        //创建类型的orm模型
        $business_type=new \App\Business_type();
        //创建标签的orm模型
        $business_tag=new \App\Business_tag();
        //取出所有类型
        $business_type_data=$business_type->quer(['id','title'])->toArray();
        foreach ($business_type_data as $value) {
            foreach ($value as $value_data) {
                //因为取出的是数组所以要判断是否为id
                if(is_numeric($value_data)){
                    //取出所有标签
                    $business_tag_data=$business_tag->quer('tag',$value_data)->toArray();
                    foreach ($business_tag_data as $business_tag_value) {
                        foreach ($business_tag_value as $business_tag_value1) {
                            $business_tag_data_value[]=$business_tag_value1;
                        }
                    }
                }else {
                    //通过拼接和组合将数据变成合格的json
                    $typetag_array[]=['type'=>$value_data,'tag'=>$business_tag_data_value];
                    $business_tag_data_value=null;
                }
            }
        }
        //假如没有查出数据
        if(empty($typetag_array)){
            return response()->json(['serverTime'=>time(),'ServerNo'=>8,'ResultData'=>['Message'=>"查询失败"]]);
        }else{
            return response()->json(['serverTime'=>time(),'ServerNo'=>0,'ResultData'=>$typetag_array]);
        }
    }

    /*
    *   该方法提供商机检索标签
    */
    public function search(Request $request)
    {
        //获得app端传过来的json格式的数据转换成数组格式
        $data=$request::all();
        $param=json_decode($data['param'],true);
        //创建标签的orm模型
        $business_tag=new \App\Business_tag();
        //查询分类标签
        $business_tag_tag=$business_tag->search_quer('tag',$param['type'])->toArray();
        //便利将关联数组转为索引数组
        foreach ($business_tag_tag as $value1) {
            foreach ($value1 as $key => $value) {
                $result_tag[]=$value;
            }
        }
        //查询地域标签
        $business_tag_area=$business_tag->search_quer('tag',0)->toArray();
        //便利将关联数组转为索引数组
        foreach ($business_tag_area as $value2) {
            foreach ($value2 as $value3) {
                $result_area[]=$value3;
            }
        }
        //假如有数据就返回，否则返回查询失败
        if(empty($business_tag_tag) && empty($business_tag_area)){
            return response()->json(['serverTime'=>time(),'ServerNo'=>8,'ResultData'=>['Message'=>"查询失败"]]);
        }else{
            return response()->json(['serverTime'=>time(),'ServerNo'=>0,'ResultData'=>['tag'=>$result_tag,'area'=>$result_area]]);
        }
    }

    /*
    *   该方法提供商机查询
    */
    public function businessinfo(Request $request)
    {
        //获得app端传过来的json格式的数据转换成数组格式
        $data=$request::all();
        $param=json_decode($data['param'],true);
        //默认每页数量
        $limit=20;
        //创建商机表的orm模型
        $business=new \App\Business();
        $businessinfo=array('bid','phone','contact','title','content','tag','tags','created_at');
        if(empty($param['tag']) && empty($param['search'])){
            //假如没有检索则sql语句为
            $sql='type ='.$param['type'];
        }elseif(!empty($param['tag']) && empty($param['search'])){
            //根据标签检索
            $sql='type ='.$param['type']." and tag='".$param['tag']."'";
        }elseif(empty($param['tag']) && !empty($param['search'])){
            //自定义检索
            $sql="MATCH(tags_match) AGAINST('".bin2hex($param['search'])."') and type =".$param['type'];
        }elseif(!empty($param['tag']) && !empty($param['search'])){
            $sql="MATCH(tags_match) AGAINST('".bin2hex($param['search'])."') and type =".$param['type']." and tag ='".$param['tag']."'";
        }
        $businessinfo_data=$business->quer($businessinfo,$sql,(($param['page']-1)*$limit),$limit);
        $list=null;
        if($businessinfo_data){
            //创建图片查询的orm模型
            $business_img=new \App\Business_img();
            //通过数组数据的组合将数据拼凑为{"total":3,"list":[{"bid":1,"phone":"","contact":"","title":"","content":"","tag":"","created_at":"2016-02-24 08:02:50","pic":["1","2"]}格式
            foreach ($businessinfo_data['list'] as $business_data) {
                $value_1=$business_img->quer('img',$business_data['bid']);
                //判断是否为空,如果是空表明没有图片
                if(empty($value_1)){
                    $list[]=$business_data;
                }else{
                    //假如不为空表明有图片，开始查询拼凑数据
                    foreach ($value_1 as $value_2) {
                        foreach ($value_2 as $value_3) {
                            $img[]=$value_3;
                        }
                    }
                    //重构数组，加上键值
                    $img_data=['pic'=>$img];
                    $list[]=array_merge($business_data,$img_data);
                    $img=null;
                }
            }
            $showphone=0;
            if($data['guid'] == 0){
                $showphone=0;
            }else{
                $users=new \App\Users();
                $users_auth=$users->quer('certification',['users_id'=>$data['guid']])->toArray();
                if($users_auth[0]['certification'] == 3){
                    $showphone=1;
                }
            }
            //返回数据总数和具体数据
            return response()->json(['serverTime'=>time(),'ServerNo'=>0,'ResultData'=>['total'=>$businessinfo_data['total'],'showphone'=>$showphone,'list'=>$list]]);
        }else{
            return response()->json(['serverTime'=>time(),'ServerNo'=>8,'ResultData'=>['Message'=>"查询失败"]]);
        }
    }

    /*
    *   该方法提供热门招标项目查询
    */
    public function businesshot(Request $request)
    {
        //获得app端传过来的json格式的数据转换成数组格式
        $data=$request::all();
        $param=json_decode($data['param'],true);
        //默认每页数量
        $limit=20;
        //创建商机表的orm模型
        $business=new \App\Business();
        $businessinfo=array('bid','phone','contact','title','content','tag','tags','created_at');
        //假如没有检索则sql语句为
        $sql='recommend = 1 and type = 1';
        $businessinfo_data=$business->quer($businessinfo,$sql,(($param['page']-1)*$limit),$limit);
        $list=null;
        if($businessinfo_data){
            //创建图片查询的orm模型
            $business_img=new \App\Business_img();
            //通过数组数据的组合将数据拼凑为{"total":3,"list":[{"bid":1,"phone":"","contact":"","title":"","content":"","tag":"","created_at":"2016-02-24 08:02:50","pic":["1","2"]}格式
            foreach ($businessinfo_data['list'] as $business_data) {
                $value_1=$business_img->quer('img',$business_data['bid']);
                //判断是否为空,如果是空表明没有图片
                if(empty($value_1)){
                    $list[]=$business_data;
                }else{
                    //假如不为空表明有图片，开始查询拼凑数据
                    foreach ($value_1 as $value_2) {
                        foreach ($value_2 as $value_3) {
                            $img[]=$value_3;
                        }
                    }
                    //重构数组，加上键值
                    $img_data=['pic'=>$img];
                    $list[]=array_merge($business_data,$img_data);
                    $img=null;
                }
            }
            $showphone=0;
            if($data['guid'] == 0){
                $showphone=0;
            }else{
                $users=new \App\Users();
                $users_auth=$users->quer('certification',['users_id'=>$data['guid']])->toArray();
                if($users_auth[0]['certification'] == 3){
                    $showphone=1;
                }
            }
            //返回数据总数和具体数据
            return response()->json(['serverTime'=>time(),'ServerNo'=>0,'ResultData'=>['total'=>$businessinfo_data['total'],'showphone'=>$showphone,'list'=>$list]]);
        }else{
            return response()->json(['serverTime'=>time(),'ServerNo'=>8,'ResultData'=>['Message'=>"查询失败"]]);
        }
    }

    /*
    *   该方法提供热门工程项目查询
    */
    public function hotproject(Request $request)
    {
        //获得app端传过来的json格式的数据转换成数组格式
        $data=$request::all();
        $param=json_decode($data['param'],true);
        //默认每页数量
        $limit=20;
        //创建商机表的orm模型
        $business=new \App\Business();
        $businessinfo=array('bid','phone','contact','title','content','tag','tags','created_at');
        //假如没有检索则sql语句为
        $sql='recommend = 1 and type in(1,2)';
        $businessinfo_data=$business->quer($businessinfo,$sql,(($param['page']-1)*$limit),$limit);
        $list=null;
        if($businessinfo_data){
            //创建图片查询的orm模型
            $business_img=new \App\Business_img();
            //通过数组数据的组合将数据拼凑为{"total":3,"list":[{"bid":1,"phone":"","contact":"","title":"","content":"","tag":"","created_at":"2016-02-24 08:02:50","pic":["1","2"]}格式
            foreach ($businessinfo_data['list'] as $business_data) {
                $value_1=$business_img->quer('img',$business_data['bid']);
                //判断是否为空,如果是空表明没有图片
                if(empty($value_1)){
                    $list[]=$business_data;
                }else{
                    //假如不为空表明有图片，开始查询拼凑数据
                    foreach ($value_1 as $value_2) {
                        foreach ($value_2 as $value_3) {
                            $img[]=$value_3;
                        }
                    }
                    //重构数组，加上键值
                    $img_data=['pic'=>$img];
                    $list[]=array_merge($business_data,$img_data);
                    $img=null;
                }
            }
            $showphone=0;
            if($data['guid'] == 0){
                $showphone=0;
            }else{
                $users=new \App\Users();
                $users_auth=$users->quer('certification',['users_id'=>$data['guid']])->toArray();
                if($users_auth[0]['certification'] == 3){
                    $showphone=1;
                }
            }
            //返回数据总数和具体数据
            return response()->json(['serverTime'=>time(),'ServerNo'=>0,'ResultData'=>['total'=>$businessinfo_data['total'],'showphone'=>$showphone,'list'=>$list]]);
        }else{
            return response()->json(['serverTime'=>time(),'ServerNo'=>8,'ResultData'=>['Message'=>"查询失败"]]);
        }
    }

    /*
    *   该方法提供最新招标项目查询
    */
    public function recent(Request $request)
    {
        //获得app端传过来的json格式的数据转换成数组格式
        $data=$request::all();
        $param=json_decode($data['param'],true);
        //默认每页数量
        $limit=20;
        //创建商机表的orm模型
        $business=new \App\Business();
        $businessinfo=array('bid','phone','contact','title','content','tag','tags','created_at');
        if(!empty($param['tag'])){
            //根据标签检索
            $sql=" tag = '".$param['tag']."' and type in(1,2)";
        }else{
            //假如没有检索则sql语句为
            $sql="type in(1,2)";
        }
        $businessinfo_data=$business->quer($businessinfo,$sql,(($param['page']-1)*$limit),$limit);
        $list=null;
        if($businessinfo_data){
            //创建图片查询的orm模型
            $business_img=new \App\Business_img();
            //通过数组数据的组合将数据拼凑为{"total":3,"list":[{"bid":1,"phone":"","contact":"","title":"","content":"","tag":"","created_at":"2016-02-24 08:02:50","pic":["1","2"]}格式
            foreach ($businessinfo_data['list'] as $business_data) {
                $value_1=$business_img->quer('img',$business_data['bid']);
                //判断是否为空,如果是空表明没有图片
                if(empty($value_1)){
                    $list[]=$business_data;
                }else{
                    //假如不为空表明有图片，开始查询拼凑数据
                    foreach ($value_1 as $value_2) {
                        foreach ($value_2 as $value_3) {
                            $img[]=$value_3;
                        }
                    }
                    //重构数组，加上键值
                    $img_data=['pic'=>$img];
                    $list[]=array_merge($business_data,$img_data);
                    $img=null;
                }
            }
            $showphone=0;
            if($data['guid'] == 0){
                $showphone=0;
            }else{
                $users=new \App\Users();
                $users_auth=$users->quer('certification',['users_id'=>$data['guid']])->toArray();
                if($users_auth[0]['certification'] == 3){
                    $showphone=1;
                }
            }
            //返回数据总数和具体数据
            return response()->json(['serverTime'=>time(),'ServerNo'=>0,'ResultData'=>['total'=>$businessinfo_data['total'],'showphone'=>$showphone,'list'=>$list]]);
        }else{
            return response()->json(['serverTime'=>time(),'ServerNo'=>8,'ResultData'=>['Message'=>"查询失败"]]);
        }
    }


    /*
    *   该方法提供商机首页
    */
    public function businessindex(Request $request)
    {
        //获得app端传过来的json格式的数据转换成数组格式
        $data=$request::all();
        $param=json_decode($data['param'],true);
        //创建商机表的orm模型
        $business=new \App\Business();
        $businessinfo=array('bid','phone','contact','title','content','tag','tags','created_at');
        //单个商机查询
        $businessinfo_data=$business->quertime($businessinfo,'bid='.$param['bid'])->toArray();
        $list=null;
        if($businessinfo_data){
            //创建图片查询的orm模型
            $business_img=new \App\Business_img();
            //通过数组数据的组合将数据拼凑
            foreach ($businessinfo_data as $business_data) {
                $value_1=$business_img->quer('img',$business_data['bid']);
                //判断是否为空,如果是空表明没有图片
                if(empty($value_1)){
                    $list['data']=$business_data;
                }else{
                    //假如不为空表明有图片，开始查询拼凑数据
                    foreach ($value_1 as $value_2) {
                        foreach ($value_2 as $value_3) {
                            $img[]=$value_3;
                        }
                    }
                    //重构数组，加上键值
                    $img_data=['pic'=>$img];
                    $list['data']=array_merge($business_data,$img_data);
                }
            }
            $showphone=0;
            if($data['guid'] == 0){
                $showphone=0;
            }else{
                $users=new \App\Users();
                $users_auth=$users->quer('certification',['users_id'=>$data['guid']])->toArray();
                if($users_auth[0]['certification'] == 3){
                    $showphone=1;
                }
            }
            $list['showphone']=$showphone;
            //返回数据总数和具体数据
            return response()->json(['serverTime'=>time(),'ServerNo'=>0,'ResultData'=>$list]);
        }else{
            return response()->json(['serverTime'=>time(),'ServerNo'=>8,'ResultData'=>['Message'=>"查询失败"]]);
        }
    }

    /*
    *   该方法提供个人发布商机查询
    */
    public function mybusinessinfo(Request $request)
    {
        //获得app端传过来的json格式的数据转换成数组格式
        $data=$request::all();
        $param=json_decode($data['param'],true);
        //默认每页数量
        $limit=20;
        //创建商机表的orm模型
        $business=new \App\Business();
        $businessinfo=array('bid','phone','contact','title','content','tag','tags','created_at');
        $businessinfo_data=$business->quer($businessinfo,'users_id='.$data['guid']." and type =".$param['type'],(($param['page']-1)*$limit),$limit);
        $list=null;
        if($businessinfo_data){
            //创建图片查询的orm模型
            $business_img=new \App\Business_img();
            //通过数组数据的组合将数据拼凑为{"total":3,"list":[{"bid":1,"phone":"","contact":"","title":"","content":"","tag":"","created_at":"2016-02-24 08:02:50","pic":["1","2"]}}格式
            foreach ($businessinfo_data['list'] as $business_data) {
                $value_1=$business_img->quer('img',$business_data['bid']);
                //判断是否为空,如果是空表明没有图片
                if(empty($value_1)){
                    $list[]=$business_data;
                }else{
                    //假如不为空表明有图片，开始查询拼凑数据
                    foreach ($value_1 as $value_2) {
                        foreach ($value_2 as $value_3) {
                            $img[]=$value_3;
                        }
                    }
                    //重构数组，加上键值
                    $img_data=['pic'=>$img];
                    $list[]=array_merge($business_data,$img_data);
                    $img=null;
                }
            }
            //返回数据总数和具体数据
            return response()->json(['serverTime'=>time(),'ServerNo'=>0,'ResultData'=>['total'=>$businessinfo_data['total'],'list'=>$list]]);
        }else{
            return response()->json(['serverTime'=>time(),'ServerNo'=>8,'ResultData'=>['Message'=>"查询失败"]]);
        }
    }

    /*
    *   对个人发布的商机做操作
    */
    public function businessaction(Request $request)
    {
        //获得app端传过来的json格式的数据转换成数组格式
        $data=$request::all();
        $param=json_decode($data['param'],true);
        //创建ORM模型
        $business=new \App\Business();
        //判断用户行为，1为更新时间
        if($param['action'] == "1"){
            //更新时间
            $result=$business->businessupdate($param['bid'],['created_at' => date('Y-m-d H:i:s',$data['time'])]);
            if($result){
                $update_time=$business->quertime('updated_at','bid = '.$param['bid'])->toArray();
                if($update_time){
                    //成功返回操作成功
                    return response()->json(['serverTime'=>time(),'ServerNo'=>0,'ResultData'=>$update_time[0]['updated_at']]);
                }else {
                    //失败返回操作失败
                    return response()->json(['serverTime'=>time(),'ServerNo'=>8,'ResultData'=>['Message'=>'操作失败']]);
                }
            }else{
                //失败返回操作失败
                return response()->json(['serverTime'=>time(),'ServerNo'=>8,'ResultData'=>['Message'=>'操作失败']]);
            }
        //判断用户行为，2为删除
        }elseif($param['action'] == "2") {
            //开启事务处理
            DB::beginTransaction();
            $delresult=$business->businessdel($param['bid']);
            if($delresult){
                $business_img=new \App\Business_img();
                //删除图片的方法
                $delresults=$business_img->delimg($param['bid']);
                if($delresults){
                    //假如成功就提交
                    DB::commit();
                    return response()->json(['serverTime'=>time(),'ServerNo'=>0,'ResultData'=>['Message'=>'操作成功']]);
                }else{
                    //假如失败就回滚
                    DB::rollback();
                    return response()->json(['serverTime'=>time(),'ServerNo'=>8,'ResultData'=>['Message'=>'操作失败']]);
                }
            }else{
                return response()->json(['serverTime'=>time(),'ServerNo'=>8,'ResultData'=>['Message'=>'操作失败']]);
            }
        }else{
            return response()->json(['serverTime'=>time(),'ServerNo'=>8,'ResultData'=>['Message'=>'非法操作']]);
        }
    }

    /*
    *   对个人发布的商机做修改
    */
    public function businessedit(Request $request)
    {
        //获得app端传过来的json格式的数据转换成数组格式
        $data=$request::all();
        $param=json_decode($data['param'],true);
        //验证用户传过来的数据是否合法
        $validator = Validator::make($param,
            [
                'type' => 'required',
                'title' => 'required|max:126',
                'content' => 'required|min:4',
                'tag' => 'required',
                'pic' => 'array',
            ]
        );
        //如果出错返回出错信息，如果正确执行下面的操作
        if ($validator->fails())
        {
            return response()->json(['serverTime'=>time(),'ServerNo'=>8,'ResultData'=>['Message'=>'请填写完整，并且标题长度不能超过60个字，工程简介不能低于4个字']]);
        }else{
            $tags_arr=explode(' ',$param['tags']);
            $tags="";
            if(!empty($tags_arr)){
                foreach ($tags_arr as $tag_arr) {
                    $tags.=bin2hex($tag_arr)." ";
                }
            }
            //需要更新的数据
            $business_data=[
                'title' => $param['title'],
                'created_at' => date('Y-m-d H:i:s',$data['time']),
                'content' => $param['content'],
                'tag' => $param['tag'],
                'tags' => $param['tags'],
                'tags_match' => $tags,
            ];
            //开启事务处理
            DB::beginTransaction();
            //创建插入方法
            $business=new \App\Business();
            $updateresult=$business->businessupdate($param['bid'],$business_data);
            //假如更新成功就继续更新图片
            if($updateresult){
                if($param['pic']){
                    $ture=false;
                    $business_imgs=new \App\Business_img();
                    //删除图片的方法
                    $delresults=$business_imgs->delimg($param['bid']);
                    if($delresults){
                        foreach ($param['pic'] as $pic) {
                            $business_img=new \App\Business_img();
                            $ture=$business_img->add(['bid'=>$param['bid'],'img'=> $pic]);
                            //假如有一张图片插入失败就返回错误
                            if(!$ture){
                                //假如失败就回滚
                                DB::rollback();
                                return response()->json(['serverTime'=>time(),'ServerNo'=>8,'ResultData'=>['Message'=>'修改信息失败，请重新修改']]);
                            }
                        }
                        //orm模型操作数据库会返回true或false,如果操作失败则返回错误信息
                        if($ture){
                            //假如成功就提交
                            DB::commit();
                            return response()->json(['serverTime'=>time(),'ServerNo'=>0,'ResultData'=>['Message'=>'修改信息成功']]);
                        }else{
                            //假如失败就回滚
                            DB::rollback();
                            return response()->json(['serverTime'=>time(),'ServerNo'=>8,'ResultData'=>['Message'=>'修改信息失败，请重新修改']]);
                        }
                    }else{
                        //假如失败就回滚
                        DB::rollback();
                        return response()->json(['serverTime'=>time(),'ServerNo'=>8,'ResultData'=>['Message'=>'修改信息失败，请重新修改']]);
                    }
                }else{
                    //假如成功就提交
                    DB::commit();
                    return response()->json(['serverTime'=>time(),'ServerNo'=>0,'ResultData'=>['Message'=>'修改信息成功']]);
                }
            }else{
                //假如失败就回滚
                DB::rollback();
                return response()->json(['serverTime'=>time(),'ServerNo'=>8,'ResultData'=>['Message'=>'修改信息失败，请重新修改']]);
            }

        }
    }
}
