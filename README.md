借助ThinkPHP 5.0实现一个微信API demo
===============

## 目录结构
为了方便微信项目中的接口开发需求,借助ThinkPHP5.0写了一个微信API库,本工程中的目录结构与ThinkPHP5完全一样,
微信API扩展库位于以下第三方类库目录中,库目录如下:
~~~
wxapidemo       项目根目录
├─vendor                第三方类库目录
    ├─wxapi             微信扩展库
        ├─api           微信接口类目录
        ├─exception     异常目录
        │    └─WXException.php      微信异常类
        ├─Interface     业务接口目录
        │    ├─InterfaceMsgHandler.php      微信消息处理业务接口
        │    ├─InterfacePay.php             微信支付业务接口
        │    ├─InterfaceWxApi.php           微信业务接口
        │    └─InterfaceWxApiCommon.php     预先实现的一个基于TP5用法的InterfaceWxApi的实现类,用户如果不希望实现多余的微信api业务接口可以直接使用该接口类,该类已经实现(缓存access_token、js_api_ticket、记录日志等)基础业务
        ├─msg
        │    ├─WxMsg.php            微信消息基础类
        │    ├─WxMsgClick.php       用户点击自定义菜单后拉取消息时的消息类
        │    ├─WxMsgLocation.php    微信上报地理未知消息类
        │    ├─WxMsgScan.php        已关注用户扫描带参数二维码消息类
        │    ├─WxMsgSubscribe.php   关注、取消关注消息类
        │    ├─WxMsgText.php        文本消息类
        │    ├─WxMsgView.php        点击菜单跳转链接时的消息类
        │    ├─WxRespMessage.php    微信消息推送回复基础类
        │    └─WxRespTextMessage.php    微信消息推送文本回复类
        ├─msghandler
        │    └─WxMsgHandler.php     微信消息推送处理类(用于微信消息推送处理接口中处理推送消息)
        │
        ├─index.php             扩展库入口文件使用扩展库前需要 include_once、include本文件或使用TP5内置函数vendor('wxapi.index')
        └─WXAutoLoader.php      扩展库类加载器

~~~

 ## 本项目已接口的形式实现了以下功能

 本项目将各种微信功能放于WX、WXPay两个控制器中

+ 微信服务器消息推送处理接口
+ 微信网页授权
+ 获取微信access_token
+ 获取微信服务器IP地址
+ 获取微信用户信息
+ 微信分享签名
+ 拉取微信公众号菜单
+ 发布微信公众号菜单
+ 获取微信支付签名数据
+ 直接后台调起微信支付
+ 微信支付回调
+ 企业转账
+ 微信红包

具体功能实现查看route.php中的路由配置查找对应控制器代码