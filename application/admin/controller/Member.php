<?php
namespace app\admin\controller;


class Member extends Base{
    
    //会员列表
    public function MemberList(){
       return $this->fetch();
    }
    
    //删除会员
    public function MemberDel(){
       return $this->fetch();
    }
    
    //等级管理
    public function MemberLevel(){
       return $this->fetch();
    }
    
    //积分管理
    public function MemberScoreoperation(){
       return $this->fetch();
    }
    
    //浏览记录
    public function MemberRecordBrowse(){
       return $this->fetch();
    }
    
    //下载记录
    public function MemberRecordDownload(){
       return $this->fetch();
    }
    
    //分享记录
    public function MemberRecordShare(){
       return $this->fetch();
    }
    
    
    //添加会员
    public function MemberAdd(){
       return $this->fetch();
    }
    
    
    //会员详情
    public function MemberShow(){
       return $this->fetch();
    }
    
    //修改密码
    public function ChangePassword(){
       return $this->fetch();
    }
    
    
   
    
}
