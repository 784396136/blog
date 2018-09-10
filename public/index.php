<?php
// 设置 SESSION 保存
ini_set('session.save_handler', 'redis');   // 使用 redis 保存 SESSION
ini_set('session.save_path', 'tcp://127.0.0.1:6379?database=1');  // 设置 redis 服务器的地址、端口、使用的数据库
ini_set('session.gc_maxlifetime', 600);   // 设置 SESSION 10分钟过期

// 开启 SESSION
session_start();

// 定义常量 根目录绝对路径
define('ROOT',dirname(__FILE__).'/../');
require(ROOT.'vendor/autoload.php');
// 自动加载
function autoLoadClass($class){
    require_once ROOT. str_replace('\\','/',$class).".php";
}
spl_autoload_register('autoLoadClass');

// view 函数
function view($file,$data=[])
{
    if($data){
        extract($data);
    }
    require ROOT . 'views/'. str_replace('.','/',$file) . '.html';
}

    // 解析路由
    // 获取url
    if(php_sapi_name() == 'cli')
    {
        $controller = ucfirst($argv[1]) . 'Controller';
        $action = $argv[2];
    }
    else
    {
        if( isset($_SERVER['PATH_INFO']) )
        {
            $pathInfo = $_SERVER['PATH_INFO'];
            // 根据 / 转成数组
            $pathInfo = explode('/', $pathInfo);

            // 得到控制器名和方法名 ：
            $controller = ucfirst($pathInfo[1]) . 'Controller';
            if(@$pathInfo[2]=='')
            {
                $action = 'index';
            }
            else
            {
                $action = $pathInfo[2];
            }
            
        }
        else
        {
            // 默认控制器和方法
            $controller = 'IndexController';
            $action = 'index';
        }
    }
    


// 任务分发到控制器
$controller = 'controllers\\'.$controller;
$_C = new $controller;
$_C->$action();

// 获取配置文件
function config($name)
{
    static $config = null;
    if($config === null)
    {
        $config = require ROOT . 'config.php';
    }
    return $config[$name];
}

// 保存搜索参数
function getUrl($except = [])
{
    $ret = '';

    foreach($_GET as $k=>$v)
    {
        if(!in_array($k,$except))
        {
            $ret .= "&$k=$v";
        }
        
    }
    return $ret;
}