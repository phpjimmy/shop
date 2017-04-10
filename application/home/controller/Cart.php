<?php
namespace app\home\controller;
use think\Controller;
use think\Session;
class Cart extends Controller{
    
    //添加购物车
    public function FlowAdd(){
         // 加入购物车 创建模型 数据处理 结果返回
        //查购物车里有没有这个商品 有这个商品,更新数据 没有添加
        $cinfo = input('param.');
        if($cinfo){
             $userid = Session::get('userid');
             $goodsid = input('param.goodsid');
             $cart = db('cart')->where("goodsid='{$goodsid}'")->where('userid',$userid)->where('status',0)->select(); // 注意: 查询结果是二维数组 
            
             if(is_array($cart) && count($cart)>0){
                   $cart = $cart[0];
                   $cart['goodsnum'] += input('param.goodsnum');
                   $cart['goodstotalprice'] = $cart['goodsnum'] * $cart['goodsprice'];
                   $res = db('cart')->update($cart);
                   if($res){
                        $this->success('更新购物车成功','home/Goods/category');
                    } else{
                        $this->error('更新购物车失败');
                    }

              }else{
                    $ginfo = db('goods')->find($goodsid);

                    $cinfo['userid'] = Session::get('userid');

                    $cinfo['goodsname'] = $ginfo['goods_name'];
                    $cinfo['goodsprice'] = $ginfo['goods_price'];
                    $cinfo['goodsimg'] = $ginfo['goods_big_img'];
                    $cinfo['goodstotalprice'] = input('param.goodsnum') * $ginfo['goods_price'];
                    $res = db('cart')->insert($cinfo);
                    if($res){
                        $this->success('添加购物车成功','home/Goods/category');
                    } else{
                        $this->error('添加购物车失败');
                    }
               }
            
        }
        
    }

  
    //购物车列表
    public function flow1(){
        $userid = Session::get('userid');
        $cart = db('cart')->where('userid',$userid)->where('status',0)->select();
        $allprice = db('cart')->where('userid',$userid)->where('status',0)->sum('goodstotalprice');
        
        $this->assign('allprice',$allprice);
        $this->assign('cart',$cart);
        return $this->fetch();
    }
    
    //填写和核对订单信息
    public function flow2(){
        
        if(!Session::get('userid')){
            $this->error('您未登录','home/User/login');
        }
        
        $userid = Session::get('userid');
        
       
        $cinfo = db('cart')->alias('c')
                           ->join('t_goods g','c.goodsid=g.goods_id')
                           ->where('userid',$userid)
                           ->where('status',0)
                           ->field('c.*,g.goods_number')
                           ->select();
//                   dump($cinfo);
//                   die();
        $allprice = db('cart')->where('userid',$userid)->where('status',0)->sum('goodstotalprice');
        $allnum = db('cart')->where('userid',$userid)->where('status',0)->sum('goodsnum');
        
        $this->assign('allprice',$allprice);
        $this->assign('allnum',$allnum);
        
        $this->assign('cinfo',$cinfo);
        return $this->fetch();
    }
    
    
    //提交订单
    public function submitAll(){
        $all = input('param.');
        $goods_number = $all['goods_number'];  //商品库存
        $goodsnum = $all['goodsnum'];    //购买数量
        $goodsid = $all['goodsid'];   //商品id
        
        db()->startTrans();
        
        $gUpdateSuccess = true;
        for ($i = 0; $i < count($goodsid); $i++) {
            // 取出 相关数量信息
            $currgoods_number = $goods_number[$i];
            $currgoodsnum = $goodsnum[$i];
           // 更新商品 相关的数量  => 库存和销售数量
            $number = $currgoods_number-$currgoodsnum;
            $id = $goodsid[$i];
            $res = db('goods')->update(['goods_number'=>$number,'goods_id'=>$id]);
            
            if (!$res) {
		$gUpdateSuccess = false;
		break;
	    }
        }
        
        // 如果商品全部更新成功 -> 继续 订单和购物车的更新操作
	if ($gUpdateSuccess) {
	    // 插入订单 
            $userid = Session::get('userid');
            $cinfo = db('cart')->where('userid',$userid)
                               ->where('status',0)
                               ->sum('goodstotalprice');
            //dump($cinfo);
            
            $c = db('cart')->where('userid',$userid)->where('status',0)->select();
            $c = $c[0];
            $goodsimg = $c['goodsimg'];
            //dump($c);
            //die();
            
            $oinfo['userid'] = $userid;
            $oinfo['ordermoney'] = $cinfo;
            $oinfo['goodsimg'] = $goodsimg;
            $res = db('order')->insertGetId($oinfo);
            
             // 如果插入订单成功, 更新购物车记录
	    if ($res > 0) {
                $orderid = db('cart')->where('userid',$userid)
                                     ->where('status',0)
                                     ->update(['orderid'=>$res,'status'=>1]);
                //   dump($orderid);die;
                if ($orderid) {
		    // 跳转界面.提示
		    db()->commit();
                    
		    $this->success('订单提交成功', 'home/Order/order');
		    return;
		}
            }
            
            db()->rollback();
	    $this->error('提交订单失败');
            
        }
    
    }


    //提交订单成功
    public function flow3(){
        return $this->fetch();
    }
    
    
    //删除购物车
    public function CartDelete(){
        $id = input('param.id');
        $res = db('cart')->delete($id);
	
        if($res){
            $this->success('删除成功', 'Cart/flow1');
        } else {
            $this->error('删除失败:'.$id->getError());    
        }
    }
    
    //添加购物车的商品数量
    public function CartAddnum(){
        $id = input('param.id');
        
        $cart = db('cart')->find($id);
        $cart['goodsnum'] +=1; 
        $cart['goodstotalprice'] = $cart['goodsnum'] * $cart['goodsprice'];
        $res = db('cart')->update($cart);
        if($res){
            //echo '添加数量成功';
           $this->redirect('Cart/flow1');
        } else {
            $this->error();
        }
        
    }
    
     //减少购物车的商品数量
    public function CartReducenum(){
        $id = input('param.id');
        
        $cart = db('cart')->find($id);
        $cart['goodsnum'] -=1; 
        $cart['goodstotalprice'] = $cart['goodsnum'] * $cart['goodsprice'];
        $res = db('cart')->update($cart);
        if($res){
            //echo '减少数量成功';
           $this->redirect('Cart/flow1');
        } else {
            $this->error();
        }
        
    }
    
    
    
}
