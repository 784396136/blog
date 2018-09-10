<?php
namespace controllers;
use models\User;

class UserController
{

    // 登录
    public function login()
    {
        view('user.login');
    }

    // 注册
    public function register()
    {
        // 显示视图
        view('user.add');
    }

    // 注册
    public function store()
    {
        // 接受表单信息
        $email = $_POST['email'];
        $pwd = md5($_POST['password']);

        $user = new User;
        $stmt = User::$pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt -> execute([
            $email,
        ]);
        if($stmt)
        {
            die('该用户名已被注册！');
        }
        // 生成激活码
        $code = md5(rand(1,99999));
        $redis = \libs\Redis::getInstance();
        // 序列化(转成JSON数据)
        $value = json_encode([
            'email'=>$email,
            'password' => $pwd,
        ]);
        $key = "temp_user:{$code}";
        $redis->setex($key,300,$value);
        
        
        
        // 发送邮件
        $mail = new \libs\Mail;
        $content = '恭喜您,注册成功!';
        // 从邮箱中取出姓名
        $name = explode('@',$email);
        $from = [$email,$name[0]];
        $message = [
            'title'=>'账号激活',
            "content"=>"点击以下链接进行激活：<br> 点击激活：
            <a href='http://localhost:3333/user/active_user?code={$code}'>http://localhost:3333/user/active_user?code={$code}</a><p>如果按钮不能点击，请复制上面链接地址，在浏览器中访问来激活账号！</p>",
            'from'=>$from,
        ];
        $message = json_encode($message);
        $redis = \libs\Redis::getInstance();
        $redis->lpush('email',$message);
        echo 'ok';

    }

    // 激活账号
    public function active_user()
    {
        $code = $_GET['code'];
        $key = "temp_user:{$code}";
        $redis = \libs\Redis::getInstance();
        $data = $redis->get($key);
        if($data)
        {
            $redis->del($key);
            $data = json_decode($data,true);
            $user = new User;
            if($user->add($data['email'],$data['password']))
            {
                die('注册成功');
            }
            else
            {
                die('注册失败');
            }
            // 跳转登录页面
            header('Location:/user/register');
        }
        else
        {
            die('激活码无效！');
        }
    }
}