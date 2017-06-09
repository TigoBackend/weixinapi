<?php

/**
 * Created by PhpStorm.
 * User: ford
 * Date: 2017/6/4
 * Time: 16:31
 */
class WxMsgLocation extends WxMsg
{
    private $Event;
    private $Latitude;
    private $Longitude;
    private $Precision;

    function __construct($message = null)
    {
        parent::__construct($message);
        if ($message){
            if (isset($message['Event'])) $this->Event = $message['Event'];
            if (isset($message['Latitude'])) $this->Latitude = $message['Latitude'];
            if (isset($message['Longitude'])) $this->Longitude = $message['Longitude'];
            if (isset($message['Precision'])) $this->Precision = $message['Precision'];
        }
    }


    public function set_event($event){
        $this->Event = $event;
    }


    public function set_latitude($latitude){
        $this->Latitude = $latitude;
    }


    public function set_longitude($longitude){
        $this->Longitude = $longitude;
    }


    public function set_precision($precision){
        $this->Precision = $precision;
    }

    public function get_event(){
        return $this->Event;
    }


    public function get_latitude(){
        return $this->Latitude;
    }


    public function get_longitude(){
        return $this->Longitude;
    }


    public function get_precision(){
        return $this->Precision;
    }

}