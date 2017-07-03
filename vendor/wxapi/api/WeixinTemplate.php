<?php
/**
 * Created by PhpStorm.
 * User: ford
 * Date: 2017/2/6
 * Time: 17:34
 */



abstract class WeixinTemplate
{

    protected $wx_config;

    protected $touser;

    protected $template_id;

    protected $url;

    /*该属性已过期*/
    protected $topcolor;

    protected $data;

    protected $business_interface;


    /**
     * 消息模板构造器
     * WeixinTemplate constructor.
     * @param array $option 消息模板信息
     * @param InterfaceWxApi|null $interface  消息模板业务接口
     */
    function __construct($option,InterfaceWxApi $interface=null)
    {
        $this->wx_config = $option['wx_config'];
        $this->touser = $option['open_id'];
        $this->url = $option['url'];
        $this->data = $option['data'];
        $this->business_interface = $interface;
    }

    /**
     * 设置接收消息的用户open_id
     * @param string $open_id
     */
    public function set_target_open_id($open_id){
        if ($open_id && is_string($open_id)) $this->touser = $open_id;
    }


    /**
     * 设置消息模板点击后的跳转地址，URL置空，则在发送后，点击模板消息会进入一个空白页面（ios），或无法点击（android）
     * @param $url
     */
    public function set_redirect_url($url){
        if ($url && is_string($url)) $this->url = $url;
    }


    /**
     * 设置顶部颜色格式为"#FFFFFF"的字符串
     * @param string $color
     */
    /*public function set_top_color($color){
        if ($color && is_string($color)) $this->topcolor = $color;
    }*/

    /**
     * 设置模板id
     * @param string $template_id
     */
    public function set_template_id($template_id){
        if ($template_id && is_string($template_id)) $this->template_id = $template_id;
    }


    /**
     * 设置消息模板的内容
     * 因为每种模板的属性都不一样，子类需实现该方法。根据微信模板自定义字段
     * @param $data
     */
    abstract function setData($data);

    /**
     * 获取模板数据
     * @return null
     */
    public function getData(){
        if(empty($this -> data)){
            return null;
        }else{
            return $this -> data;
        }
    }


    /**
     * 封装发送到微信服务器的消息模板请求数据
     * @return array
     */
    public function get_params(){
        return array(
            "touser"=>$this -> touser,
            "template_id"=>$this -> template_id,
            "url"=>$this -> url,
            "data" => $this -> data
        );
    }

    /**
     * 向微信服务器发送信息模板请求
     * @return bool|mixed
     */
    public function sendTemplateMsg(){
        $templateApi = new WeixinMsgTemplateApi($this->wx_config,$this->business_interface);
        $result = $templateApi -> sendTemplateMsg($this);
        return $result;
    }


}