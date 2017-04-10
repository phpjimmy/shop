<?php
namespace app\admin\controller;


class Picture extends Base{
    
    //图片列表
    public function PictureList(){
       return $this->fetch();
    }
    
    //图片展示
    public function PictureShow(){
       return $this->fetch();
    }
    
    //图片添加
    public function PictureAdd(){
       return $this->fetch();
    }
    
}
