<?php
namespace models;
use PDO;

class User extends Base
{
    function  getName(){
        return 'tom';
    }

    // 登录
    public function login($email,$pwd)
    {
        $stmt = self::$pdo->prepare("SELECT * FROM users WHERE email=? AND password=?");
        $stmt -> execute([
            $email,
            $pwd,
        ]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if($user)
        {
            $_SESSION['id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            return true;
        }
        else
        {
            return false;
        }
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