<?php

/**
 * Created by PhpStorm.
 * User: ford
 * Date: 2017/6/4
 * Time: 23:04
 */
class WxMsgScan extends WxMsg
{
    private $Event;
    private $EventKey;
    private $Ticket;

    function __construct($message = null)
    {
        parent::__construct($message);
        if ($message){
            if (isset($message['Event'])) $this->Event = $message['Event'];
            if (isset($message['EventKey'])) $this->EventKey = $message['EventKey'];
            if (isset($message['Ticket'])) $this->Ticket = $message['Ticket'];
        }
    }

    public function set_event($event){
        $this->Event = $event;
    }
    
    
    public function set_event_key($event_key){
        $this->EventKey = $event_key;
    }
    
    
    public function set_ticket($ticket){
        $this->Ticket = $ticket;
    }

    public function get_event(){
        return $this->Event;
    }

    public function get_event_key(){
        return $this->EventKey;
    }

    public function get_ticket(){
        return $this->Ticket;
    }

}