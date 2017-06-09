<?php

/**
 * Created by PhpStorm.
 * User: ford
 * Date: 2017/3/31
 * Time: 10:24
 */
class WxMsgText extends WxMsg
{

    private $Content;
    private $MsgId;


    function __construct($message = null)
    {
        parent::__construct($message);
        if ($message){
            if (isset($message['Content'])) $this->Content;
            if (isset($message['MsgId'])) $this->MsgId;
        }
    }

    public function set_content($content){
        $this->Content = $content;
    }

    public function set_msg_id($msg_id){
        $this->MsgId = $msg_id;
    }



}