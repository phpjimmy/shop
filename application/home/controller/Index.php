<?php
namespace app\home\controller;
use think\Session;
use think\Controller;
class Index extends Controller{
    
    //前台首页
    public function index(){
       $goods = db('goods')->select();   //二维数组
       $name = Session::get('last_username')?Session::get('last_username'):'登录';
       
       $this->assign('goods',$goods);
       $this->assign('name',$name);
       return $this->fetch();
    }
    
    
    
    
}
