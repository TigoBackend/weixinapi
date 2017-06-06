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
    'viewpay'=>'wxpay/Index/wx_pay',
    'wx/msgHandle'=>'index/WX/msg_handle',
    'wx/wx_login'=>'index/WX/wx_auth',
    'wx/wx_login1'=>'index/WX/wx_auth1',
    'wx/getAccessToken'=>'index/WX/get_access_token',
    'wx/getCallbackIp'=>'index/WX/get_callback_ip',
    'wx/userInfo'=>'index/WX/get_subscribe_user_info',

];
