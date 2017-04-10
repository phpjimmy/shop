<?php
namespace app\home\controller;
use think\Controller;
use think\Session;
class User extends Controller{
    // 登录 login,doLogin
    // 注册 registe,doRegist
    // 修改密码 editPwd,doEditPwd
    
    // User: login/加载登录页面  dologin:登录处理  logout:注销 regist:注册 resetpwd:重置密码 
    
    //用户注册
    public function regist(){
       return $this->fetch();
    }
    
    //验证注册表单的用户名是否重复
    public function yanZheng(){
        $username = input('param.username');
        $data = db('user')->where('username',$username)->find();
        if($data){
            echo "false";  //此用户名存在
        } else {
            echo "true";
        }
    }
    
    
    //验证注册表单的验证码是否正确
    public function checkCode(){
        $data = input('param.');
        //dump($data);
        $res = $this->validate($data,[
            'verify|验证码'=>'require|captcha'
        ]);
        //$this->validate() 只有验证成功，才返回true
        if($res === true){
            echo "true";
        }else{
            echo "false";
        }
        
    }

    
    //验证注册信息
    public function doregist(){
       $info = input('param.');
      // dump($info);
       //die();
       
       $username = input('param.username');
       $password = input('param.password');
       $confirm_password = input('param.confirm_password');
       $checkcode = input('param.verify');
       //dump($username);
    
       
       //验证再次输入密码是否正确
       if($password !== $confirm_password){
          $this->error('注册失败：密码输入错误');
          return;
       }
      
       
       //TP5的内置验证验证码功能，添加 captcha 验证规则即可
//       $res = $this->validate($info,[
//            'verify|验证码'=>'require|captcha'
//       ]);
//       if(!$res){
//           $this->error('验证码错误');
//           return;
//       }
      
       
       //手动验证验证码
//       if(!captcha_check($checkcode)){
//           $this->error('注册失败：验证码错误');
//           return;
//       };
       
       if(empty($username)||empty($password)){
           $this->error('注册失败：账号或密码不能为空');
       }else{
            $regtime = date('Y-m-d H:i:s');
            $info = ['username'=>$username,'password'=>$password,'regtime'=>$regtime]; 
            $user = db('user')->insert($info);
            $this->success('注册成功','Index/index',3);
       }
       
       
    }
    
    
    //用户登录
    // 登录流程: 获取数据 -> 数据验证(模型,查询) -> 缓存 -> 跳转页面 
    public function login(){
       $last_username = Session::get('last_username');
       $this->assign('last_username', $last_username);
       $src = input('param.src');
       $this->assign('login_src',$src);
       //dump('从SRC='.$src.'过来登录');
       
       $referurl = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "Home/User/user";
       $this->assign('referurl',$referurl);
       return $this->fetch();
    }
    
    
    //验证登录信息
    public function dologin(){
       $username = input('param.username');
       $password = input('param.password');
       $checkcode = input('param.verify');
       $referurl = input('param.referurl');
       //dump($username);
       
       // 登录源:  0:默认登录 1:商品列表 2:商品详情  3:点击购物车列表  4:订单列表 5:点击用户中心
       $src = input('param.src');

       //TP5的内置验证功能，添加 captcha 验证规则即可
//       $res = $this->validate($checkcode,[
//            'captcha|验证码'=>'require|captcha'
//       ]);
//       if(!$res){
//           $this->error('验证码错误');
//           return;
//       }
       
       //手动验证
       if(!captcha_check($checkcode)){
           $this->error('登录失败：验证码错误');
           return;
       };
     
       if(empty($username)||empty($password)){
            $this->error('登录失败：账号或密码不能为空');
       }else{
           $userinfo = db('user')->where('username',$username)->where('password',$password)->find();
           //dump($userinfo);
           //die();
           if($userinfo){
               // 缓存登录用户名  (成功:跳转,缓存用户信息) (失败:跳转,提示)
	     Session::set('last_username', $username);
             Session::set('userid', $userinfo['userid']);
             Session::set('userinfo',$userinfo);
              
             $this->success('登录成功',$referurl,3);
              
             // 登录源:  0:默认登录 1:商品列表 2:商品详情  3:点击购物车列表  4:订单列表 5:点击用户中心
//              switch ($src) {
//		    case 0:
//			$this->success('登录成功', 'Index/index', 3);
//			break;
//		    case 1:
//			$this->success('登录成功', 'Goods/category', 3);
//			break;
//		    case 2:
//                        $this->success('登录成功', 'Goods/goods', 3);
//			break;
//		    case 3:
//                        $this->success('登录成功', 'Cart/flow1', 3);
//			break;
//		    case 4:
//		        $this->success('登录成功', 'Order/order', 3);
//			break;
//		    case 5:
//                        $this->success('登录成功', 'User/user', 3);
//			break;
//		    default:
//                        $this->success('登录成功', 'User/login', 3);
//			break;
//		}
              
           }else{
              $this->error('登录失败：账号或密码错误');
           }
       }
             
    }
    
    public function logout(){
        Session::delete('$userinfo');
        Session::clear();
        $this->success("退出成功",'home/User/login');
    }
    
    //用户中心
    public function user(){
       return $this->fetch();
    }
    
    
}
