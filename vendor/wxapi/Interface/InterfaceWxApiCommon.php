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
class InterfaceWxApiCommon implements InterfaceWeixinApi
{

    /*缓存文件前缀*/
    const CACHE_PREFIX_ACCESS_TOKEN = 'wx';
    /*缓存普通access_token的key*/
    const CACHE_KEY_ACCESS_TOKEN = 'WX_ACCESS_TOKEN';
    /*缓存js_api_ticket的key*/
    const CACHE_KEY_JS_API_TICKET = 'WX_JS_API_TICKET';


    /**
     * 获取缓存中的access_token(如果无需缓存access_token或缓存已经过期则返回null或空字符串)
     * 返回null或空字符串则微信api则直接调用微信接口获取access_token
     * @return string|null
     */
    public function get_cache_access_token()
    {
        return $this->get_file_cache(self::CACHE_KEY_ACCESS_TOKEN,self::CACHE_PREFIX_ACCESS_TOKEN);
    }

    /**
     *
     * 缓存微信服务器中获取的普通access_token
     * @param array $result 微信服务器中获取到的access_token信息例如:array("access_token"=>"ACCESS_TOKEN","expires_in"=>7200)
     */
    public function cache_access_token($result)
    {
        if (isset($result['access_token']) && $result['access_token'] && isset($result['expires_in']) && $result['expires_in']){
            $expires_in = $result['expires_in'] - 200;
            $expires_in = $expires_in > 0 ? $expires_in:3600;
            $this->file_cache(self::CACHE_KEY_ACCESS_TOKEN,$result['access_token'],$expires_in,self::CACHE_PREFIX_ACCESS_TOKEN);
        }
    }

    /**
     * 获取缓存中的网页授权access_token(如果无需缓存access_token或缓存已经过期则返回null或空字符串)
     * 返回null或空字符串则微信api则直接调用微信接口获取网页授权access_token
     * @return string|null
     */
    public function get_cache_auth_access_token()
    {
        return null;
    }

    /**
     * 缓存网页授权access_token(与普通access_token不同缓存时请别搞混)
     * @param array $result 微信服务器中获取到的网页授权access_token信息例如array("access_token"=>"ACCESS_TOKEN","expires_in"=>7200,"refresh_token"=>"REFRESH_TOKEN","openid"=>"OPENID","scope"=>"SCOPE")
     * @return mixed
     */
    public function cache_auth_access_token($result)
    {
        return true;
    }

    /**
     * 缓存网页授权refresh_token
     * @param array $result 微信服务器中获取到的网页授权access_token信息例如array("access_token"=>"ACCESS_TOKEN","expires_in"=>7200,"refresh_token"=>"REFRESH_TOKEN","openid"=>"OPENID","scope"=>"SCOPE")
     * @return mixed
     */
    public function cache_auth_refresh_token($result)
    {
        return true;
    }

    /**
     * 获取缓存中的网页授权refresh_token如果有的话
     * @return string|null
     */
    public function get_cache_auth_refresh_token()
    {
        return null;
    }

    /**
     * 获取缓存中的jsapi ticket
     * @return mixed
     */
    public function get_cache_js_api_ticket()
    {
        return $this->get_file_cache(self::CACHE_KEY_JS_API_TICKET,self::CACHE_PREFIX_ACCESS_TOKEN);
    }

    /**
     * 缓存jsapi ticket
     * @param array $result js_api_ticket 结构为['errcode'=>0,'errmsg'=>'ok','ticket'=>"bxLdikRXVbTPdHSM05e5u5sUoXNKd8-41ZO3MhKoyN5OfkWITDGgnr2fwJ0m9E8NYzWKVZvdVtaUgWvsdshFKA",'expires_in'=>7200]
     * @return mixed
     */
    public function cache_js_api_ticket($result)
    {
        if (isset($result['errcode']) && $result['errcode'] == 0 && isset($result['ticket']) && $result['ticket'] && isset($result['expires_in']) && $result['expires_in']){
            $expires_in = $result['expires_in'] - 200;
            $expires_in = $expires_in > 0 ? $expires_in : 3600;
            $this->file_cache(self::CACHE_KEY_JS_API_TICKET,$result['ticket'],$expires_in,self::CACHE_PREFIX_ACCESS_TOKEN);
        }
        return true;
    }

    /**
     * 记录日志
     * @param string $msg           日志内容
     * @param string $log_path     日志路径
     */
    public function log($msg,$log_path = null)
    {
        $option = [
            'type' => 'File',
            'file_size' => 2097152,
            'time_format' => 'c',
        ];
        if ($log_path) {
            $option['path'] = WX_API_PATH.'/../../log/wx/';
        }else{
            $option['path'] = $log_path;
        }
        \think\Log::init($option);
        \think\Log::record($msg);
    }


    /**
     * 以文件类型缓存数据
     * @param $key
     * @param $value
     * @param int $expire
     * @param string $prefix
     * @return bool
     */
    public function file_cache($key,$value=null,$expire=70,$prefix=''){
        if (empty($key)) return false;
        $option = array(
            'type'=>'File',
            'prefix'=>$prefix,
            'path'=>CACHE_PATH,
        );
        $expire = empty($expire)?0:$expire;
        $cache = \think\Cache::connect($option);
        if ($value){
            return $cache->set($key,$value,$expire);
        }else{
            return $cache->rm($key);
        }
    }

    /**
     * 获取文件类型的缓存数据
     * @param $key
     * @param string $prefix
     * @return mixed
     */
    public function get_file_cache($key,$prefix=''){
        $option = array(
            'type'=>'File',
            'prefix'=>$prefix,
            'path'=>CACHE_PATH,
        );
        $cache = \think\Cache::connect($option);
        return $cache->get($key,[]);
    }

}