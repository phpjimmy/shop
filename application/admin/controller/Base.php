<?php
namespace app\admin\controller;
use think\Controller;
use think\Session;
use think\Request;
class Base extends Controller{
    
    /*
     * 1、后台用户登录，先判断当前用户的角色，在根据角色查询对应的功能  $url[]=array()
     * 2、登录成功之后，要进行功能判断（获取当前操作的模块名称/控制器名称/方法名称，和角色拥有的功能url做对比） 
     *  if(!in_array(模块名称/控制器名称/方法名称,$url)){
     *     alert(没有权限访问)；
     *  }
     */
    public function __construct() {
        parent::__construct();
        
        $last_admin_id = Session::get('last_admin_id');
        $last_loginid = Session::get('last_loginid');
        
        $str =Request::instance()->controller().'-'. Request::instance()->action();   //控制器名称-方法名称
        $auth_path = ['Admin-login','Admin-logout'];
        if(empty($last_loginid) && !in_array($str, $auth_path)){
            $this->redirect('admin/Admin/login');
        }
        $ainfo = db('admin')->where('status',0)->find($last_admin_id);
        $roleid = $ainfo['roleid'];
        $rinfo = db('role')->where('role_id',$roleid)->find();
        // dump($rinfo);
        $role_auth_ca = $rinfo['role_auth_ca'];
        //dump($role_auth_ca);
        $url = explode(',', $role_auth_ca);
        
        $public = ['Admin-login','Admin-logout','Index-index','Index-welcome'];
        //dump($publich);
         
        if(!in_array($str,$url) && $last_admin_id !== 1 && !in_array($str,$public)){
            $this->error('对不起！您没有访问权限','admin/Index/welcome');
        }
       
        
    }
    
    
    
    
    
}
