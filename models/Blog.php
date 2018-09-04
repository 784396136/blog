<?php
namespace models;
use PDO;

class Blog
{
    public $pdo;
    public function __construct()
    {
        // 取日志的数据
        $this->pdo = new PDO('mysql:host=127.0.0.1;dbname=blog', 'root', '');
        $this->pdo->exec('SET NAMES utf8');
    }

    public function search()
    {
        $stmt = $this->pdo->query('SELECT * FROM blogs');
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data;
    }
}