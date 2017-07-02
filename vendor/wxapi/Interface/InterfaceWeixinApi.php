<?php
/**
 * Created by PhpStorm.
 * User: ford
 * Date: 2017/2/6
 * Time: 15:39
 */


interface InterfaceWeixinApi
{

    /**
     * 获取缓存中的access_token(如果无需缓存access_token或缓存已经过期则返回null或空字符串)
     * 返回null或空字符串则微信api则直接调用微信接口获取access_token
     * @return string|null
     */
    public function get_cache_access_token();

    /**
     *
     * 缓存微信服务器中获取的普通access_token
     * @param array $result 微信服务器中获取到的access_token信息例如:array("access_token"=>"ACCESS_TOKEN","expires_in"=>7200)
     */
    public function cache_access_token($result);

    /**
     * 获取缓存中的网页授权access_token(如果无需缓存access_token或缓存已经过期则返回null或空字符串)
     * 返回null或空字符串则微信api则直接调用微信接口获取网页授权access_token
     * @return string|null
     */
    public function get_cache_auth_access_token();

    /**
     * 缓存网页授权access_token(与普通access_token不同缓存时请别搞混)
     * @param array $result 微信服务器中获取到的网页授权access_token信息例如array("access_token"=>"ACCESS_TOKEN","expires_in"=>7200,"refresh_token"=>"REFRESH_TOKEN","openid"=>"OPENID","scope"=>"SCOPE")
     * @return mixed
     */
    public function cache_auth_access_token($result);

    /**
     * 缓存网页授权refresh_token
     * @param array $result 微信服务器中获取到的网页授权access_token信息例如array("access_token"=>"ACCESS_TOKEN","expires_in"=>7200,"refresh_token"=>"REFRESH_TOKEN","openid"=>"OPENID","scope"=>"SCOPE")
     * @return mixed
     */
    public function cache_auth_refresh_token($result);


    /**
     * 获取缓存中的网页授权refresh_token如果有的话
     * @return string|null
     */
    public function get_cache_auth_refresh_token();


    /**
     * 获取缓存中的jsapi ticket
     * @return mixed
     */
    public function get_cache_js_api_ticket();


    /**
     * 缓存jsapi ticket
     * @param array $result js_api_ticket 结构为['errcode'=>0,'errmsg'=>'ok','ticket'=>"bxLdikRXVbTPdHSM05e5u5sUoXNKd8-41ZO3MhKoyN5OfkWITDGgnr2fwJ0m9E8NYzWKVZvdVtaUgWvsdshFKA",'expires_in'=>7200]
     * @return mixed
     */
    public function cache_js_api_ticket($result);



    /**
     * 记录日志
     * @param string $msg           日志内容
     * @param string $log_path     日志路径
     */
    public function log($msg,$log_path = null);




}