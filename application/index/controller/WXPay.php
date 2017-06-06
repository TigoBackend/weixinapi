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

    



}