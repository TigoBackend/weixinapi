<?php
/**
 * Created by PhpStorm.
 * User: ford
 * Date: 2017/2/6
 * Time: 17:27
 */



class WeixinMsgTemplateApi extends WeixinApi
{

    /*微信模板消息发送API接口*/
    const API_TEMPLATE_SEND_URL = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=";

    /**
     * 消息模板api构造函数
     * WeixinMsgTemplateApi constructor.
     * @param array $wx_config
     * @param InterfaceWeixinApi $interface
     */
    public function __construct(array $wx_config,InterfaceWeixinApi $interface = null)
    {
        parent::__construct($wx_config, $interface);
    }


    /**
     * 推送模板信息
     * @param WeixinTemplate $template  要推送的模板
     * @return bool|array  推送失败返回false，否则返回推送结果
     */
    public function sendTemplateMsg(WeixinTemplate $template){
        if ($template instanceof WeixinTemplate){
            $access_token = $this->get_access_token();
            if (!$access_token) {
                /*执行业务接口的推送日志记录*/
                if ($this->business_interface) $this->business_interface->log('access_token empty');
                return false;
            }
            $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=$access_token";
            $params = $template -> get_params();
            $result = $this->curl($url,json_encode($params));
            if ($result['errcode']){
                /*执行业务接口的推送日志记录*/
                if ($this->business_interface) $this->business_interface->log('sendTemplateMsg: result:'.json_encode($result));
                /*如果access_token已经过期强制刷新缓存access_token并从新发送一次(此处恐怕会有死循环如果has_retry不起作用的话)*/
                static $has_retry = false;
                if ($result['errcode'] == 40001 && $has_retry === false){
                    $this->refreshAccessToken();
                    $this->sendTemplateMsg($template);
                    $has_retry = true;
                }
                return false;
            }
            return $result;
        }else{
            return false;
        }
    }


}