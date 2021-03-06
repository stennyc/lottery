<?php 

include dirname(__FILE__).DIRECTORY_SEPARATOR."RequestHandler.class.php";
class tenpay {
    //配置
    private $config;
    public function __construct(){          
        $this->db=System::load_sys_class('model');      
    }     
    public function config($config=null){   
        $pay_type =$this->db->GetOne("SELECT * from `@#_payment` where `pay_class` = '$config[pay_class]' and `pay_start` = '1' and `pay_id`='$config[pay_id]'");
        $config['pay_account']=$pay_type['pay_account'];
        $config['pay_key']=$pay_type['pay_key'];
        $config['pay_type']=$pay_type['pay_type'];
        $payreturn1=array();$payreturn2=array();        
        $payreturn1['pay_class']=$pay_type['pay_class'];
        $payreturn1['pay_fun']="qiantai";       
        $payreturn1=base64_encode(json_encode($payreturn1));  
                             
        $payreturn2['pay_class']=$pay_type['pay_class'];
        $payreturn2['pay_fun']="houtai";    
        $payreturn2=base64_encode(json_encode($payreturn2));          
        $config['pay_ReturnUrl']= G_WEB_PATH.'/index.php/?plugin=true&api=Pay&action=return&data='.$payreturn1;
        $config['pay_NotifyUrl']=G_WEB_PATH.'/index.php/?plugin=true&api=Pay&action=return&data='.$payreturn2;        
        
            $this->config = $config;        
            $partner = $config['pay_account'];                                      //财付通商户号
            $key = $config['pay_key'];                                      //财付通密钥
            $return_url = $config['pay_ReturnUrl'];             //显示支付结果页面,*替换成payReturnUrl.php所在路径
            $notify_url = $config['pay_NotifyUrl'];             //支付完成后的回调处理页面,*替换成payNotifyUrl.php所在路径
            /* 获取提交的订单号 */
            $out_trade_no = $config['pay_code'];
            /* 获取提交的商品名称 */
            $product_name = $config['pay_title'];
            /* 获取提交的商品价格 */
            $order_price = $config['pay_money'];        
            /* 获取提交的备注信息 */
            $remarkexplain = '';
            /* 支付方式 */
            $trade_mode= $config['pay_type'];
            /* 商品价格（包含运费），以分为单位 */
            $total_fee = $order_price*100;
            /* 商品名称 */
            //$desc = "商品：".$product_name.",备注:".$remarkexplain;
            $desc = $product_name;
            /* 创建支付请求对象 */
            $reqHandler = new RequestHandler();
            $reqHandler->init();
            $reqHandler->setKey($key);
            $reqHandler->setGateUrl("https://gw.tenpay.com/gateway/pay.htm");
            //----------------------------------------
            //设置支付参数 
            //----------------------------------------
            $reqHandler->setParameter("partner", $partner);
            $reqHandler->setParameter("out_trade_no", $out_trade_no);
            $reqHandler->setParameter("total_fee", $total_fee);         //总金额
            $reqHandler->setParameter("return_url", $return_url);
            $reqHandler->setParameter("notify_url", $notify_url);
            $reqHandler->setParameter("body", $desc);
            $reqHandler->setParameter("bank_type", $config['pay_bank']);            //银行类型，默认为财付通DEFAULT
            //用户ip
            $reqHandler->setParameter("spbill_create_ip", $_SERVER['REMOTE_ADDR']);//客户端IP
            $reqHandler->setParameter("fee_type", "1");               //币种
            $reqHandler->setParameter("subject",$desc);          //商品名称，（中介交易时必填)

            //系统可选参数
            $reqHandler->setParameter("sign_type", "MD5");            //签名方式，默认为MD5，可选RSA
            $reqHandler->setParameter("service_version", "1.0");      //接口版本号
            $reqHandler->setParameter("input_charset", "utf-8");      //字符集
            $reqHandler->setParameter("sign_key_index", "1");         //密钥序号
            //业务可选参数
            $reqHandler->setParameter("attach", "");                  //附件数据，原样返回就可以了
            $reqHandler->setParameter("product_fee", "");             //商品费用
            $reqHandler->setParameter("transport_fee", "0");          //物流费用
            $reqHandler->setParameter("time_start", date("YmdHis"));  //订单生成时间
            $reqHandler->setParameter("time_expire", "");             //订单失效时间
            $reqHandler->setParameter("buyer_id", "");                //买方财付通帐号
            $reqHandler->setParameter("goods_tag", "");               //商品标记
            $reqHandler->setParameter("trade_mode",$trade_mode);      //交易模式（1.即时到帐模式，2.中介担保模式，3.后台选择（卖家进入支付中心列表选择））
            $reqHandler->setParameter("transport_desc","");           //物流说明
            $reqHandler->setParameter("trans_type","1");              //交易类型
            $reqHandler->setParameter("agentid","");                  //平台ID
            $reqHandler->setParameter("agent_type","");               //代理模式（0.无代理，1.表示卡易售模式，2.表示网店模式）
            $reqHandler->setParameter("seller_id","");                //卖家的商户号            
            //请求的URL
            $this->url=$reqUrl = $reqHandler->getRequestURL();
            //获取debug信息,建议把请求和debug信息写入日志，方便定位问题         
            $this->debugInfo=$debugInfo = $reqHandler->getDebugInfo();          
    }

    //支付页面
    public function send_pay(){
        $url = $this->url;
        header("Location: $url");exit;
    }

}

?>
