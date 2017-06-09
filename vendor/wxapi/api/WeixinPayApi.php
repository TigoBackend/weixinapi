<?php

/**
 * Created by PhpStorm.
 * User: ford
 * Date: 2017/4/17
 * Time: 17:01
 */
class WeixinPayApi extends WeixinApi
{

    /*统一下单接口*/
    const API_UNIFIED_ORDER = 'https://api.mch.weixin.qq.com/pay/unifiedorder';


    public function __construct(array $wx_config, $interface = null)
    {
        parent::__construct($wx_config, $interface);
    }


    /**
     * 统一下单返回前端jssdk调起支付需要的数据
     * 适用于后台调起支付获取数据
     * @param InterfacePay $interface
     * @param null $redirect_uri
     * @return array|bool
     * @throws Exception
     */
    public function get_jssdk_parameters_current_wx_user(InterfacePay $interface,$redirect_uri = null){
        if (!$interface || !$redirect_uri) throw new Exception('WeixinPayApi params exception');
        $config = $this->config;
        if (!isset($_GET['code'])) {
            /*获取订单号*/
            $out_trade_no=$_GET['out_trade_no'];
            if (!$out_trade_no) throw new Exception('WeixinPayApi out_trade_no exception');
            /*校验订单是否能够支付*/
            $can_pay = $interface->check_can_pay();
            if (!$can_pay) {
                if ($this->business_interface) $this -> business_interface -> log("out_trade_no:$out_trade_no can not pay");
                return $can_pay;
            }

            $this->get_code($redirect_uri,$out_trade_no,'snsapi_base',self::PROCESS_REDIRECT);
        }else{
            /*如果有code参数；则表示获取到openid*/
            $code = $_GET['code'];

            /*校验订单是否能够支付*/
            $can_pay = $interface->check_can_pay();
            if (!$can_pay) {
                if ($this->business_interface) $this -> business_interface -> log("received wx_code out_trade_no:{$interface->get_out_trade_no()} can not pay");
                return $can_pay;
            }

            /*组合获取prepay_id的url*/
            $result = self::curl_auth_access_token($code);

            $openid = $result['openid'];

            /*封装统一下单数据*/
            $order = $interface -> create_unified_order_data($openid);

            /*统一下单 获取prepay_id*/
            $unified_order=$this->unified_order($order);

            /*组合jssdk需要用到的数据*/
            $time=time();
            $data = [
                'appId'=>$config['app_id'], /*appid*/
                'timeStamp'=>strval($time), /*时间戳*/
                'nonceStr'=>$unified_order['nonce_str'], /*随机字符串*/
                'package'=>'prepay_id='.$unified_order['prepay_id'],/* 预支付交易会话标识*/
                'signType'=>'MD5'//加密方式
            ];

            /*生成签名*/
            $data['paySign']=$this->make_sign($data);
            return $data;
        }
    }


    /**
     * 统一下单返回前端jssdk调起支付需要的数据
     * 适用于使用已知open_id进行下单的情况,接口形式返回支付数据给前端调起支付的情况
     * @param InterfacePay $interface
     * @return array|bool
     * @throws Exception
     */
    public function get_jssdk_parameters_specially_wx_user(InterfacePay $interface){
        if (!$interface) throw new Exception('WeixinPayApi params exception');
        $wx_config = $this->config;
        /*校验订单是否能够支付*/
        $can_pay = $interface->check_can_pay();
        if (!$can_pay) return $can_pay;
        $open_id = $interface-> get_open_id();
        if (!$open_id){
            if ($this->business_interface) $this->business_interface->log('get_jssdk_parameters_specially_wx_user open_id exception');
            throw new Exception('get_jssdk_parameters_specially_wx_user open_id exception');
        }
        /*封装统一下单数据*/
        $order = $interface -> create_unified_order_data($open_id);
        /*统一下单 获取prepay_id*/
        $unified_order=$this->unified_order($order);

        /*组合jssdk需要用到的数据*/
        $time=time();
        $data = [
            'appId'=>$wx_config['app_id'], /*appid*/
            'timeStamp'=>strval($time), /*时间戳*/
            'nonceStr'=>$unified_order['nonce_str'], /*随机字符串*/
            'package'=>'prepay_id='.$unified_order['prepay_id'],/* 预支付交易会话标识*/
            'signType'=>'MD5'//加密方式
        ];

        /*生成签名*/
        $data['paySign']=$this->make_sign($data);
        return $data;
    }


    /**
     * 统一下单
     * @param $order
     * @return array
     * @throws Exception
     */
    private function unified_order($order){
        $wx_config = $this -> config;
        $nonce_str = $this->get_nonce_Str();
        $config = [
            'appid'=>$wx_config['app_id'],
            'mch_id'=>$wx_config['mch_id'],
            'nonce_str'=>$nonce_str,
            'spbill_create_ip'=>$this->client_ip(),
            'notify_url'=>$wx_config['notify_url']
        ];
        /*合并配置数据和订单数据*/
        $data = array_merge($order,$config);
        /*生成签名*/
        $sign = $this->make_sign($data);
        $data['sign'] = $sign;
        $xml = $this->to_xml($data);
        $header[] = "Content-type: text/xml";/*定义content-type为xml,注意是数组*/
        $ch = curl_init (self::API_UNIFIED_ORDER);
        curl_setopt($ch, CURLOPT_URL, self::API_UNIFIED_ORDER);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); /*兼容本地没有指定curl.cainfo路径的错误*/
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        $response = curl_exec($ch);
        if(curl_errno($ch)){
            // 显示报错信息；终止继续执行
            die(curl_error($ch));
        }
        curl_close($ch);
        $result = $this->to_array($response);
        /*显示错误信息*/
        if ($result['return_code']=='FAIL') {
            if ($this->business_interface) $this->business_interface->log('unified_order fail:'.json_encode($result));
            throw new Exception($result['return_msg']);
        }
        $result['sign']=$sign;
        $result['nonce_str']=$nonce_str;
        return $result;
    }



    public function business_transfer($data){
        $data['mch_appid'] = $this->config['app_id'];
        $data['mchid'] = $this->config['mch_id'];
        $data['check_name'] = "NO_CHECK";
        $data['spbill_create_ip'] = gethostbyname($_ENV['COMPUTERNAME']);
        $data['nonce_str'] = $this->get_nonce_Str();
        /*生成签名*/
        $sign = $this->make_sign($data);
        $data['sign'] = $sign;
        $xml = $this->to_xml($data);
        if ($this->business_interface) $this -> business_interface ->log('企业支付数据:'.json_encode($data,JSON_UNESCAPED_UNICODE));

        /*发出请求*/
        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_TIMEOUT,30);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
        /*以下两种方式需选择一种*/
        /*第一种方法，cert 与 key 分别属于两个.pem文件*/
        /*curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');*/
        curl_setopt($ch,CURLOPT_SSLCERT,VENDOR_PATH.'wxapi/cert/apiclient_cert.pem');
        /*curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');*/
        curl_setopt($ch,CURLOPT_SSLKEY,VENDOR_PATH.'wxapi/cert/apiclient_key.pem');
        /*第二种方式，两个文件合成一个.pem文件*/
        /*curl_setopt($ch,CURLOPT_SSLCERT,getcwd().'/all.pem');*/
        /*设置头文件*/
        /*if( count($aHeader) >= 1 ){
            curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);
        }*/

        curl_setopt($ch,CURLOPT_POST, 1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$xml);
        $data = curl_exec($ch);
        if($data){
            curl_close($ch);
            $data = $this->to_array($data);
            if ($this->business_interface) $this -> business_interface ->log('企业支付结果:'.json_encode($data,JSON_UNESCAPED_UNICODE));
            return $data;
        }else {
            $error = curl_errno($ch);
            /*echo "call faild, errorCode:$error\n";*/
            if ($this->business_interface) $this -> business_interface ->log('红包发送失败,error::'.$error);
            curl_close($ch);
            return false;
        }
    }


    

    



}