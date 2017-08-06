<?php
/**
 * Created by PhpStorm.
 * User: ford
 * Date: 2017/8/4
 * Time: 17:24
 */

namespace app\common\controller;


use think\Controller;
use think\Request;

class ApiBase extends Controller
{


    function __construct(Request $request) {
        parent::__construct($request);
        header("Content-Type: text/html; charset=utf-8");
        header('Access-Control-Allow-Origin:*');
        header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS");
        header('Access-Control-Max-Age:600');
        header('Access-Control-Allow-Headers: X-Requested-With, Content-Type, Accept');

        define('MODULE_NAME', $request->module());
        define('CONTROLLER_NAME', $request->controller());
        define('ACTION_NAME', $request->action());
    }

    function _initialize(){

    }


    /**
     * 接口返回正常访问
     * @param array $data
     * @param int $code
     * @param string $msg
     */
    public function show_true_json($data=[],$code = 100, $msg = '请求成功'){
        $result = [
            "data" => empty($data) ? [] : $data,
            "status" => true,
            "msg" => $msg,
            "code" => $code
        ];
        header('Content-Type:application/json; charset=utf-8');
        exit(json_encode($result));
    }


    /**
     * 接口返回失败访问
     * @param $msg
     * @param int $code
     */
    public function show_false_json($msg='系统异常',$code = 999){
        $result = [
            "status"=>false,
            "code"=>$code,
            "msg"=>$msg,
        ];
        header('Content-Type:application/json; charset=utf-8');
        exit(json_encode($result));
    }

}