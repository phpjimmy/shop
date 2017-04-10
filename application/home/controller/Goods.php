<?php
namespace app\home\controller;
use think\Controller;
class Goods extends Controller{
    
    //商品列表页
    public function category(){
       $goods = db('goods')->select();   //二维数组
       $this->assign('goods',$goods);
       
       return $this->fetch();
    }
    
    //商品详情页
    public function goods(){
        $goods_id = input('param.goods_id');
        
	$ginfo = db('goods')->find($goods_id);
        
	// 详情数据设定
	$this->assign('ginfo', $ginfo);
	// 渲染
        // 跨域问题
       //header("Access-Control_Allow-Origin:*");
        //echo $_GET['callback']."(".json_encode($html);
        return $this->fetch();
    }
     
    
}
