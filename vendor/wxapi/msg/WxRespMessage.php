<?php

/**
 * Created by PhpStorm.
 * User: ford
 * Date: 2017/3/30
 * Time: 21:04
 */
abstract class WxRespMessage
{

    protected $ToUserName;
    protected $FromUserName;
    protected $CreateTime;
    protected $MsgType;

    function __construct()
    {
        $this->CreateTime = time();
    }



    public abstract function output();

}