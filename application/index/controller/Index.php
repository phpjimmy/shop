<?php
namespace app\index\controller;
use think\Controller;
use think\Request;
session_start();
class Index extends Controller{
    
     //ajax分页技术
    public function ajaxindex(){
        $model= new \app\index\model\User;
        $pagesize=1;  //每页显示的数据
        $page=empty($_GET['p'])?1:$_GET['p'];  //当前页
        //$count = db('user')->count();
        $count=$model->count();  //总数量
        //echo $count;
        $start=($page-1)*$pagesize; //开始位置  默认从0开始
        
        //$list = db('user')->limit($start,$pagesize)->select();
        $list = $model->limit($start,$pagesize)->select();
        //dump($list);
        
        $total = ceil($count/$pagesize);   //总页数
        
        $this->assign('page',$page);
        $this->assign('count',$count);
        $this->assign('list',$list);
        $this->assign('total',$total);
        
        //if (Request::instance()->isAjax()) echo "当前为 Ajax 请求";
        if(Request::instance()->isAjax()){
            
            //生成静态页面
            ob_start();
            require  APP_PATH.'index/view/index/ajaxindex.html';
            $html = ob_get_clean();  //解析之后的内容
            echo $html;
            
            //return $this-display('ajaxindex');
        } else {
            return $this->fetch(); 
        }
        
       
    }
    
    
    
}
