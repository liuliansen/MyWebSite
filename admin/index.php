<?php
namespace LSCMS;
use includes\APP as APP;

//定义程序根路径
define('APP_PATH' , str_replace('\\', '/', __DIR__) .'/');

/* 注册类的自动载入方法
 * 类的文件名必须以 ".class.php"结尾。
 * 类中的namespace名必须和目录结构符合 */
spl_autoload_register(function($class){
    if(!class_exists($class)){     
        $class= APP_PATH . str_replace('\\', '/', $class).'.class.php';   
        require_once $class;
    }
});

//启动程序
try{
    (new APP())->start();
}catch(\Exception $e){
    die($e->getMessage());
}