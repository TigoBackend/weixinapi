<?php

/**
 * Created by PhpStorm.
 * User: ford
 * Date: 2017/4/17
 * Time: 17:05
 */
interface InterfacePay
{
    

    public function __construct($out_trade_no);


    /**
     * 校验订单是否可以支付,返回bool
     * @return bool
     */
    public function check_can_pay();



    /**
     * 封装统一下单所需的业务参数
     * 可以支付则封装统一下单中body、total_fee、out_trade_no、product_id、trade_type、openid等数据返回
     * 否则返回bool
     * @param $open_id
     * @return bool     订单不能支付返回false
     * @return mixed|array    订单可以支付返回统一下单数据
     */
    public function create_unified_order_data($open_id);

    /**
     * 支付回调业务
     * @param array $result 微信支付结果
     * @return bool     完成支付业务返回结果true-完成 false-失败 失败是也可以选择抛出异常有上层处理结果
     * @throws \Exception 失败是可以选择抛出异常有上层处理结果
     */
    public function notify($result);


    /**
     * 获取支付成功后的跳转链接
     * @return string   支付成功时的跳转页面
     */
    public function get_success_url();


    /**
     * 获取支付失败或取消时的跳转链接
     * @return string   支付失败时的跳转页面
     */
    public function get_fail_url();


    /**
     * 获取用户open_id
     * 适用于
     * @return mixed
     */
    public function get_open_id();


    /**
     * 获取指定的回调地址
     * @return mixed
     */
    public function get_notify_url();


    /**
     * 获取订单编号
     * @return mixed
     */
    public function get_out_trade_no();


}