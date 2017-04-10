<?php
namespace app\admin\controller;


class Comments extends Base{
   
    //意见反馈
    public function FeedbackList(){
       return $this->fetch();
    }
    
    //会员详情
    public function MemberShow(){
       return $this->fetch();
    }
    
    //添加会员
    public function MemberAdd(){
       return $this->fetch();
    }
    
}
