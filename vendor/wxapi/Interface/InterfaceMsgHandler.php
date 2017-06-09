<?php

/**
 * Created by PhpStorm.
 * User: ford
 * Date: 2017/3/31
 * Time: 9:28
 */
interface InterfaceMsgHandler
{

    /**
     * 用户关注时、未关注用户扫码带参数二维码进行关注时事件推送
     * @param WxMsgSubscribe $msg
     * @return WxRespTextMessage|null
     */
    public function subscribe(WxMsgSubscribe $msg);


    /**
     * 取消关注消息推送
     * @param WxMsgSubscribe $msg
     * @return mixed|null
     */
    public function un_subscribe(WxMsgSubscribe $msg);


    /**
     * 用户同意上报地理位置后，每次进入公众号会话时，都会在进入时上报地理位置，
     * 或在进入会话后每5秒上报一次地理位置，公众号可以在公众平台网站中修改以上设置。
     * 上报地理位置时，微信会将上报地理位置事件推送到开发者填写的URL
     * @param WxMsgLocation $msg
     * @return mixed|null
     */
    public function location(WxMsgLocation $msg);

    /**
     *已关注用户扫码带参数二维码时事件推送
     * @param WxMsgScan $msg
     * @return mixed|null
     */
    public function subscribe_scan(WxMsgScan $msg);

    /**
     * 自定义菜单事件,点击菜单跳转链接时的事件推送
     * @param WxMsgView $msg
     * @return mixed|null
     */
    public function view(WxMsgView $msg);

    /**
     * 自定义菜单事件,点击菜单拉取消息时的事件推送
     * @param WxMsgClick $msg
     * @return WxRespTextMessage|null
     */
    public function click(WxMsgClick $msg);


}