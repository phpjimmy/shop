<?php
namespace app\admin\controller;

class Article extends Base{
   
    //资讯管理
    public function ArticleList(){
        return $this->fetch();
    }
    
    //添加资讯
    public function ArticleAdd(){
        return $this->fetch();
    }
    
    
}
