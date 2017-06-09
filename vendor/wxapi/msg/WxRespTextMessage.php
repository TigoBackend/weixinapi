<?php

/**
 * Created by PhpStorm.
 * User: ford
 * Date: 2017/3/30
 * Time: 21:06
 */
class WxRespTextMessage extends WxRespMessage
{

    private $Content;

    function __construct()
    {
        parent::__construct();
        $this->MsgType = 'text';
    }

    /**
     * 回复的消息内容（换行：在content中能够换行，微信客户端就支持换行显示）
     * @param $content
     */
    public function set_content($content){
        $this->Content = $content;
    }

    /**
     * 接收方帐号（收到的OpenID）
     * @param $user_name
     */
    public function set_to_user_name($user_name){
        $this->ToUserName = $user_name;
    }

    /**
     * 开发者微信号
     * @param $user_name
     */
    public function set_from_user_name($user_name){
        $this->FromUserName = $user_name;
    }

    /**
     * 输出到微信服务器
     */
    public function output()
    {
        $template = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							</xml>";
        $xml = sprintf($template,$this->ToUserName,$this->FromUserName,$this->CreateTime,$this->MsgType,$this->Content);
        echo $xml;
        exit;
    }

}