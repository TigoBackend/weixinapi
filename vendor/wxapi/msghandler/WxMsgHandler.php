<?php

/**
 * Created by PhpStorm.
 * User: ford
 * Date: 2017/3/30
 * Time: 21:30
 */
class WxMsgHandler
{

    private $business_interface;

    function __construct(InterfaceMsgHandler $interface = null)
    {
        $this->business_interface = $interface;
    }


    /**
     * 处理微信通知事件
     *
     */
    public function handle()
    {
        $input = file_get_contents("php://input");
        if(empty($input)){
            echo '';
            exit;
        }
        $message = simplexml_load_string($input, 'SimpleXMLElement', LIBXML_NOCDATA);
        $message = json_encode($message);
        $message = json_decode($message,true);
        if (!isset($message['MsgType']) || empty($message['MsgType']) || !isset($message['Event']) || empty($message['Event'])){
            echo '';
            return;
        }
        switch ($message['MsgType']){
            case 'event':
                $this->handle_event($message);
                exit;
            case 'text':
                
            case 'image':
            case 'voice':
            case 'video':
            case 'shortvideo':
            case 'location':
            case 'link':
            default:
                echo "";
                exit;
        }
    }


    private function handle_event($message){
        switch ($message['Event']){
            case "subscribe":       /*用户关注时、未关注用户扫码带参数二维码进行关注时事件推送*/
                if ($this->business_interface) {
                    $msg = new WxMsgSubscribe($message);
                    $resp_msg = $this->business_interface->subscribe($msg);
                    if ($resp_msg instanceof WxRespTextMessage) {
                        $resp_msg -> set_from_user_name($message['ToUserName']);
                        $resp_msg -> set_to_user_name($message['FromUserName']);
                        $resp_msg->output();
                    }
                }
                break;
            case "unsubscribe":     /*取消关注时间推送*/
                if ($this->business_interface) {
                    $msg = new WxMsgSubscribe($message);
                    $this->business_interface->un_subscribe($msg);
                    echo '';
                    exit;
                }
                break;
            case "LOCATION":        /*上报地理位置事件推送*/
                if ($this->business_interface) {
                    $msg = new WxMsgLocation($message);
                    $this->business_interface->location($msg);
                    echo '';
                    exit;
                }
                break;
            case "SCAN":            /*已关注用户扫码带参数二维码时事件推送*/
                if ($this->business_interface) {
                    $msg = new WxMsgScan($message);
                    $this->business_interface->subscribe_scan($msg);
                    echo '';
                    exit;
                }
                break;
            case "VIEW":            /*用户点击自定义菜单后，点击菜单跳转链接时的事件推送*/
                if ($this->business_interface) {
                    $msg = new WxMsgView($message);
                    $this->business_interface->view($msg);
                    echo '';
                    exit;
                }
                break;
            case "CLICK":           /*用户点击自定义菜单后，点击菜单拉取消息时的事件推送*/
                if ($this->business_interface) {
                    $msg = new WxMsgClick($message);
                    $resp_msg = $this->business_interface->click($msg);
                    if ($resp_msg instanceof WxRespTextMessage) {
                        $resp_msg -> set_from_user_name($message['ToUserName']);
                        $resp_msg -> set_to_user_name($message['FromUserName']);
                        $resp_msg->output();
                    }
                    echo '';
                    exit;
                }
                break;
            default:
                echo '';
                exit;
        }
    }

}