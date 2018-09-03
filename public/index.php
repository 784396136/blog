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
function route(){
    // 获取url
    $url = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/';
    //定义默认控制器和方法
    $defaultController = 'IndexController';
    $dataultAction = 'index';
    if($url=='/'){
        return [
            $defaultController,
            $dataultAction
        ];
    }else if(strpos($url,'/',1) !==false){
        $url = ltrim($url,'/');
        $route = explode('/',$url);
        // 格式化控制器名称
        $route[0] = ucfirst($route[0]).'Controller';
        return $route;
    }else{
        die('请求的url地址不正确');
    }
}
$route = route();

// 任务分发到控制器
$controller = "controllers\\{$route[0]}";
$active = $route[1];
$_C = new $controller;
$_C->$active();