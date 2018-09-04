<?php
// 定义常量 根目录绝对路径
define('ROOT',dirname(__FILE__).'/../');

// 自动加载
function autoLoadClass($class){
    require_once ROOT. str_replace('\\','/',$class).".php";
}
spl_autoload_register('autoLoadClass');

// view 函数
function view($file,$data=[]){
    if($data){
        extract($data);
    }
    require_once ROOT . 'views/'. str_replace('.','/',$file) . '.html';
}

    // 解析路由
    // 获取url
    if( isset($_SERVER['PATH_INFO']) )
    {
        $pathInfo = $_SERVER['PATH_INFO'];
        // 根据 / 转成数组
        $pathInfo = explode('/', $pathInfo);

        // 得到控制器名和方法名 ：
        $controller = ucfirst($pathInfo[1]) . 'Controller';
        $action = $pathInfo[2];
    }
    else
    {
        // 默认控制器和方法
        $controller = 'IndexController';
        $action = 'index';
    }


// 任务分发到控制器
$controller = 'controllers\\'.$controller;
$_C = new $controller;
$_C->$action();