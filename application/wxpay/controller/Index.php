<?php

/**
 * Created by PhpStorm.
 * User: ford
 * Date: 2017/6/5
 * Time: 14:38
 */
namespace app\wxpay\controller;


use think\Controller;
use think\Request;
use wx\InterfaceDepositPay;
use wx\InterfaceGoodsPay;
use wx\InterfaceWXCommon;

class Index extends Controller
{
    function __construct(Request $request)
    {
        parent::__construct($request);
    }


    /**
     * 网页形式调起微信支付
     * @return array
     */
    public function wx_pay(){
        try{
            $out_trade_no = input('post.out_trade_no',0);
            if (!$out_trade_no) throw new \Exception('未知订单编号');
            $prefix = substr($out_trade_no,0,2);
            $interface = null;
            vendor('wxapi.index');
            switch ($prefix){
                case 12:
                    $interface = new InterfaceGoodsPay($out_trade_no);
                    break;
                case 13:
                    $interface = new InterfaceDepositPay($out_trade_no);
                    break;
                default:
                    throw new \Exception('未知订单类型');
            }
            $wx_pay_api = new \WeixinPayApi(config('wx_config'),new InterfaceWXCommon());
            $redirect_url = url('index/WXPay/wx_pay','',false,true);
            $data = $wx_pay_api -> get_jssdk_parameters_current_wx_user($interface,$redirect_url);
            if (!$data) throw new \Exception('订单不能支付');
            $result = [
                'pay_data'=>$data,
                'succecc_url'=>$interface->get_success_url(),
                'fail_url'=>$interface->get_fail_url(),
            ];

            $this->assign('pay_data',$result);
            return $this->fetch();
        }catch (\Exception $e){
            /*捕捉到异常做自己的异常处理业务如:记录日志,回滚事务等*/
            return $this->fetch('error');
        }
    }

}