<?php

/**
 * Created by PhpStorm.
 * User: ford
 * Date: 2017/3/31
 * Time: 9:46
 */
abstract class WxMsg
{

    private $ToUserName;
    private $FromUserName;
    private $CreateTime;
    private $MsgType;

    function __construct($message = null)
    {
        if ($message){
            if (isset($message['ToUserName'])) $this->ToUserName = $message['ToUserName'];
            if (isset($message['FromUserName'])) $this->FromUserName = $message['FromUserName'];
            if (isset($message['CreateTime'])) $this->CreateTime = $message['CreateTime'];
            if (isset($message['MsgType'])) $this->MsgType = $message['MsgType'];
        }
    }

    public function set_to_user_name($user_name){
        $this->ToUserName = $user_name;
    }

    public function set_from_user_name($user_name){
        $this->FromUserName = $user_name;
    }

    public function set_create_time($create_time){
        $this->CreateTime = $create_time;
    }

    public function set_msg_type($msg_type){
        $this->MsgType = $msg_type;
    }

    public function get_to_user_name(){
        return $this->ToUserName;
    }

    public function get_from_user_name(){
        return $this->FromUserName;
    }

    public function get_create_time(){
        return $this->CreateTime;
    }

    public function get_msg_type(){
        return $this->MsgType;
    }


}