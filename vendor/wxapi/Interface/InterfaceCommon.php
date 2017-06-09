<?php

/**
 *
 * TP5下的通用微信基础api接口实现类
 * Created by PhpStorm.
 * User: ford
 * Date: 2017/3/14
 * Time: 16:52
 *
 */
class InterfaceCommon implements InterfaceWeixinApi
{

    public function get_cache_access_token()
    {
        return null;
    }

    public function cache_access_token($result)
    {
        return true;
    }


    public function log($msg)
    {
        return file_put_contents('api',$msg.PHP_EOL,FILE_APPEND);
    }

    public function cache_auth_refresh_token($result)
    {
        // TODO: Implement cache_auth_refresh_token() method.
    }

    public function cache_auth_access_token($result)
    {
        // TODO: Implement cache_auth_access_token() method.
    }

    public function get_cache_auth_refresh_token()
    {
        // TODO: Implement get_cache_auth_refresh_token() method.
    }

    public function get_cache_auth_access_token()
    {
        // TODO: Implement get_cache_auth_access_token() method.
    }

}