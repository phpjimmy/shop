<?php
namespace app\index\controller;
use think\Loader;
class Api extends \think\Controller{
    
    // error_reporting(E_ERROR);
    public function wxpay(){
       
            //ini_set('date.timezone','Asia/Shanghai');
            //require_once "vendor/Wxpay/lib/WxPay.Api.php";
            //require_once "vendor/Wxpay/WxPay.NativePay.php";
            //require_once 'vendor/Wxpay/log.php';
            Loader::import('wxpay.lib.WxPay',"",".Api.php");
            Loader::import('wxpay.example.WxPay',"",".NativePay.php");
            $notify = new \NativePay();
            $input = new \WxPayUnifiedOrder();
            $input->SetBody("test");
            $input->SetAttach("test");
            $input->SetOut_trade_no(date("YmdHis"));  ////SetOut_trade_no(订单号)
            $input->SetTotal_fee("1");//0.01   100元=100*100  订单总金额 单位是分 一定要乘以100
            $input->SetTime_start(date("YmdHis"));
            $input->SetTime_expire(date("YmdHis", time() + 600));
            $input->SetGoods_tag("test");//
            $input->SetNotify_url("shop.io/index/api/notify"); //回到当前文件自己方法的路径 自己的url 
            $input->SetTrade_type("NATIVE");
            $input->SetProduct_id("123456789");
            $result = $notify->GetPayUrl($input);
            $url2 = $result["code_url"];

            $a = '<div style="margin-left: 10px;color:#556B2F;font-size:30px;font-weight: bolder;">扫描支付模式二</div><br/>
                    <img alt="模式二扫码支付" src="http://paysdk.weixin.qq.com/example/qrcode.php?data='.urlencode($url2).'" style="width:150px;height:150px;"/>';
            $this->assign('a',$a);
            return $this->fetch();
            
    }
   
   //回调
//   public function notify(){
//     Loader::import('wxpay.example.notify');  
//     //Log::DEBUG("begin notify");
//    $notify = new \PayNotifyCallBack();
//    $notify->Handle(false);
//   }
    
    
     /**
     * 生成支付代码
     * @param   array   $order      订单信息
     * @param   array   $config_value    支付方式信息
     */
    public function alipay(){
        //require_once("alipay.config.php");
        //require_once("lib/alipay_submit.class.php");
        Loader::import('alipay.alipay.config',"",".php");
        Loader::import('alipay.lib.alipay_submit',"",".class.php");
         
        /**************************请求参数**************************/

        //支付类型
        $payment_type = "1";
        //必填，不能修改
        //服务器异步通知页面路径
        $notify_url = "shop.io/alipay/notify_url.php";
        //需http://格式的完整路径，不能加?id=123这类自定义参数

        //页面跳转同步通知页面路径
        $return_url = "shop.io/alipay/return_url.php";
        //需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/

        //商户订单号
        $out_trade_no = $_POST['WIDout_trade_no'];
        //商户网站订单系统中唯一订单号，必填

        //订单名称
        $subject = $_POST['WIDsubject'];
        //必填

        //付款金额
        $price = $_POST['WIDprice'];
        //必填

        //商品数量
        $quantity = "1";
        //必填，建议默认为1，不改变值，把一次交易看成是一次下订单而非购买一件商品
        //物流费用
        $logistics_fee = "0.00";
        //必填，即运费
        //物流类型
        $logistics_type = "EXPRESS";
        //必填，三个值可选：EXPRESS（快递）、POST（平邮）、EMS（EMS）
        //物流支付方式
        $logistics_payment = "SELLER_PAY";
        //必填，两个值可选：SELLER_PAY（卖家承担运费）、BUYER_PAY（买家承担运费）
        //订单描述

        $body = $_POST['WIDbody'];
        //商品展示地址
        $show_url = $_POST['WIDshow_url'];
        //需以http://开头的完整路径，如：http://www.商户网站.com/myorder.html

        //收货人姓名
        $receive_name = $_POST['WIDreceive_name'];
        //如：张三

        //收货人地址
        $receive_address = $_POST['WIDreceive_address'];
        //如：XX省XXX市XXX区XXX路XXX小区XXX栋XXX单元XXX号

        //收货人邮编
        $receive_zip = $_POST['WIDreceive_zip'];
        //如：123456

        //收货人电话号码
        $receive_phone = $_POST['WIDreceive_phone'];
        //如：0571-88158090

        //收货人手机号码
        $receive_mobile = $_POST['WIDreceive_mobile'];
        //如：13312341234
        
        
        //构造要请求的参数数组，无需改动
        $parameter = array(
                    "service" => "create_partner_trade_by_buyer",
                    "partner" => trim($alipay_config['partner']),
                    "seller_email" => trim($alipay_config['seller_email']),
                    "payment_type"	=> $payment_type,
                    "notify_url"	=> $notify_url,
                    "return_url"	=> $return_url,
                    "out_trade_no"	=> $out_trade_no,
                    "subject"	=> $subject,
                    "price"	=> $price,
                    "quantity"	=> $quantity,
                    "logistics_fee"	=> $logistics_fee,
                    "logistics_type"	=> $logistics_type,
                    "logistics_payment"	=> $logistics_payment,
                    "body"	=> $body,
                    "show_url"	=> $show_url,
                    "receive_name"	=> $receive_name,
                    "receive_address"	=> $receive_address,
                    "receive_zip"	=> $receive_zip,
                    "receive_phone"	=> $receive_phone,
                    "receive_mobile"	=> $receive_mobile,
                    "_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
                );
             //建立请求
            $alipaySubmit = new AlipaySubmit($alipay_config);
            $html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
            
            $this->assign('a',$html_text);
            return $this->fetch();       
  
    
    }
    
    
    
}
