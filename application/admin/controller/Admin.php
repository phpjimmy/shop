<?php
namespace app\admin\controller;

use think\Session;
class Admin extends Base{
    

    //验证注册表单的用户名是否重复
    public function yanZheng(){
        $loginid = input('param.loginid');
        $data = db('admin')->where('loginid',$loginid)->find();
        if($data){
            echo "false";  //此用户名存在
        } else {
            echo "true";
        }
        
    }
    
    //后台管理员登录
    public function login(){
       $last_admin_id = Session::get('last_admin_id');
       if($last_admin_id && $last_admin_id>0){
           $this->error("您已登录",'admin/Index/index');
       }
        
       if($_POST){
           $loginid = input('param.loginid');
           $password = input('param.password');
           $checkcode = input('param.verify');
           //dump($username);

           //手动验证验证码
           if(!captcha_check($checkcode)){
               $this->error('登录失败：验证码错误');
               return;
           };

           if(empty($loginid)||empty($password)){
                $this->error('登录失败：账号或密码不能为空');
           }else{
               $userinfo = db('admin')->where('loginid',$loginid)->where('password',$password)->find();
               //dump($userinfo);
               //die();
               if($userinfo){
                 // 缓存登录用户名  (成功:跳转,缓存用户信息) (失败:跳转,提示)
                 Session::set('last_admin_id', $userinfo['admin_id']);
                 Session::set('last_loginid', $loginid);
                 Session::set('userinfo', $userinfo);

                 $this->success('登录成功','Index/index',3);
               }else{
                 $this->error('登录失败：账号或密码错误');
               }
           }
       }
       
      
       return $this->fetch();
       
    }
    
    //后台管理员退出
    public function logout(){
        Session::delete('$userinfo');
        Session::clear();
        $this->redirect('admin/Admin/login');
        //$this->success("退出成功",'admin/Admin/login');
    }
   
    
    
    //角色管理--列表
    public function adminRole(){
       $count = db('role')->count();
       $rlist = db('role')->select();
       
       $this->assign('rlist',$rlist);
       $this->assign('count',$count);
       return $this->fetch();
    }
    
    
    //角色添加
    public function AdminRoleAdd(){
       $data = input('param.');
       
       
       if(!empty($data)){
           $ids = $data['role_auth_ids'];
             $data['role_auth_ca']='';
           for($i=0;$i<count($ids);$i++){
               $info = db('auth')->find($ids[$i]);
               if(($info['auth_pid'])!==0){
                   $data['role_auth_ca'] .= $info['auth_c'].'-'.$info['auth_a'].',';     
               }
           }
           $data['role_auth_ca']= rtrim($data['role_auth_ca'],',');
           $data['role_auth_ids'] = implode(',', $data['role_auth_ids']);
           
           $res = db('role')->insert($data);
           
           if($res){
               $this->success('角色添加成功');
           }
       } 
       $auth1 = db('auth')->where('auth_pid',0)->select();
       $auth2 = db('auth')->where('auth_pid != 0')->select();
       
       $this->assign('auth1',$auth1);
       $this->assign('auth2',$auth2);
       return $this->fetch();
    }
    
    //角色修改
    public function AdminRoleModify(){
       $roleid = input('param.roleid');
       $rlist = db('role')->find($roleid);
       $id = $rlist['role_auth_ids'];
       $id = explode(',', $id);
       
       $data = input('post.');
       if(!empty($data)){
           $ids = $data['role_auth_ids'];
             $data['role_auth_ca']='';
           for($i=0;$i<count($ids);$i++){
               $info = db('auth')->find($ids[$i]);
               if(($info['auth_pid'])!==0){
                   $data['role_auth_ca'] .= $info['auth_c'].'-'.$info['auth_a'].',';     
               }
           }
           $data['role_auth_ca']= rtrim($data['role_auth_ca'],',');
           $data['role_auth_ids'] = implode(',', $data['role_auth_ids']);
           
           $res = db('role')->update($data);
           
           if($res){
               $this->success('角色修改成功');
           }
       } 
       $auth1 = db('auth')->where('auth_pid',0)->select();
       $auth2 = db('auth')->where('auth_pid != 0')->select();
       
       $this->assign('id',$id);
       $this->assign('rlist',$rlist);
       $this->assign('auth1',$auth1);
       $this->assign('auth2',$auth2);
       return $this->fetch();
       
    }

    //角色删除
    public function AdminRoleDelete(){
        $roleid = input('param.roleid');
       
        $res = db('role')->delete($roleid);
        if($res){
            $this->success('角色删除成功');
        }
        
    }


    
    
    //权限(节点)添加
    public function AdminPermissionAdd(){
       $data = input('param.');
       if(!empty($data)){
           $res = db('auth')->insert($data);
           if($res){
               $this->success('添加权限成功');
           }else{
               $this->error('添加权限失败');
           }
       }
       
       $auth = db('auth')->where('auth_pid',0)->select();
       
       $this->assign('auth',$auth);
       return $this->fetch();
    }
    
    
    //权限管理--列表
    public function AdminPermission(){
        
       $count = db('auth')->count();
       
       $keyword = input('post.keywords');
       $keyword = trim($keyword);
       if(!empty($keyword)){
            $auth1 = db('auth')->where('auth_pid',0)
                              ->where('auth_name','like',"%{$keyword}%")
                              ->select();
            $auth2 = db('auth')->where('auth_pid != 0')
                              ->where('auth_name','like',"%{$keyword}%")
                              ->select();                  
       }else{
            $auth1 = db('auth')->where('auth_pid',0)->select();
            $auth2 = db('auth')->where('auth_pid != 0')->select();
       }
       
       $this->assign('auth1',$auth1);
       $this->assign('auth2',$auth2);
       $this->assign('count',$count);
       return $this->fetch();
       
    }
    
   
    //权限删除
    public function AdminPermissionDelete(){
        $authid = input('param.authid');
       
        $res = db('auth')->delete($authid);
        if($res){
            $this->success('权限删除成功');
        }
   }

   //权限修改
    public function AdminPermissionModify(){
        $authid = input('param.authid');
        
        $ainfo = db('auth')->find($authid);
        $auth1 = db('auth')->where('auth_pid',0)->select();
        
        $this->assign('ainfo',$ainfo);
        $this->assign('auth1',$auth1);
        return $this->fetch();
    }
    
    public function AdminPermissionDoModify(){
        $authid = input('param.authid');
        $modifydata = input('param.');
       
        $res = db('auth')->where('auth_id',$authid)->update($modifydata);
              
        if($res){
            $this->success('修改成功');
        }else{
            $this->error('修改失败:'.$modifydata->getError());    
        }
        
    }
    
   //权限搜索
    public function AdminPermissionSearch(){
       $count = db('auth')->count();
         
       $keyword = input('post.keywords');
       $keyword = trim($keyword);

       
       $alist = db('auth')->where('auth_name','like',"%{$keyword}%")
                          ->select();
    
       $this->assign('count',$count);
       $this->assign('alist',$alist);
       return $this->fetch('adminpermission');
   }
    

   
   
   //管理员列表
    public function AdminList(){
       $count = db('admin')->count();
     
       
       $alist = db('admin')->alias('a')
                           ->join('t_role r','r.role_id=a.roleid')
                           ->order('admin_id')
                           ->select();
        //dump($alist);
       // die();
    
       $this->assign('count',$count);
       $this->assign('alist',$alist);
       return $this->fetch();
    }
    
    public function changeStatus1(){
        $adminid = input('param.adminid');
        if(!empty($adminid)){
            $data = db('admin')->where('admin_id',$adminid)->update(['status'=>1]);
            $this->redirect('Admin/AdminList');
        }
    }
    
    public function changeStatus0(){
        $adminid = input('param.adminid');
        if(!empty($adminid)){
            $data = db('admin')->where('admin_id',$adminid)->update(['status'=>0]);
            $this->redirect('Admin/AdminList');
        }
    }
    
    //管理员添加
    public function adminAdd(){
       
       $admins = input('param.');
       //dump($admins);
      
       if(!empty($admins)){
            $loginid = $admins['loginid'];
            $data = db('admin')->where('loginid',$loginid)->find();
            if($data){
                  $this->error('此用户名已存在');  
            } else {
                 
                   $res = db('admin')->insert($admins);
                   if($res){
                       $this->success('添加管理员成功');
                   }else{
                      $this->error('添加管理员失败');
                   }
            }
        }else{
            $role = db('role')->select();
            $this->assign('role',$role);
            return $this->fetch();
        }
      
    }
    
    //管理员修改
    public function adminModify(){
        $adminid = input('param.adminid');
        
        $ainfo = db('admin')->find($adminid);
        
        $this->assign('ainfo',$ainfo);
        $role = db('role')->select();
        $this->assign('role',$role);
        return $this->fetch();
    }
    
    public function adminDoModify(){
        $id = input('param.admin_id');
        $modifydata = input('param.');
       
        $res = db('admin')->where('admin_id',$id)->update($modifydata);
              
        if($res){
            $this->success('修改成功');
        }else{
            $this->error('修改失败:'.$modifydata->getError());    
        }
        
    }
    
     //管理员删除
    public function adminDelete(){
        $adminid = input('param.adminid');
        $res = db('admin')->delete($adminid);
	
        if($res){
            $this->success('删除成功', 'Admin/AdminList');
        } else {
            $this->error('删除失败:'.$adminid->getError());    
        }
    }
    
     //管理员搜索
    public function adminSearch(){
       $count = db('admin')->count();
         
       $keyword = input('post.keywords');
       $keyword = trim($keyword);

       $alist = db('admin')->alias('a')
                           ->join('t_role r','r.role_id=a.roleid')
                           ->where('loginid','like',"%{$keyword}%")
                           ->select();
    
        $this->assign('count',$count);
        $this->assign('alist',$alist);
        return $this->fetch('adminlist');
   }
    
    
   
   
}
