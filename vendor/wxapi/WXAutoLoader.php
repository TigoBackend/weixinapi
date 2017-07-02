<?php

/**
 * Created by PhpStorm.
 * User: ford
 * Date: 2017/2/19
 * Time: 1:23
 */
class WXAutoLoader
{

    public static function autoload($class){
        $name = $class;
        if (false !== strpos($name,'\\')) $name = strstr($class,'\\',true);

        $filename = WX_API_PATH."/api/$name.php";
        if (is_file($filename)){
            include $filename;
            return;
        }

        $filename = WX_API_PATH."/exception/$name.php";
        if (is_file($filename)){
            include $filename;
            return;
        }

        $filename = WX_API_PATH."/Interface/$name.php";
        if (is_file($filename)){
            include $filename;
            return;
        }

        $filename = WX_API_PATH."/msghandler/$name.php";
        if (is_file($filename)){
            include $filename;
            return;
        }

        $filename = WX_API_PATH."/msg/$name.php";
        if (is_file($filename)){
            include $filename;
            return;
        }
    }

}


spl_autoload_register('WXAutoLoader::autoload');
?>