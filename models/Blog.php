<?php
namespace models;
use PDO;

class Blog extends Base
{

    public function search()
    {
        $stmt = self::$pdo->query('SELECT * FROM blogs');
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data;
    }
}