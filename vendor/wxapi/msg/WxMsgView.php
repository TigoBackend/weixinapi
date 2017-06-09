<?php

/**
 * Created by PhpStorm.
 * User: ford
 * Date: 2017/6/4
 * Time: 23:14
 */
class WxMsgView extends WxMsg
{
    private $Event;
    private $EventKey;

    function __construct($message = null)
    {
        parent::__construct($message);
        if ($message){
            if (isset($message['Event'])) $this->Event = $message['Event'];
            if (isset($message['EventKey'])) $this->EventKey = $message['EventKey'];
        }
    }


    public function set_event($event){
        $this->Event = $event;
    }


    public function set_event_key($event_key){
        $this->EventKey = $event_key;
    }

    public function get_event(){
        return $this->Event;
    }

    public function get_event_key(){
        return $this->EventKey;
    }

}