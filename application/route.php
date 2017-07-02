<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    '__pattern__' => [
        'name' => '\w+',
    ],
    '[hello]'     => [
        ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
        ':name' => ['index/hello', ['method' => 'post']],
    ],

    'wx'=>'index/WX/index',
    /*微信消息推送处理接口*/
    'wx/msgHandle'=>'index/WX/msg_handle',
    /*网页授权方式1*/
    'wx/wx_login'=>'index/WX/wx_auth',
    /*网页授权方式2*/
    'wx/wx_login1'=>'index/WX/wx_auth1',
    /*获取微信access_token*/
    'wx/getAccessToken'=>'index/WX/get_access_token',
    /*获取微信服务器IP地址*/
    'wx/getCallbackIp'=>'index/WX/get_callback_ip',
    /*获取微信用户信息*/
    'wx/userInfo'=>'index/WX/get_subscribe_user_info',
    /*微信分享*/
    'wx/share'=>'index/WX/share_url',
    /*拉取公众号菜单*/
    'wx/pull_menu'=>'index/WX/pull_menu',
    /*发布公众号自定义菜单*/
    'wx/push_menu'=>'index/WX/push_menu',
    /*企业支付*/
    'wx/business_transfer'=>'index/WXPay/business_transfer',
    /*发红包*/
    'wx/red_pack'=>'index/WXPay/send_red_pack',
    /*后台调起微信支付*/
    'wx/to_pay'=>'wxpay/Index/wx_pay',
    /*微信支付回调*/
    'wx/call_back'=>'index/WXPay/wx_pay_notify',
    /*获取微信支付签名数据*/
    'wx/get_pay'=>'index/WXPay/wx_pay_api',
];
