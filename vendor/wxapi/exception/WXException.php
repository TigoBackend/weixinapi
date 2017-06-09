<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/12/29
 * Time: 9:11
 */


class WXException extends \Exception
{
    const EXCEPTION_LEVEL_LOG = 1;
    const EXCEPTION_LEVEL_ROLLBACK = 2;
    const EXCEPTION_LEVEL_NORMAL = 0;

    public $msg;
    public $status;
    public $level;

    public function __construct($message = "", $status = array(),$level = 0 , $code = 0, \Exception $previous = null)
    {

        if(isset($status['status'])) $code = $status['status'];
        if(isset($status['msg'])){
            $message = $status['msg'];
        }

        parent::__construct($message, $code, $previous);
        $this -> status = $status;
        $this -> level = $level;
        $this -> msg = $message;

        //写入到Expcetion父类的字段中
        if(isset($status['status'])) $this->code = $status['status'];
        if(isset($status['msg'])){
            $this->message = $status['msg'];
        }
        if(!empty($message)){
            $this->message = $message;
        }

    }


    /**
     * 获取异常信息
     * @return string
     */
    final public function getMyMessage()
    {
        $msg = "\n" . "check in:" . date('Y-m-d H:i:s', time()) . "\n";
        $msg .= "MSG:" . $this->getMessage() . "\n";
        $msg .= "STATUS:" . $this-> getStatus()['status'] . "\n";
        $msg .= "STATUS MSG:" . $this-> getStatus()['msg'] . "\n";
        $msg .= "FILE:" . $this->getFile() . "\n";
        $msg .= "LINE:" . $this->getLine() . "\n";
        $msg .= "Trace:\n" . $this->getTraceAsString() . "\n";
        $this->msg = $msg;
        return $this->msg;
    }

    final public function getStatus(){
        return $this -> status;
    }

    final public function getLevel(){
        return $this -> level;
    }

}