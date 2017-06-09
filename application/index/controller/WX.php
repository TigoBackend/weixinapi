<?php
/**
 * Created by PhpStorm.
 * User: ford
 * Date: 2017/6/4
 * Time: 1:08
 */

namespace app\index\controller;


use think\Request;
use wx\InterfaceMsg;
use wx\InterfaceWXCommon;

class WX
{

    public function index(){

        return ['name'=>'name'];
    }

    /**
     * 以接口的形式提供网页授权功能
     *
     */
    public function wx_auth(){
        try{
            $params = input('param.');
            $wx_config = config('wx_config');
            vendor('wxapi.index');
            $interface = new InterfaceWXCommon();
            $api = new \WeixinApi($wx_config,$interface);
            if (isset($params['callback']) && $params['callback']){     /*自带callback参数即我们的前端主动跳转到当前接口请求微信授权,以微信重定向到当前接口地址的形式请求code*/
                $redirect_url = url('index/WX/wx_auth','',false,true);
                /*scope='snsapi_userinfo'形式获取code*/
                $api ->get_code($redirect_url,$params['callback'],\WeixinApi::SCOPE_TYPE_USER_INFO,\WeixinApi::PROCESS_RETURN);
                /*scope='snsapi_base'形式获取code*/
                /*$api ->get_code($redirect_url,$params['callback'],\WeixinApi::SCOPE_TYPE_BASE,\WeixinApi::PROCESS_REDIRECT);*/
            }elseif (isset($params['code']) && isset($params['state']) && $params['code'] && $params['state']){       /*自带code及state参数即请求code后微信服务器重定向到当前地址*/
                /*获取用户信息*/
                $user_info = $api -> get_user_info($params['code']);
                if ($user_info){
                    /*拉取到微信用户信息后在这里可以进行应用自己的业务操作如刷新自己项目里面的微信用户信息等操作*/

                    /*最后将微信用户信息以cookie形式保存在cookie中并且指定域名为前端工程与接口工程共用的一级域名以便最后跳转到前端页面时可以获取到微信用户信息*/
                    $nick_name = $api::filter_emoji($user_info['nickname']);        /*过滤掉微信昵称中的emoji表情图片*/
                    cookie('nickname',$nick_name,['expire'=>'3600','domain'=>'tigonenetwork.com']);
                    cookie('open_id',$user_info['openid'],['expire'=>'3600','domain'=>'tigonenetwork.com']);
                    cookie('sex',$user_info['sex'],['expire'=>'3600','domain'=>'tigonenetwork.com']);
                    cookie('headimgurl',$user_info['headimgurl'],['expire'=>'3600','domain'=>'tigonenetwork.com']);

                    /*跳转到前端第一次请求授权时需要跳转的前端页面*/
                    echo "<script>";
                    echo "window.location.href='{$params['state']}'" ;
                    echo "</script>";
                }
            }else{
                echo 'login.....';
            }
        }catch (\Exception $e){
            /*捕捉到异常做自己的异常处理业务如:记录日志,回滚事务等*/
            echo "<script>";
            echo "window.location.href='".url('index/Index','',false,true)."'" ;
            echo "</script>";
        }
    }


    /**
     * 以接口的形式提供网页授权功能
     */
    public function wx_auth1(){
        try{
            $params = input('param.');
            $wx_config = config('wx_config');
            vendor('wxapi.index');
            $interface = new InterfaceWXCommon();
            $api = new \WeixinApi($wx_config,$interface);
            if (isset($params['callback']) && $params['callback']){     /*以微信重定向到当前接口地址的形式请求code*/
                /*scope='snsapi_userinfo'形式获取code*/
                $redirect_url = url('index/WX/handle_code','',false,true);
                $api ->get_code($redirect_url,$params['callback'],\WeixinApi::SCOPE_TYPE_USER_INFO,\WeixinApi::PROCESS_REDIRECT);
                /*scope='snsapi_base'形式获取code*/
                /*$api ->get_code($redirect_url,$params['callback'],\WeixinApi::SCOPE_TYPE_BASE,\WeixinApi::PROCESS_REDIRECT);*/
            }else{
                echo 'login.....';
                exit;
            }
        }catch (\Exception $e){
            /*捕捉到异常做自己的异常处理业务如:记录日志,回滚事务等*/
            echo "<script>";
            echo "window.location.href='".url('index/Index','',false,true)."'" ;
            echo "</script>";
        }
    }


    /**
     * 处理网页授权时的code重定向
     */
    public function handle_code(){
        try{
            $params = input('param.');
            if(isset($params['code']) && isset($params['state']) && $params['code'] && $params['state']) {       /*自带code及state参数即请求code后微信服务器重定向到当前地址*/
                $wx_config = config('wx_config');
                vendor('wxapi.index');
                $interface = new InterfaceWXCommon();
                $api = new \WeixinApi($wx_config,$interface);
                /*获取用户信息*/
                $user_info = $api -> get_user_info($params['code']);
                /*获取用户信息*/
                $user_info = $api -> get_user_info($params['code']);
                if ($user_info){
                    /*拉取到微信用户信息后在这里可以进行应用自己的业务操作如刷新自己项目里面的微信用户信息等操作*/

                    /*最后将微信用户信息以cookie形式保存在cookie中并且指定域名为前端工程与接口工程共用的一级域名以便最后跳转到前端页面时可以获取到微信用户信息*/
                    $nick_name = $api::filter_emoji($user_info['nickname']);        /*过滤掉微信昵称中的emoji表情图片*/
                    cookie('nickname',$nick_name,['expire'=>'3600','domain'=>'tigonenetwork.com']);
                    cookie('open_id',$user_info['openid'],['expire'=>'3600','domain'=>'tigonenetwork.com']);
                    cookie('sex',$user_info['sex'],['expire'=>'3600','domain'=>'tigonenetwork.com']);
                    cookie('headimgurl',$user_info['headimgurl'],['expire'=>'3600','domain'=>'tigonenetwork.com']);

                    /*跳转到前端第一次请求授权时需要跳转的前端页面*/
                    echo "<script>";
                    echo "window.location.href='{$params['state']}'" ;
                    echo "</script>";
                }
            }else{
                echo 'login.....';
                exit;
            }
        }catch (\Exception $e){
            /*捕捉到异常做自己的异常处理业务如:记录日志,回滚事务等*/
            echo "<script>";
            echo "window.location.href='".url('index/Index','',false,true)."'" ;
            echo "</script>";
        }
    }



    /**
     * 微信服务器在将用户的消息发给公众号的开发者服务器地址
     */
    public function msg_handle(){
        vendor('wxapi.index');
        if (Request::instance()->isGet()){
            $api = new \WeixinApi(config('wx_config'));
            $api -> valid();
        }else{
            $handler = new \WxMsgHandler(new InterfaceMsg());
            $handler -> handle();
        }
//        try{
//            vendor('wxapi.index');
//            if (Request::instance()->isGet()){
//                $api = new \WeixinApi(config('wx_config'));
//                $api -> valid();
//            }else{
//                $handler = new \WxMsgHandler(new InterfaceMsg());
//                $handler -> handle();
//            }
//
//        }catch (\Exception $e){
//            /*捕捉到异常做自己的异常处理业务如:记录日志,回滚事务等*/
//            echo '';
//        }
    }

    /**
     * 获取微信access_token
     * @return null|string
     */
    public function get_access_token(){
        try{
            vendor('wxapi.index');
//            $interface = new InterfaceWXCommon();
            $interface = new \InterfaceCommon();
            $api = new \WeixinApi(config('wx_config'),$interface);
            $rs = $api -> get_access_token();
            return ['access_token'=>$rs];
        }catch (\Exception $e){
            /*捕捉到异常做自己的异常处理业务如:记录日志,回滚事务等*/
            return ['status'=>false];
        }
    }

    /**
     * 获取微信服务器IP地址
     * @return array|mixed|null
     */
    public function get_callback_ip(){
        try{
            vendor('wxapi.index');
            $interface = new InterfaceWXCommon();
            $api = new \WeixinApi(config('wx_config'),$interface);
            $rs = $api -> get_callback_ip();
            return $rs;
        }catch (\Exception $e){
            /*捕捉到异常做自己的异常处理业务如:记录日志,回滚事务等*/
            return ['status'=>false];
        }
    }

    /**
     * 获取微信用户信息只能获取已关注的用户信息
     * @return array|mixed
     */
    public function get_subscribe_user_info(){
        try{
            vendor('wxapi.index');
            $interface = new InterfaceWXCommon();
            $api = new \WeixinApi(config('wx_config'),$interface);
            /*已关注用户*/
//            $rs = $api -> get_subscribe_user_info('o09KlwiCtHz1IN__67Rg-HhqqF1k');
            /*未关注用户*/
            $rs = $api -> get_subscribe_user_info('o09Klwua9nCGJY0k8VovQVahaM9M');
            return $rs;
        }catch (\Exception $e){
            /*捕捉到异常做自己的异常处理业务如:记录日志,回滚事务等*/
            return ['status'=>false];
        }
    }


    public function share_url(){
        try{
            vendor('wxapi.index');
            $interface = new InterfaceWXCommon();
            $api = new \WeixinApi(config('wx_config'),$interface);
            $rs = $api->share_url('www.baidu.com');
            return $rs;
        }catch (\Exception $e){
            /*捕捉到异常做自己的异常处理业务如:记录日志,回滚事务等*/
            return ['status'=>false];
        }
    }




}