<?php
namespace app\admin\controller;


class Product extends Base{
    
    //品牌管理
    public function ProductBrand(){
       return $this->fetch();
    }
    
    //分类管理
    public function ProductCategory(){
       return $this->fetch();
    }
    
    //分类添加
    public function ProductCategoryAdd(){
       return $this->fetch();
    }
    
    
    public function Codeing(){
       return $this->fetch();
    }
    
    
    //产品管理(列表)
    public function ProductList(){
      
       $count = db('goods')->count();
     
       $ginfo = db('goods')->select();
       //dump(ginfo);
       
       $this->assign('count',$count);
       $this->assign('ginfo',$ginfo);
       
       return $this->fetch();
    }
    
    public function changeStatus1(){
        $goodsid = input('param.goodsid');
        if(!empty($goodsid)){
            $data = db('goods')->where('goods_id',$goodsid)->update(['goods_status'=>1]);
            $this->redirect('Product/ProductList');
        }
    }
    
    public function changeStatus0(){
        $goodsid = input('param.goodsid');
        if(!empty($goodsid)){
            $data = db('goods')->where('goods_id',$goodsid)->update(['goods_status'=>0]);
            $this->redirect('Product/ProductList');
        }
    }
    
    //产品详情
    public function ProductInfo(){
        $goods_id = input('param.goods_id');
        //dump($goods_id);
        
	$ginfo = db('goods')->find($goods_id);
       
	// 详情数据设定
	$this->assign('ginfo', $ginfo);
	// 渲染
	return $this->fetch();
    }

    //产品添加
    public function ProductAdd(){
       $goods = input('post.');
       //dump($goods);
       if(!empty($goods)){
          //上传图片
            // 获取表单上传文件 例如上传了001.jpg
            $file = request()->file('image');
            // 移动到框架应用根目录/public/uploads/ 目录下
            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
            if($info){
                // 成功上传后 获取上传信息
                // 输出 jpg
                //echo $info->getExtension();
                // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
                $img = $info->getSaveName();
                $goods['goods_big_img'] = '/uploads/'.$img;
                // 输出 42a79759f284b767dfcb2a0197904287.jpg
                //echo $info->getFilename();
                //die();
            }else{
                // 上传失败获取错误信息
                echo $file->getError();
                die();
            }

           unset($goods['file']);
           $res = db('goods')->insert($goods);
           if($res){
               $this->success('添加商品成功','Product/ProductList');
           }
       }
       
       return $this->fetch();
    }
    
    
    //产品删除
    public function ProductDelete(){
        $goods_id = input('param.goods_id');
        $res = db('goods')->delete($goods_id);
	
        if($res){
            $this->success('删除成功', 'Product/ProductList');
        } else {
            $this->error('删除失败:'.$goods_id->getError());    
        }
    }
   
   
   //产品修改
    public function ProductModify(){
       $goods_id = input('param.goods_id');
       $ginfo = db('goods')->find($goods_id);
       
       $this->assign('ginfo',$ginfo);
       return $this->fetch();
       
    }
    
    public function ProductDoModify(){
        $goods_id = input('param.goods_id');
        $modifydata = input('param.');
        $res = db('goods')->where('goods_id',$goods_id)->update($modifydata);
              
        if($res){
            $this->success('修改成功', 'Product/ProductList');
        } else {
            $this->error('修改失败:'.$modifydata->getError());    
        }
        
    }
    
    //产品搜索
    public function ProductSearch(){
        $count = db('goods')->count();
         
        $keyword = input('post.keywords');
        $keyword = trim($keyword);

        $ginfo = db('goods')->where('goods_name','like',"%{$keyword}%")
                            ->select();
        
        $this->assign('count',$count);
        $this->assign('ginfo',$ginfo);
        return $this->fetch('productlist');
   }
   
   
    
    
    
}
