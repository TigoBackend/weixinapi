<?php
/**
 * Created by PhpStorm.
 * User: ford
 * Date: 2017/2/6
 * Time: 15:26
 */



class WeixinApi
{
    /*函数处理方案函数返回*/
    const PROCESS_RETURN = 'RETURN';
    /*函数处理方案函数重定向*/
    const PROCESS_REDIRECT = 'REDIRECT';

    const SCOPE_TYPE_USER_INFO = 'snsapi_userinfo';

    const SCOPE_TYPE_BASE = 'snsapi_base';


    /*获取access_token的API接口*/
    const API_ACCESS_TOKEN = 'https://api.weixin.qq.com/cgi-bin/token?';
    /*获取微信服务器ip地址接口*/
    const API_CALLBACK_IP = 'https://api.weixin.qq.com/cgi-bin/getcallbackip?';
    /*获取用户基本信息(UnionID机制)*/
    const API_SUBSCRIBE_USER_INFO = 'https://api.weixin.qq.com/cgi-bin/user/info?';

    /*微信网页授权系列接口地址*/
    /*获取code*/
    const API_AUTH_CODE = 'https://open.weixin.qq.com/connect/oauth2/authorize?';
    /*获取网页授权access_token*/
    const API_AUTH_ACCESS_TOKEN = 'https://api.weixin.qq.com/sns/oauth2/access_token?';
    /*刷新网页授权access_token*/
    const API_AUTH_REFRESH_TOKEN = 'https://api.weixin.qq.com/sns/oauth2/refresh_token?';
    /*拉取用户信息*/
    const API_AUTH_USER_INFO = 'https://api.weixin.qq.com/sns/userinfo?';
    /*获取ticket*/
    const API_GET_TICKET = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?';

    protected $business_interface;
    protected $config;

    /**
     * 微信api类构造函数
     * WeixinApi constructor.
     * @param array $wx_config    微信配置信息
     * @param InterfaceWeixinApi|null $interface        自定义业务接口
     * @throws Exception
     */
    public function __construct($wx_config,InterfaceWeixinApi $interface=null)
    {
        if (!isset($wx_config['app_id']) || !isset($wx_config['app_secret'])) {
            if ($interface) $interface->log('WeixinApi wx_config exception!');
            throw new Exception('WeixinApi wx_config exception!');
        }
        $this->config = $wx_config;
        $this->business_interface = $interface;
    }


    /**
     * 通过微信api或缓存机制获取可用的微信普通access_token
     * @return null|string
     */
    public function get_access_token(){
        /*使用业务接口获取缓存中的access_token避免频繁调用接口刷新access_token*/
        if ($this->business_interface){
            $access_token = $this->business_interface->get_cache_access_token();
            if(!empty($access_token)){
                return $access_token;
            }
        }
        return $this->refreshAccessToken();
    }

    /**
     *  获取微信服务器ip地址列表
     * @return mixed|null
     */
    public function get_callback_ip(){
        $access_token = $this->get_access_token();
        if (!$access_token){
            if ($this->business_interface) $this->business_interface->log('access_token is null');
            return null;
        }
        $url = self::API_CALLBACK_IP.'access_token='.$access_token;
        $result = $this->curl($url);
        return $result;
    }


    /**
     * 获取已关注的微信用户信息
     * @param $open_id
     * @return mixed
     */
    public function get_subscribe_user_info($open_id){
        $access_token = $this->get_access_token();
        $url = self::API_SUBSCRIBE_USER_INFO."access_token={$access_token}&openid={$open_id}&lang=zh_CN";
        $result = $this->curl($url);
        if (isset($result['nickname']) && $result['nickname']) $result['nickname'] = self::filter_emoji($result['nickname']);
        return $result;
    }

    /**
     * 分享链接
     * @param $url
     */
    public function share_url($url){
        if (empty($url)){
            if ($this->business_interface)$this->business_interface->log('share_url url is null');
            return null;
        }
        $js_api_ticket = $this->get_js_api_ticket();
        if (empty($js_api_ticket)){
            if ($this->business_interface)$this->business_interface->log('share_url js_api_ticket is null');
            return null;
        }
        $timestamp = time();
        $nonce_str = $this->get_nonce_Str(16);

        $str = "jsapi_ticket=$js_api_ticket&noncestr=$nonce_str&timestamp=$timestamp&url=$url";
        $signature = sha1($str);
        $sign_package = [
            'app_id'=>$this->config['app_id'],
            'nonce_str'=>$nonce_str,
            'timestamp'=>$timestamp,
            'url'=>$url,
            'signature'=>$signature,
            'raw_string'=>$str,
        ];
        return $sign_package;
    }


    /**
     * 通过微信api或缓存机制获取可用的微信jsapi_ticket
     * @return mixed|null
     */
    public function get_js_api_ticket(){
        /*使用业务接口获取缓存中的js_api_ticket避免频繁调用接口刷新js_api_ticket*/
        if ($this->business_interface){
            $js_api_ticket = $this -> business_interface -> get_cache_js_api_ticket();
            if (!empty($js_api_ticket)){
                return $js_api_ticket;
            }
        }
        return $this->refresh_js_api_ticket();
    }


    /**
     * 向微信获取并通过业务接口缓存js_api_ticket
     * @return null
     */
    protected function refresh_js_api_ticket(){
        $config = $this -> config;
        if(!isset($config['app_id']) || !isset($config['app_secret']) || empty($config['app_id']) || empty($config['app_secret'])){
            if ($this->business_interface) $this->business_interface->log('refresh_js_api_ticket APP_ID or APP_SECRET must be set');
            return null;
        }
        $access_token = $this -> get_access_token();
        if (empty($access_token)){
            if ($this->business_interface) $this->business_interface->log('refresh_js_api_ticket access_token is null');
            return null;
        }
        $url = self::API_GET_TICKET."access_token={$access_token}&type=jsapi";
        $result = $this -> curl($url);
        if (isset($result['errcode']) && $result['errcode'] == 0 && isset($result['ticket'])){
            if ($this->business_interface)$this->business_interface->cache_js_api_ticket($result);
            return $result['ticket'];
        }elseif (isset($result['errmsg']) && $result['errmsg'] && $this->business_interface){
            $this->business_interface->log("refresh_js_api_ticket exception:{$result['errmsg']}");
        }
        return null;
    }






    /*-------------------------------------------------网页授权系列start--------------------------------------------------------------------*/

    /**
     * 返回微信网页授权链接或者直接重定向到授权链接
     * @param string $redirect_uri 回调地址
     * @param string $state    自定义参数,会原样在调地址中添加进去
     * @param string $scope 取值范围 snsapi_userinfo|snsapi_base
     * @param string $process 函数流程控制参数默认为RETURN返回授权链接给外部处理
     * @throws Exception
     * @return string       返回微信网页授权链接|直接重定向到授权链接
     */
    public function get_code($redirect_uri,$state='state',$scope='snsapi_userinfo',$process = 'RETURN'){
        if (empty($scope))$scope='snsapi_userinfo';
        if ($scope !== 'snsapi_userinfo' && $scope !== 'snsapi_base'){
            if ($this->business_interface) $this->business_interface->log('scope exception');
            throw new Exception('WeixinApi scope exception!');
        }
        $redirect_uri = urlencode($redirect_uri);
        $code_url = self::API_AUTH_CODE."appid={$this->config['app_id']}&redirect_uri=$redirect_uri&response_type=code&scope=$scope&state=$state#wechat_redirect";
        $process = strtoupper($process);
        switch ($process){
            case self::PROCESS_RETURN:
                return $code_url;
                break;
            case self::PROCESS_REDIRECT:
            default:
                header("Location: $code_url");
        }
    }


    /**
     * @param string $code     申请的网页授权code
     * @return bool|array       返回false表示获取授权access_token失败,否则返回获取到的信息由外部处理格式为array("access_token"=>"ACCESS_TOKEN","expires_in"=>7200,"refresh_token"=>"REFRESH_TOKEN","openid"=>"OPENID","scope"=>"SCOPE")
     */
    public function curl_auth_access_token($code){
        $url = self::API_AUTH_ACCESS_TOKEN."appid={$this->config['app_id']}&secret={$this->config['app_secret']}&code=$code&&grant_type=authorization_code";
        $result = $this->curl($url);
        if (!isset($result['access_token'])){
            if ($this->business_interface) $this->business_interface->log("auth access_token exception:".json_encode($result));
            return false;
        }else{
            if ($this->business_interface) $this->business_interface->cache_auth_access_token($result);
            if ($this->business_interface) $this->business_interface->cache_auth_refresh_token($result);
            return $result;
        }
    }

    /**
     * 通过refresh_token刷新授权access_token
     * @param null $refresh_token
     * @return bool|mixed   刷新授权access_token异常返回false,否则返回获取到的access_token信息,例如array("access_token"=>"ACCESS_TOKEN","expires_in"=>7200,"refresh_token"=>"REFRESH_TOKEN","openid"=>"OPENID","scope"=>"SCOPE")
     */
    public function refresh_auth_access_token($refresh_token=null){
        if (!$refresh_token){
            if ($this->business_interface) $refresh_token = $this->business_interface->get_cache_auth_refresh_token();
            if (!$refresh_token) return false;
        }
        $url = self::API_AUTH_REFRESH_TOKEN."appid={$this->config['app_id']}&grant_type=refresh_token&refresh_token=$refresh_token";
        $result = $this->curl($url);
        if (!$result['access_token']){
            if ($this->business_interface) $this->business_interface->log("auth refresh_token exception:".json_encode($result));
            return false;
        }else{
            if ($this->business_interface) $this->business_interface->cache_auth_access_token($result);
            if ($this->business_interface) $this->business_interface->cache_auth_refresh_token($result);
            return $result;
        }
    }


    /**
     * 通过code拉起用户信息
     * @param $code
     * @return array|bool|mixed     成功拉取信息返回数组如['openid'=>'openid','nickname'=>'NICKNAME','sex'=>'1','province'=>'PROVINCE','city'=>'city','country'=>'country','headimgurl'=>'www.baidu.com','privilege'=>['PRIVILEGE1','PRIVILEGE2'],'unionid'=>'o6_bmasdasdsad6_2sgVt7hMZOPfL']
     */
    public function get_user_info($code){
        if (empty($code)){
            if ($this->business_interface) $this->business_interface->log('get_user_info code exception code:'.json_encode($code));
            return false;
        }
        $result = $this->curl_auth_access_token($code);
        if (!$result){
            if ($this->business_interface) $this->business_interface->log('get_user_info curl_auth_access_token exception result:'.json_encode($result));
            return false;
        }
        $url = self::API_AUTH_USER_INFO."access_token={$result['access_token']}&openid={$result['openid']}&lang=zh_CN";
        $result = $this->curl($url);
        if (isset($result['errcode']) && $result['errcode']){
            if ($this->business_interface) $this->business_interface->log('get_user_info get user_info exception result:'.json_encode($result));
            return false;
        }
        return $result;
    }
    
    

    /*-------------------------------------------------网页授权系列end--------------------------------------------------------------------*/



    /**
     * 刷新微信普通access_token并保存到缓存各自的机制中(如果business_interface有实现的话)
     * @return string|null
     */
    protected function refreshAccessToken(){
        $config = $this->config;
        if (empty($config['app_id']) || empty($config['app_secret'])){
            if ($this->business_interface) $this->business_interface->log('APP_ID or APP_SECRET must be set');
            return null;
        }

        $url = self::API_ACCESS_TOKEN."grant_type=client_credential&appid={$config['app_id']}&secret={$config['app_secret']}";
        $result = $this->curl_access_token($url);
        if (!empty($result)) {
            $result = json_decode($result,true);
            if (empty($result['access_token'])) {
                if ($this->business_interface) $this->business_interface->log("Weixin Exception:errcode:{$result['errcode']},errmsg:{$result['errmsg']}");
                return null;
            }
            if ($this->business_interface) $this->business_interface->cache_access_token($result);
            return $result['access_token'];
        }
        return null;
    }

    /**
     * 以get/post方式访问url地址并获得响应结果
     * @param $url
     * @param null $postFields
     * @return mixed
     */
    protected function curl_access_token($url, $postFields = NULL)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        //https 请求
        if (strlen($url) > 5 && strtolower(substr($url, 0, 5)) == 'https') {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        }

        if (is_array($postFields) && 0 < count($postFields)) {
            $postBodyString = '';
            $postMultipart = FALSE;
            foreach ($postFields as $k => $v) {
                if ('@' != substr($v, 0, 1)) //判断是不是文件上传
                {
                    $postBodyString .= "$k=" . urlencode($v) . "&";
                } else {
                    //文件上传用multipart/form-data，否则用www-form-urlencoded
                    $postMultipart = TRUE;
                }
            }
            $postFields = trim($postBodyString, '&');
            unset($k, $v);
            curl_setopt($ch, CURLOPT_POST, TRUE);
            if ($postMultipart) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
            } else {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
            }
        }

        $reponse = curl_exec($ch);
        curl_close($ch);
        return $reponse;
    }


    /**
     * 特殊的curl方法，该方法专用于微信接口访问并获取结果
     * 该方法适用于返回数据以json方式返回的微信接口
     * @param $url
     * @param array $data
     * @return mixed
     */
    protected function curl($url  , $data = array()){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $reponse = curl_exec($ch);
        curl_close($ch);
        $result =  json_decode($reponse , true);

        //因access_token失效导致访问失败，重新access_token再试一次
        /*if($result['errcode'] == 40001 || $result['errcode'] == 40014 ||$result['errcode'] == 42001){

        }*/
        return $result;
    }


    public function valid(){
        $rs = $_GET['echostr'];
        if ($this->check_signature()){
            echo $rs;
            exit;
        }else{
            if ($this->business_interface) $this->business_interface->log('valid fail. get data:'.json_encode($_GET));
            echo '';
            exit;
        }
    }


    /**
     * 检查微信消息回调是否正确
     * @return bool
     * @throws \think\Exception
     */
    private function check_signature(){
        if (!isset($this->config['token']) || empty($this->config['token'])) throw new \think\Exception('token is not defined!');
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $tmp = [$this->config['token'],$timestamp,$nonce];
        sort($tmp,SORT_STRING);
        $tmp_str = implode($tmp);
        $tmp_str = sha1($tmp_str);
        if ($tmp_str == $signature) return true;
        return false;
    }


    /**
     * 产生随机字符串
     * @param int $len 长度，默认32位
     * @return string
     */
    public function get_nonce_Str($len = 32)
    {
        $str = null;
        $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($strPol) - 1;
        for ($i = 0; $i < $len; $i++) {
            $str .= $strPol[rand(0, $max)];
        }
        return $str;
    }


    /**
     * 获取客户端IP
     * @return string <string, unknown>
     */
    function client_ip(){
        if (isset($_SERVER)) {
            if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
                $real_ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
            } else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
                $real_ip = $_SERVER["HTTP_CLIENT_IP"];
            } else {
                $real_ip = $_SERVER["REMOTE_ADDR"];
            }
        } else {
            if (getenv("HTTP_X_FORWARDED_FOR")) {
                $real_ip = getenv("HTTP_X_FORWARDED_FOR");
            } else if (getenv("HTTP_CLIENT_IP")) {
                $real_ip = getenv("HTTP_CLIENT_IP");
            } else {
                $real_ip = getenv("REMOTE_ADDR");
            }
        }
        return $real_ip;
    }


    /**
     * 输出xml字符
     * @param $data
     * @return string
     * @throws Exception
     */
    public function to_xml($data){
        if(!is_array($data) || count($data) <= 0) throw new Exception('xml data exception!');
        $xml = "<xml>";
        foreach ($data as $key=>$val){
            if (is_numeric($val)){
                $xml.="<".$key.">".$val."</".$key.">";
            }else{
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
            }
        }
        $xml.="</xml>";
        return $xml;
    }


    /**
     * 生成签名
     * @param $data
     * @return string   签名，本函数不覆盖sign成员变量，如要设置签名需要调用SetSign方法赋值
     */
    public function make_sign($data){
        /*去空*/
        $data=array_filter($data);
        /*签名步骤一：按字典序排序参数*/
        ksort($data);
        $string_a=http_build_query($data);
        $string_a=urldecode($string_a);

        /*签名步骤二：在string后加入KEY*/
        $config=$this->config;

        $string_sign_temp=$string_a."&key=".$config['key'];
        /*签名步骤三：MD5加密*/
        $sign = md5($string_sign_temp);
        /*签名步骤四：所有字符转为大写*/
        $result=strtoupper($sign);
        return $result;
    }


    /**
     * 将xml转为array
     * @param  string $xml xml字符串
     * @return array       转换得到的数组
     */
    public function to_array($xml){
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $result= json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $result;
    }


    /**
     * 验证签名
     * @return array|bool
     */
    public function check_notify(){
        /*获取xml*/
        $xml=file_get_contents('php://input', 'r');
        /*转成php数组*/
        $data=$this->to_array($xml);
        /*保存原sign*/
        $data_sign=$data['sign'];
        /*sign不参与签名*/
        unset($data['sign']);
        $sign=$this->make_sign($data);

        /*判断签名是否正确  判断支付状态*/
        if ($sign===$data_sign && $data['return_code']=='SUCCESS' && $data['result_code']=='SUCCESS') {
            $result=$data;
        }else{
            $result=false;
        }
        // 返回状态给微信服务器,该动作转由外部控制
        /*if ($result) {
            $str='<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
        }else{
            $str='<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[签名失败]]></return_msg></xml>';
        }
        echo $str;*/
        return $result;
    }

    /**
     * 处理用户昵称中的emoji表情
     * @param $str
     * @return mixed
     */
    public static function filter_emoji($str){
        $str = preg_replace_callback(
            '/./u',
            function (array $match) {
                return strlen($match[0]) >= 4 ? '' : $match[0];
            },
            $str);
        return $str;
    }


}