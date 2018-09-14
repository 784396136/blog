<?php
namespace models;
use PDO;

class User extends Base
{
    function  getName(){
        return 'tom';
    }

    // 更新用户余额
    public function addMoney($money,$userId)
    {
        $stmt = self::$pdo->prepare("UPDATE users set money = money+? WHERE id = ?");
        return $stmt->execute([
            $money,
            $userId,
            ]);

    }

    public function minusMoney($money,$userId)
    {
        $stmt = self::$pdo->prepare("UPDATE users set money = money-? WHERE id = ?");
        return $stmt->execute([
            $money,
            $userId,
            ]);
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
            $_SESSION['money'] = $user['money'];
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