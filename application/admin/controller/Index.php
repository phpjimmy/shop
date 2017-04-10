<?php
namespace app\admin\controller;

use think\Session;
class Index extends Base{
    
    //后台首页
    public function index(){
        
       $last_loginid = Session::get('last_loginid');
       $ainfo = db('admin')->where('loginid',$last_loginid)->find();
       $roleid = $ainfo['roleid'];
       $rinfo = db('role')->where('role_id',$roleid)->find();
       $role_name = $rinfo['role_name'];
       
       $this->assign('role_name',$role_name);
       $this->assign('last_loginid',$last_loginid);
       
       
       return $this->fetch();
       
    }
    
    
    
    //后台欢迎页
    public function welcome(){
        
       $last_admin_id = Session::get('last_admin_id');
       $ainfo = db('admin')->where('admin_id',$last_admin_id)->find();
       $regtime = $ainfo['regtime'];
       $this->assign('regtime',$regtime);
       return $this->fetch();
       
    }
    
    
    //添加资讯
    public function ArticleAdd(){
        return $this->fetch();
    }
    
    
}
