<?php
namespace app\home\controller;
use think\Controller;
use think\Session;
class Order extends Controller{
   
    //订单信息--列表
    public function order(){
        if(!Session::get('userid')){
            $this->error('您未登录','home/User/login');
        }
        
        $userid = Session::get('userid');
        $oinfo = db('order')->where('userid',$userid)->where('status',0)->select();
        //dump($oinfo);
        //die();
        $this->assign('oinfo',$oinfo);
        return $this->fetch();
    }
    
    //订单详情
    public function orderInfo(){
        $id = input('param.id');
        $oinfo = db('order')->find($id);
        
        $this->assign('oinfo',$oinfo);
        return $this->fetch();
        
    }
    
    //订单删除  --逻辑删除
    public function orderDelete(){
        $id = input('param.id');
        $userid = Session::get('userid');
        $o = db('order')->where('userid',$userid)->where('id',$id)->where('status',0)->update(['status'=>1]);
        if($o){
            $this->success('删除成功','Order/order');
        }else{
            $this->error('删除失败');
        }
       
    }

   
    //订单地址信息
    public function address(){
        return $this->fetch();
    }
    
    
    public function pay(){
      // 订单信息  商品信息  价格 => 需要在页面显示,需要在支付时候用 就查询 
        $orderid = input('param.orderid');
       
        $info = db('order')->alias('o')
                           ->join('t_cart c','c.orderid=o.id')
                           ->where('o.id',$orderid)
                           ->where('o.status',0)
                           ->field('c.goodsname,c.goodsnum,c.goodsprice,o.ordermoney,o.note,o.addr,o.id')
                           ->select();
          
        
        $this->assign('info',$info[0]);
        return $this->fetch();
    }
    
    public function dopay(){
        $orderid = input('param.orderid');
        $o = db('order')->where('id',$orderid)->where('status',0)->update(['orderstatus'=>1]);
        if($o){
            $this->success('支付成功','Order/order');
        } else {
            $this->error('支付失败');
        }
        
    }
    
    
    public function remind() {
        $orderid = input('param.orderid');
        //推送 =>实现提醒
        //插入一条推送消息
        //$re = db('shop')->select();
        //if (!$re) {
         //   $this->error('提醒商家发货失败!');
        //}

        $o = db('order')->where('id',$orderid)->where('status',0)->update(['orderstatus'=>2]);
        if($o){
            $this->success('提醒商家发货成功!','Order/order');
        } else {
            $this->error('提醒商家发货失败');
        }
    }
    
    
    public function qianshou(){
        $orderid = input('param.orderid');
       
        $info = db('order')->alias('o')
                           ->join('t_cart c','c.orderid=o.id')
                           ->where('o.id',$orderid)
                           ->where('o.status',0)
                           ->field('c.goodsname,c.goodsnum,c.goodsprice,o.ordermoney,o.note,o.addr,o.id')
                           ->select();
          
        
        $this->assign('info',$info[0]);
        return $this->fetch();
    }
    
    
    public function doqianshou(){
        $orderid = input('param.orderid');
        $o = db('order')->where('id',$orderid)->where('status',0)->update(['orderstatus'=>3]);
        if($o){
            $this->success('收货成功,请对本次交易做出评价','Order/order');
        } else {
            $this->error('收货失败');
        }
        
    }
    
     public function comment() {
       // 插入评论表数据, 更新订单记录(orderstaus,commentflag)
        
        $orderid = input('param.orderid');
        $o = db('order')->where('id',$orderid)->where('status',0)->select();
                 
        $this->assign('info', $o[0]);
       // return $this->fetch();
    }
    
    
    
    
}
