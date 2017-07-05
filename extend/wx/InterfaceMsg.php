<?php

/**
 * Created by PhpStorm.
 * User: ford
 * Date: 2017/6/4
 * Time: 15:51
 */
namespace wx;

use WxMsgClick;
use WxMsgLocation;
use WxMsgScan;
use WxMsgSubscribe;
use WxMsgView;
use WxRespTextMessage;

class InterfaceMsg implements \InterfaceMsgHandler
{
    /**
     * 用户关注时、未关注用户扫码带参数二维码进行关注时事件推送
     * @param WxMsgSubscribe $msg
     * @return WxRespTextMessage|null
     */
    public function subscribe(WxMsgSubscribe $msg)
    {
        $openid = $msg -> get_from_user_name();
        vendor('wxapi.index');
        $interface = new InterfaceWXCommon();
        $api = new \WeixinApi(config('wx_config'),$interface);
        /*已关注用户*/
//            $rs = $api -> get_subscribe_user_info('o09KlwiCtHz1IN__67Rg-HhqqF1k');
        /*未关注用户*/
        $rs = $api -> get_subscribe_user_info($openid);
    }

    /**
     * 取消关注消息推送
     * @param WxMsgSubscribe $msg
     * @return mixed|null
     */
    public function un_subscribe(WxMsgSubscribe $msg)
    {
        // TODO: Implement un_subscribe() method.
    }

    /**
     * 用户同意上报地理位置后，每次进入公众号会话时，都会在进入时上报地理位置，
     * 或在进入会话后每5秒上报一次地理位置，公众号可以在公众平台网站中修改以上设置。
     * 上报地理位置时，微信会将上报地理位置事件推送到开发者填写的URL
     * @param WxMsgLocation $msg
     * @return mixed|null
     */
    public function location(WxMsgLocation $msg)
    {
        // TODO: Implement location() method.
    }

    /**
     *已关注用户扫码带参数二维码时事件推送
     * @param WxMsgScan $msg
     * @return mixed|null
     */
    public function subscribe_scan(WxMsgScan $msg)
    {
        // TODO: Implement subscribe_scan() method.
    }

    /**
     * 自定义菜单事件,点击菜单跳转链接时的事件推送
     * @param WxMsgView $msg
     * @return mixed|null
     */
    public function view(WxMsgView $msg)
    {
        // TODO: Implement view() method.
    }

    /**
     * 自定义菜单事件,点击菜单拉取消息时的事件推送
     * @param WxMsgClick $msg
     * @return WxRespTextMessage|null
     */
    public function click(WxMsgClick $msg)
    {
        // TODO: Implement click() method.
    }

}