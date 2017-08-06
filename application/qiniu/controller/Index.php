<?php
/**
 * Created by PhpStorm.
 * User: ford
 * Date: 2017/8/4
 * Time: 16:59
 */

namespace app\qiniu\controller;


use app\common\controller\ApiBase;
use app\common\lib\exception\MyException;

class Index extends ApiBase
{


    /**
     * 获取文件的状态信息
     */
    public function file_status(){
        try{
            $params = extract_data_by_keys(input('param.'),['bucket','key']);
            check_params($params,['bucket','key'],2);
            vendor('qiniu.index');
            $config = config('qn_config');
            $auth = new \Auth($config['access_key'],$config['secret_key']);
            $bucket_mgr = new \BucketManager($auth);
            list($result,$err) = $bucket_mgr->stat($params['bucket'],$params['key']);
            if ($err !== null){
                dump($err);
            }else{
                dump($result);
            }
        }catch (MyException $e){
            handle_exception($e);
            $this->show_false_json($e->getStatus()['msg'],$e->getStatus()['code']);
        }catch (\Exception $e){
            handle_exception($e);
            $this->show_false_json($e->getMessage());
        }
    }




    /**
     * 将文件从文件$key 复制到文件$key2。 可以在不同bucket复制
     */
    public function file_copy(){
        try{
            $params = extract_data_by_keys(input('param.'),['bucket_from','bucket_to','key_from','key_to']);
            check_params($params,['bucket_from','bucket_to','key_from','key_to'],2);
            vendor('qiniu.index');
            $config = config('qn_config');
            $auth = new \Auth($config['access_key'],$config['secret_key']);
            $bucket_mgr = new \BucketManager($auth);
            $rs = $bucket_mgr->copy($params['bucket_from'],$params['key_from'],$params['bucket_to'],$params['key_to']);
            if ($rs !== null){
                dump($rs);
            }else{
                $this->show_true_json('Success!');
            }
        }catch (MyException $e){
            handle_exception($e);
            $this->show_false_json($e->getStatus()['msg'],$e->getStatus()['code']);
        }catch (\Exception $e){
            handle_exception($e);
            $this->show_false_json($e->getMessage());
        }
    }


    /**
     * 将文件从文件$key2 移动到文件$key3。 可以在不同bucket移动
     */
    public function file_move(){
        try{
            $params = extract_data_by_keys(input('param.'),['bucket_from','bucket_to','key_from','key_to']);
            check_params($params,['bucket_from','bucket_to','key_from','key_to'],2);
            vendor('qiniu.index');
            $config = config('qn_config');
            $auth = new \Auth($config['access_key'],$config['secret_key']);
            $bucket_mgr = new \BucketManager($auth);
            $rs = $bucket_mgr->move($params['bucket_from'],$params['key_from'],$params['bucket_to'],$params['key_to']);
            if ($rs !== null){
                dump($rs);
            }else{
                $this->show_true_json('Success!');
            }
        }catch (MyException $e){
            handle_exception($e);
            $this->show_false_json($e->getStatus()['msg'],$e->getStatus()['code']);
        }catch (\Exception $e){
            handle_exception($e);
            $this->show_false_json($e->getMessage());
        }
    }


    /**
     * 删除$bucket 中的文件 $key
     */
    public function file_delete(){
        try{
            $params = extract_data_by_keys(input('param.'),['bucket','key']);
            check_params($params,['bucket','key'],2);
            vendor('qiniu.index');
            $config = config('qn_config');
            $auth = new \Auth($config['access_key'],$config['secret_key']);
            $bucket_mgr = new \BucketManager($auth);
            $rs = $bucket_mgr->delete($params['bucket'],$params['key']);
            if ($rs !== null){
                dump($rs);
            }else{
                $this->show_true_json('Success!');
            }
        }catch (MyException $e){
            handle_exception($e);
            $this->show_false_json($e->getStatus()['msg'],$e->getStatus()['code']);
        }catch (\Exception $e){
            handle_exception($e);
            $this->show_false_json($e->getMessage());
        }
    }


    /**
     * 抓取网络资源到存储空间
     */
    public function file_fetch(){
        try{
            $params = extract_data_by_keys(input('param.'),['bucket','key','url']);
            check_params($params,['bucket','key','url'],2);
            vendor('qiniu.index');
            $config = config('qn_config');
            $auth = new \Auth($config['access_key'],$config['secret_key']);
            $bucket_mgr = new \BucketManager($auth);
            list($result,$err) = $bucket_mgr->fetch($params['url'],$params['bucket'],$params['key']);
            if ($err !== null){
                dump($err);
            }else{
                dump($result);
                $this->show_true_json('Success!');
            }
        }catch (MyException $e){
            handle_exception($e);
            $this->show_false_json($e->getStatus()['msg'],$e->getStatus()['code']);
        }catch (\Exception $e){
            handle_exception($e);
            $this->show_false_json($e->getMessage());
        }
    }


    /**
     * 为存储空间中的图片生成缩略图链接
     */
    public function thumbnail(){
        try{
            $params = extract_data_by_keys(input('param.'),['url']);
            check_params($params,['url'],2);
            vendor('qiniu.index');
            $img_builder = new \ImageUrlBuilder();
            $thumb_url = $img_builder->thumbnail($params['url'],1,100,100);
            $this->show_true_json($thumb_url);
        }catch (MyException $e){
            handle_exception($e);
            $this->show_false_json($e->getStatus()['msg'],$e->getStatus()['code']);
        }catch (\Exception $e){
            handle_exception($e);
            $this->show_false_json($e->getMessage());
        }
    }


    /**
     * 为存储空间中的图片生成带图片水印图片链接
     */
    public function water_img(){
        try{
            $params = extract_data_by_keys(input('param.'),['url','water_url']);
            check_params($params,['url','water_url'],2);
            vendor('qiniu.index');
            $img_builder = new \ImageUrlBuilder();
            $water_url = $img_builder->waterImg($params['url'],$params['water_url']);
            $this->show_true_json($water_url);
        }catch (MyException $e){
            handle_exception($e);
            $this->show_false_json($e->getStatus()['msg'],$e->getStatus()['code']);
        }catch (\Exception $e){
            handle_exception($e);
            $this->show_false_json($e->getMessage());
        }
    }


    /**
     * 为存储空间中的图片生成带文字水印图片链接
     */
    public function water_text(){
        try{
            $params = extract_data_by_keys(input('param.'),['url','text']);
            check_params($params,['url'],2);
            vendor('qiniu.index');
            $img_builder = new \ImageUrlBuilder();
            $water_url = $img_builder->waterText($params['url'],$params['text'],'微软雅黑',500,'#FF0000');
            $this->show_true_json($water_url);
        }catch (MyException $e){
            handle_exception($e);
            $this->show_false_json($e->getStatus()['msg'],$e->getStatus()['code']);
        }catch (\Exception $e){
            handle_exception($e);
            $this->show_false_json($e->getMessage());
        }
    }


    /**
     * 通过base64形式上传图片,本地保留一份并上传一份到储存空间返回储存空间的链接给前端
     */
    public function upload_file(){
        try{
            $params = extract_data_by_keys(input('param.'),['base64','bucket']);
            check_params($params,['base64','bucket'],2);
            $file_name = base64_img_save($params['base64']);
            vendor('qiniu.index');
            $config = config('qn_config');
            $auth = new \Auth($config['access_key'],$config['secret_key']);
            $policy = [
                'callbackUrl'=>'http://172.30.251.210/callback.php',    //上传成功回调接口
                'callbackBody'=>'filename=$(fname)&filesize=$(fsize)',  //回调传参
//                'callbackBodyType' => 'application/json',               //指定以json形式回调传参
//                'callbackBody' => '{"filename":$(fname), "filesize": $(fsize)}'  //设置application/json格式回调
            ];
//            $token = $auth->uploadToken($params['bucket'],null,3600,$policy); //指定上传回调形式上传
            $token = $auth->uploadToken($params['bucket'],null,3600);
            $upload_mgr = new \UploadManager();

            list($result,$err) = $upload_mgr->putFile($token,$file_name['msg'],"./source/temp/{$file_name['msg']}");
            if ($err !== null){
                dump($err);
            }else{
                $this->show_true_json("http://ou3re7cj0.bkt.clouddn.com/{$result['key']}");
            }
        }catch (MyException $e){
            handle_exception($e);
            $this->show_false_json($e->getStatus()['msg'],$e->getStatus()['code']);
        }catch (\Exception $e){
            handle_exception($e);
            $this->show_false_json($e->getMessage());
        }
    }







    
    
    
    
    


}