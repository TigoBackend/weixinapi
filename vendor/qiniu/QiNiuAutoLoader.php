<?php

/**
 * Created by PhpStorm.
 * User: ford
 * Date: 2017/8/4
 * Time: 15:46
 */
class QiNiuAutoLoader
{

    public static function autoload($class){
        $name = $class;
        if (false !== strpos($name,'\\')) $name = strstr($class,'\\',true);

        $filename = QI_NIU_API_PATH."/$name.php";
        if (is_file($filename)){
            include $filename;
            return;
        }

        $filename = QI_NIU_API_PATH."/Http/$name.php";
        if (is_file($filename)){
            include $filename;
            return;
        }

        $filename = QI_NIU_API_PATH."/Processing/$name.php";
        if (is_file($filename)){
            include $filename;
            return;
        }

        $filename = QI_NIU_API_PATH."/Storage/$name.php";
        if (is_file($filename)){
            include $filename;
            return;
        }


    }

}

spl_autoload_register('QiNiuAutoLoader::autoload');

require_once QI_NIU_API_PATH.'/functions.php';

?>