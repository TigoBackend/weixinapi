<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

/**
 * 校验引用数组params中是否有keys中对应的key的值并且不为empty
 * @param $params
 * @param null $keys
 * @param int $exception_type
 * @param string $handle_type
 * @return bool
 * @throws Exception
 */
function check_params($params,$keys = null,$exception_type = 1,$handle_type="THROW"){
    $result = true;
    if(empty($params)){
        $result = false;
    } else if(!empty($keys) && is_array($keys)){
        foreach ($keys as $key){
            if(empty($params[$key])){
                $result = false;
                break;
            }
        }
    }else if(!empty($keys)){
        if(empty($params[$keys])) $result = false;
    }
    /*根据处理方法执行操作*/
    if(empty($result)){
        switch ($handle_type){
            case 'RETURN':
                return $result;
            case 'THROW':
            default:
                switch ($exception_type){
                    case 2:
                        throw_my_exception('参数错误',null,999,\app\common\lib\exception\MyException::EXCEPTION_LEVEL_ROLLBACK);
                        break;
                    case 1:
                    default:
                        throw_exception();
                }
        }
    }else{
        return true;
    }
}


/**
 * 抛出自定义异常
 * @param string $msg
 * @param null $data
 * @param int $code
 * @param int $level
 * @throws \app\common\lib\exception\MyException
 */
function throw_my_exception($msg = '异常',$data=null,$code=108,$level = \app\common\lib\exception\MyException::EXCEPTION_LEVEL_NORMAL){
    throw new \app\common\lib\exception\MyException($msg,['code'=>$code,'msg'=>$msg,'data'=>empty($data)?[]:$data],$level);
}

/**
 * 抛出系统级别异常
 * @param string $msg
 * @throws Exception
 */
function throw_exception($msg = '系统异常'){
    throw new Exception($msg);
}


/**
 *截取数据中指定key的的值
 * 可用于移除接口中多余的传入参数,只截取接口规定的参数
 * @param array $data 要过滤的引用数组
 * @param array $keys 要截取的key索引数组
 * @return array
 */
function extract_data_by_keys($data=null, $keys=null)
{
    if (empty($data)) return null;
    if (empty($keys)) return $data;
    if (!is_array($data)) {
        return $data;
    } else {
        if (!is_array($keys)) {
            return isset($data[$keys])?$data[$keys]:"";
        } else {
            $result = array();
            foreach ($keys as $key) {
                if (isset($data[$key]) && !isset($result[$key])) $result[$key] = $data[$key];
            }
            unset($data);
            return $result;
        }
    }
}



/**
 * 根据异常记录信息日志
 * @param Exception $e
 * @param string $folder 要保存日志的文件夹路径
 */
function handle_exception(Exception $e,$folder='public/log/exception/'){
    if (!$folder)$folder = 'public/Log/Exception/';
    if ($e instanceof \app\common\lib\exception\MyException && ($e->getLevel() >= \app\common\lib\exception\MyException::EXCEPTION_LEVEL_LOG || \think\Config::get('app_debug'))) {
        \think\Log::init(['type' => 'File', 'path' => ROOT_PATH . $folder, 'file_size' => 2097152, 'time_format' => 'c']);
        \think\Log::record($e->getMyMessage());
    } elseif ($e instanceof Exception) {
        \think\Log::init(['type' => 'File', 'path' => ROOT_PATH . $folder, 'file_size' => 2097152, 'time_format' => 'c']);
        \think\Log::record($e->getTraceAsString());
        \think\Log::record($e->getMessage());
    }
}

/**
 * 记录异常信息到指定目录
 * @param string $msg
 * @param string $folder
 */
function add_log($msg='',$folder='public/log/exception/'){
    if ($msg){
        \think\Log::init(['type' => 'File', 'path' => ROOT_PATH . $folder, 'file_size' => 2097152, 'time_format' => 'c']);
        \think\Log::record($msg);
    }
}



/**
 * base64图片保存到临时目录
 * @param $base64
 * @param string $upload_path
 * @return array
 */
function base64_img_save($base64){
    if (empty($base64)){
        return array('status'=>-1,'msg'=>'图片上传错误');
    }
    $upload_path = './source/temp/';
    $base64_head = substr(strstr($base64,';',1),11);
    $base64_body = substr(strstr($base64,','),1);
    switch ($base64_head){
        case 'jpeg':
        case 'jpg':
            $suffix = '.jpg';
            break;
        case 'png':
            $suffix = '.png';
            break;
        case 'gif':
            $suffix = '.gif';
            break;
        default:
            return array('status'=>-1,'msg'=>'图片类型异常');
    }
    $img_name = date('YmdHis')."_".rand(0,9999999);
    $file_name = "{$img_name}{$suffix}";
    $path = "{$upload_path}{$file_name}";
    /*判断能否上传*/
    if (!is_dir($upload_path)){
        if (!mkdir($upload_path,0777,true)){
            return array('status'=>-1,'msg'=>'文件上传目录不存在并且无法创建目录');
        }elseif(!chmod($upload_path,0755)){
            return array('status'=>-1,'msg'=>'文件上传目录权限无法设置为可读可写');
        }
    }
    $img = base64_decode($base64_body);
    $a = file_put_contents($path, $img);//返回的是字节
    if($a>0){
        return array('status'=>1,'msg'=>$file_name);
    }
    else{
        return array('status'=>-1,'msg'=>'上传异常');
    }
}