<?php
namespace models;

class User extends Base
{
    function  getName(){
        return 'tom';
    }

    // 注册
    public function add($email,$pwd)
    {
        $stmt = self::$pdo->prepare("INSERT INTO users (email,password) VALUES(?,?)");
        return $stmt->execute([
            $email,
            $pwd,
        ]);
    }
}