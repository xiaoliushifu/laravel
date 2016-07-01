<?php

namespace App\Http\Controllers\Api\Order;

//use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Request;
use Validator;
use DB;

/*
*   该控制器包含了订单模块的操作
*/
class OrderController extends Controller
{
    /*
    *   该方法提供了订单生成的功能
    */
    public function ordercreate(Request $request)
    {
        //获得app端传过来的json格式的数据转换成数组格式
        $data=$request::all();
        $param=json_decode($data['param'],true);
        $true=false;
        //开启事务处理
        DB::beginTransaction();
        //遍历传过来的订单数据
        foreach ($param['list'] as $orderarr) {
            //查出该订单生成的联系人姓名
            $usermessages=new \App\Usermessages();
            $name=$usermessages->quer('contact',['users_id'=>$data['guid']]);
            if(empty($name[0]['contact'])){
                //假如失败就回滚
                DB::rollback();
                return response()->json(['serverTime'=>time(),'ServerNo'=>12,'ResultData'=>['Message'=>'请先填写个人资料里面的联系人']]);
            }
            //查出该店铺客服联系方式
            $customer=new \App\Shop();
            $customers=$customer->quer('customer',"sid =".$orderarr['sid'])->toArray();
            //如果店铺客服为空，则指定一个值不能让他报错
            if(empty($customers)){
                //假如失败就回滚
                DB::rollback();
                return response()->json(['serverTime'=>time(),'ServerNo'=>12,'ResultData'=>['Message'=>'订单生成失败']]);
            }
            $order_num=rand(10000,99999).substr($data['guid'],0,1).time();
            $order_data=[
                'order_num' => $order_num,
                'users_id' => $data['guid'],
                'sid' => $orderarr['sid'],
                'sname' => $orderarr['sname'],
                'address' => $param['address'],
                'name' => $param['name'],
                'phone' => $param['phone'],
                'total_price' => $orderarr['total_price'],
                'created_at' => date('Y-m-d H:i:s',$data['time']),
                'freight' => $orderarr['freight'],
                'invoice' => $param['invoice'],
                'customer' => $customers[0]['customer'],
                'tname' => $name[0]['contact']
            ];
            //创建订单的ORM模型
            $order=new \App\Order();
            $cart=new \App\Cart();
            //插入数据
            $result=$order->add($order_data);
            //如果成功
            if($result){
                foreach ($orderarr['goods'] as $goodsinfo) {
                    //创建货品表的ORM模型来查询货品数量
                    $goods_specifications=new \App\Goods_specifications();
                    $goods_num=$goods_specifications->quer(['title','goods_num','added'],'gid ='.$goodsinfo['gid'])->toArray();
                    //判断商品是否以删除
                    if(empty($goods_num)){
                        //假如失败就回滚
                        DB::rollback();
                        return response()->json(['serverTime'=>time(),'ServerNo'=>12,'ResultData'=>['Message'=>$goodsinfo['goods_name'].'商品已下架']]);
                    }
                    //判断商品是否下架
                    if($goods_num[0]['added'] == 1){
                    //判断总库存是否足够
                        if($goods_num[0]['goods_num'] >= $goodsinfo['goods_num']){
                            $goodsnum=$goods_num[0]['goods_num']-$goodsinfo['goods_num'];
                            //订单生产时更新库存
                            $goodsnum_result=$goods_specifications->specupdate($goodsinfo['gid'],['goods_num' => $goodsnum]);
                            if($goodsnum_result){
                                $orderinfo_data=[
                                    'order_num' =>$order_num,
                                    'goods_name' => $goodsinfo['goods_name'],
                                    'goods_num' => $goodsinfo['goods_num'],
                                    'goods_price' => $goodsinfo['goods_price'],
                                    'goods_type' => $goodsinfo['goods_type'],
                                    'img' => $goodsinfo['img']
                                ];
                                //创建购物车的ORM模型
                                $orderinfo=new \App\Orderinfo();
                                //插入数据
                                $order_result=$orderinfo->add($orderinfo_data);
                                if($order_result){
                                    $true=true;
                                    //同时删除购物车
                                    $resultdel=$cart->cartdel($goodsinfo['cart_id']);
                                    if($resultdel){
                                        $true=true;
                                    }else{
                                        $true=false;
                                    }
                                }else{
                                    //假如失败就回滚
                                    DB::rollback();
                                    return response()->json(['serverTime'=>time(),'ServerNo'=>12,'ResultData'=>['Message'=>'订单生成失败']]);
                                }
                            }else{
                                //假如失败就回滚
                                DB::rollback();
                                return response()->json(['serverTime'=>time(),'ServerNo'=>12,'ResultData'=>['Message'=>'订单生成失败']]);
                            }
                        }else{
                            //假如失败就回滚
                            DB::rollback();
                            return response()->json(['serverTime'=>time(),'ServerNo'=>12,'ResultData'=>['Message'=>$goods_num[0]['title'].'库存不足，剩余库存'.$goods_num[0]['goods_num']]]);
                        }
                    }else{
                        //假如失败就回滚
                        DB::rollback();
                        return response()->json(['serverTime'=>time(),'ServerNo'=>12,'ResultData'=>['Message'=>$goods_num[0]['title'].'已下架']]);
                    }
                }
            }else{
                //假如失败就回滚
                DB::rollback();
                return response()->json(['serverTime'=>time(),'ServerNo'=>12,'ResultData'=>['Message'=>'订单生成失败']]);
            }
        }
        if($true){
            //假如成功就提交
            DB::commit();
            return response()->json(['serverTime'=>time(),'ServerNo'=>0,'ResultData'=>['Message'=>'订单生成成功']]);
        }else{
            //假如失败就回滚
            DB::rollback();
            return response()->json(['serverTime'=>time(),'ServerNo'=>12,'ResultData'=>['Message'=>'订单生成失败']]);
        }
    }

    /*
    *   该方法提供订单查看
    */
    public function orderinfo(Request $request)
    {
        //获得app端传过来的json格式的数据转换成数组格式
        $data=$request::all();
        $param=json_decode($data['param'],true);
        //默认每页数量
        $limit=10;
        //创建ORM模型
        $order=new \App\Order();
        $orderinfo=new \App\Orderinfo();
        //判断用户行为
        switch ($param['state']) {
            //0为全部订单
            case 0:
                $sql='users_id ='.$data['guid'];
                break;
            //1为待付款
            case 1:
                $sql='users_id ='.$data['guid'].' and state ='.$param['state'];
                break;
            //2为待发货
            case 2:
                $sql='users_id ='.$data['guid'].' and state ='.$param['state'];
                break;
            //3为待收货
            case 3:
                $sql='users_id ='.$data['guid'].' and state ='.$param['state'];
                break;
            //4为退款
            case 4:
                $sql='users_id ='.$data['guid'].' and state in(4,5)';
                break;
            default:
                return response()->json(['serverTime'=>time(),'ServerNo'=>12,'ResultData'=>['Message'=>'用户行为异常']]);;
                break;
        }
        //定于查询数据
        $order_data=['order_id','order_num','sid','sname','state','created_at','total_price','name','phone','address','freight','invoice','customer'];
        $orderinfo_data=['goods_name','goods_num','goods_price','goods_type','img'];
        //查询该用户的订单数据
        $order_result=$order->quer($order_data,$sql,(($param['page']-1)*$limit),$limit);
        if($order_result['total'] == 0){
            return response()->json(['serverTime'=>time(),'ServerNo'=>0,'ResultData'=>$order_result]);
        }
        //最终结果
        $result=null;
        //查看该用户订单的详细数据精确到商品
        foreach ($order_result['list'] as $order_results) {
            //根据订单号查到该订单的详细数据
            $orderinfo_result=$orderinfo->quer($orderinfo_data,'order_num ='.$order_results['order_num'])->toArray();
            //将查询结果组成数组
            $order_results['goods']=$orderinfo_result;
            $result[]=$order_results;
            $order_results=null;
        }
        if(!empty($result)){
            return response()->json(['serverTime'=>time(),'ServerNo'=>0,'ResultData'=>['total'=>$order_result['total'],'list'=>$result]]);
        }else{
            response()->json(['serverTime'=>time(),'ServerNo'=>12,'ResultData'=>['Message'=>'订单查询失败']]);
        }
    }

    /*
    *   该方法提供订单操作
    */
    public function orderoperation(Request $request)
    {
        //获得app端传过来的json格式的数据转换成数组格式
        $data=$request::all();
        $param=json_decode($data['param'],true);
        //创建ORM模型
        $order=new \App\Order();
        //开启事务处理
        DB::beginTransaction();
        if($param['action'] == 8){
            //进行订单删除,web段的话需要确认订单状态
            $results=$order->orderdel($param['order_id']);
            if($results){
                //创建ORM模型
                $orderinfo=new \App\Orderinfo();
                $result=$orderinfo->orderinfodel($param['order_num']);
            }else{
                //假如失败就回滚
                DB::rollback();
                return response()->json(['serverTime'=>time(),'ServerNo'=>12,'ResultData'=>['Message'=>'删除订单失败']]);
            }
        }else{
            //进行订单操作
            $result=$order->orderupdate($param['order_id'],['state' => $param['action']]);
        }
        if($result){
            //假如成功就提交
            DB::commit();
            return response()->json(['serverTime'=>time(),'ServerNo'=>0,'ResultData'=>['Message'=>'操作成功']]);
        }else{
            //假如失败就回滚
            DB::rollback();
            return response()->json(['serverTime'=>time(),'ServerNo'=>12,'ResultData'=>['Message'=>'操作失败']]);
        }
    }
}
