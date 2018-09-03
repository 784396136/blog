<?php
namespace models;
use PDO;
class BaseModel{
    private static $_pdo = null;
    private $_dbname = 'blog';
    private $_host = '127.0.0.1';
    private $_user = 'root';
    private $_pwd = '';

    public function __construct()
    {
        if(self::$_pdo===null)
        {
            // 生成pdo对象,连接数据库
            self::$_pdo = new PDO('mysql:dbname='.$this->_dbname.';host='.$this->_host,$this->_user,$this->_pwd);
            // 设置编码
            self::$_pdo->exec('set names utf8');
        }
    }
}