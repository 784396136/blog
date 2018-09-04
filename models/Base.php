<?php
namespace models;
use PDO;

class Base
{
    public static $pdo = null;

    public function __construct()
    {
        if(self::$pdo === null)
        {
            $config = config('db');
            // 生成pdo对象,连接数据库
            self::$pdo = new PDO('mysql:dbname='.$config['dbname'].';host='.$config['host'],$config['user'],$config['pass']);
            // 设置编码
            self::$pdo->exec('set names '.$config['charset']);
        }
    }
}

