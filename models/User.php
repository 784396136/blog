<?php
namespace models;
use PDO;
use function GuzzleHttp\json_decode;

class User extends Base
{
    function  getName(){
        return 'tom';
    }

    // 计算活跃用户
    public function computerActiveUser()
    {
        // 取出一周内日志的分值
        $stmt = self::$pdo->query("SELECT user_id,COUNT(*)*5 fz
                                    FROM blogs 
                                    WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)
                                        GROUP BY user_id");
        $data1 = $stmt->fetchAll( PDO::FETCH_ASSOC );
        // 取出一周内评论的分值
        $stmt = self::$pdo->query("SELECT user_id,COUNT(*)*3 fz
                                    FROM comments 
                                    WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)
                                        GROUP BY user_id");
        $data2 = $stmt->fetchAll( PDO::FETCH_ASSOC );

        $arr = [];
        foreach($data1 as $v)
        {
            $arr[$v['user_id']] = $v['fz'];
        }

        foreach($data2 as $v)
        {
            if(isset($arr[$v['user_id']]))
                $arr[$v['user_id']] += $v['fz'];
            else
                $arr[$v['user_id']] = $v['fz'];
        }

        arsort($arr);

        $data = array_slice($arr,0,20,TRUE);

        $userIds = array_keys($data);

        $userIds = implode(',',$userIds);

        $sql = "SELECT id,email,avatar FROM users WHERE id IN ($userIds)";

        $stmt = self::$pdo->query($sql);

        $data = $stmt->fetchAll( PDO::FETCH_ASSOC );

        $redis = \libs\Redis::getInstance();

        $redis->set("active_users",json_encode($data));
    }

    // 获取活跃用户
    public function getActiveUsers()
    {
        $redis = \libs\Redis::getInstance();
        $data = $redis->get("active_users");
        return json_decode($data,TRUE);
    }

    // 设置头像
    public function setavatar($path)
    {
        $stmt = self::$pdo->prepare("UPDATE users SET avatar = ? WHERE id = ?");
        $stmt -> execute([
            $path,
            $_SESSION['id'],
        ]);
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

    public function getMoney()
    {
        $stmt = self::$pdo->prepare("SELECT money FROM users WHERE id=?");
        $stmt->execute([
            $_SESSION['id'],
        ]);
        $money = $stmt->fetch( PDO::FETCH_COLUMN );
        $_SESSION['money'] = $money;
        return $money;
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
            $_SESSION['avatar'] = $user['avatar'];
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