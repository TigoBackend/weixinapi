<?php
/**
 * Created by PhpStorm.
 * User: ford
 * Date: 2017/6/5
 * Time: 0:47
 */

namespace app\index\controller;


use think\Controller;
use think\Db;
use wx\InterfaceDepositPay;
use wx\InterfaceGoodsPay;
use wx\InterfaceWXCommon;

class WXPay
{

    

    /**
     * 以接口的形式向微信发起统一下单,并把订单信息返回给前端调起微信支付
     */
    public function wx_pay_api(){
        try{
            $out_trade_no = input('post.out_trade_no',0);
            if (!$out_trade_no) throw new \Exception('未知订单编号');
            $prefix = substr($out_trade_no,0,2);
            $interface = null;
            vendor('wxapi.index');
            switch ($prefix){
                case 12:
                    $config = [
                        'notify_url'=>''
                    ];
                    $interface = new InterfaceDepositPay($out_trade_no);
                    break;
                case 13:
                    $config = [

                        'notify_url'=>''
                    ];
                    $interface = new InterfaceGoodsPay($out_trade_no);
                    break;
                default:
                    throw new \Exception('未知订单类型');
            }
            $wx_pay_api = new \WeixinPayApi($config,new InterfaceWXCommon());
            Db::startTrans();
            $data = $wx_pay_api -> get_jssdk_parameters_specially_wx_user($interface);
            Db::commit();
            if (!$data) throw new \Exception('订单不能支付');
            $result = [
                'pay_data'=>$data,
                'succecc_url'=>$interface->get_success_url(),
                'fail_url'=>$interface->get_fail_url(),
            ];
            return $result;
        }catch (\Exception $e){
            /*捕捉到异常做自己的异常处理业务如:记录日志,回滚事务等*/
            Db::rollback();
            return ['status'=>false,'msg'=>$e->getMessage()];
        }
    }


    /**
     * 微信支付回调接口
     */
    public function wx_pay_notify(){
        try{
            vendor('wxapi.index');
            $wx_pay_api = new \WeixinPayApi(config('wx_config'),new InterfaceWXCommon());
            $result = $wx_pay_api -> check_notify();
            if (!$result) throw new \Exception('签名失败');

            $out_trade_no = $result['out_trade_no'];
            $prefix = substr($out_trade_no, 0, 2);
            $interface = null;
            switch ($prefix){
                case 12:
                    $interface = new InterfaceDepositPay($out_trade_no);
                    break;
                case 13:
                    $interface = new InterfaceGoodsPay($out_trade_no);
                    break;
                default:
                    throw new \Exception('未知订单类型');
            }
            Db::startTrans();
            $rs = $interface -> notify($result);
            if ($rs !== true) throw new \Exception('回调业务异常');
            Db::commit();

            $this->notify_success();
        }catch (\Exception $e){
            Db::rollback();
            $this->notify_fail('FAIL');
        }
    }


    private function notify_success()
    {
        echo '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
    }


    private function notify_fail($msg)
    {
        echo "<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[$msg]]></return_msg></xml>";
    }


    /**
     * 企业支付
     * @return array
     */
    public function business_transfer(){
        try{
            $data = [
                'partner_trade_no'=>'125630032',    /*订单号*/
                'openid'=>'sdflsodiu123654',        /*要收款的微信openid*/
                'amount'=>1,                        /*支付金额*/
                'desc'=>"来自楼链保证金退款,感谢对楼链的支持!!",     /*企业付款操作说明信息*/
//                'check_name'=>'FORCE_CHECK',         /*校验用户姓名选项 NO_CHECK：不校验真实姓名 FORCE_CHECK：强校验真实姓名 根据自己业务可选*/
//                'device_info'=>'微信支付分配的终端设备号',  /*微信支付分配的终端设备号 根据自己业务可选*/
//                're_user_name'=>'收款用户姓名'        /*收款用户姓名 收款用户真实姓名。 如果check_name设置为FORCE_CHECK，则必填用户真实姓名 根据自己业务可选*/
//                'spbill_create_ip'=>'192.168.0.1'   /*调用接口的机器Ip地址 根据自己业务可选*/
            ];
            $api = new \WeixinPayApi(config('wx_config'),new \InterfaceWxApiCommon());
            $rs = $api->business_transfer($data);
            if (!$rs) throw new \Exception('红包发放失败');
            if ($rs['return_code'] !== 'SUCCESS'){
                throw new \Exception($rs['return_msg']);
            }elseif($rs['result_code'] !== 'SUCCESS'){
                throw new \Exception($rs['err_code_des']);
            }
            return ['status'=>true,'msg'=>'success'];

        }catch (\Exception $e){
            /*捕捉到异常做自己的异常处理业务如:记录日志,回滚事务等*/
            return ['status'=>false,'msg'=>$e->getMessage()];
        }
    }


    /**
     * 红包发放
     * @return array
     */
    public function send_red_pack(){
        try{
            $data = [
                'mch_billno'=>'1234564qwe',        /*商户订单号*/
                're_openid'=>'sdflsodiu123654',        /*要收款的微信openid*/
                'total_amount'=>1,                        /*支付金额*/
//                'send_name'=>'天高',                /*红包发送者名称 根据自己业务需要可选*/
//                'wishing'=>"红包祝福语",                 /*红包祝福语 根据自己业务需要可选 */
//                'remark'=>"猜越多得越多，快来抢！",                 /*备注信息 根据自己业务需要可选*/
//                'client_ip'=>"192.168.0.1",                 /*调用接口的机器Ip地址 根据自己业务需要可选*/
//                'act_name'=>"活动名称",                 /*活动名称 根据自己业务需要可选*/
//                'scene_id'=>"PRODUCT_8",                 /*发放红包使用场景，红包金额大于200时必PRODUCT_1:商品促销PRODUCT_2:抽奖PRODUCT_3:虚拟物品兑奖PRODUCT_4:企业内部福利PRODUCT_5:渠道分润PRODUCT_6:保险回馈PRODUCT_7:彩票派奖PRODUCT_8:税务刮奖 根据自己业务需要可选*/
//                'risk_info'=>['posttime'=>time(),'mobile'=>'13758966589','deviceid'=>'地址或者设备唯一标识','clientversion'=>'用户操作的客户端版本',],      /*posttime:用户操作的时间戳mobile:业务系统账号的手机号，国家代码-手机号。不需要+号deviceid :mac 地址或者设备唯一标识 clientversion :用户操作的客户端版本 根据自己业务需要可选*/
//                'consume_mch_id'=>'1222000096'          /*资金授权商户号服务商替特约商户发放时使用 根据自己业务需要可选*/
            ];
            $api = new \WeixinPayApi(config('wx_config'),new \InterfaceWxApiCommon());
            $rs = $api->send_rend_pack($data);
            if (!$rs) throw new \Exception('红包发放失败');
            if ($rs['return_code'] !== 'SUCCESS'){
                throw new \Exception($rs['return_msg']);
            }elseif($rs['result_code'] !== 'SUCCESS'){
                throw new \Exception($rs['err_code_des']);
            }
            return ['status'=>true,'msg'=>'success'];

        }catch (\Exception $e){
            /*捕捉到异常做自己的异常处理业务如:记录日志,回滚事务等*/
            return ['status'=>false,'msg'=>$e->getMessage()];
        }
    }



}