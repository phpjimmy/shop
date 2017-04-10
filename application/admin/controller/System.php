<?php
namespace app\admin\controller;

class System extends Base{
   
    //系统设置
     public function SystemBase(){
       return $this->fetch();
    }
    
    //栏目管理
    public function SystemCategory(){
       return $this->fetch();
    }
    
    //数据字典
    public function SystemData(){
       return $this->fetch();
    }
    
    //屏蔽词
    public function SystemShielding(){
       return $this->fetch();
    }
    
    //系统日志
    public function SystemLog(){
       return $this->fetch();
    }
    
    //添加栏目
    public function SystemCategoryAdd(){
       return $this->fetch();
    }
    
}
