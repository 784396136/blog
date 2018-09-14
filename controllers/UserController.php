<?php
namespace controllers;
use models\User;
use models\Order;

class UserController
{
    // 头像视图
    public function face()
    {
        view('user.face');
    }

    public function uploadbig()
    {

        /* 接收提交的数据 */
        $count = $_POST['count'];  // 总的数量
        $i = $_POST['i'];        // 当前是第几块
        $size = $_POST['size'];   // 每块大小
        $name = 'big_img_'.$_POST['img_name'];  // 所有分块的名字
        $img = $_FILES['img'];    // 图片
        // echo "<pre>";
        // var_dump($img);
        /* 保存每个分片 */
        move_uploaded_file( $img['tmp_name'] , ROOT.'tmp/'.$i);
        $redis = \libs\Redis::getInstance();
        // 每上传一张就加1
        $uploadedCount = $redis->incr($name);
        // 如果是最后一个分支就合并
        if($uploadedCount == $count)
        {
            // 以追回的方式创建并打开最终的大文件
            $fp = fopen(ROOT.'public/uploads/big/'.$name.'.png', 'a');
            // 循环所有的分片
            for($i=0; $i<$count; $i++)
            {
                // 读取第 i 号文件并写到大文件中
                fwrite($fp, file_get_contents(ROOT.'tmp/'.$i));
                // 删除第 i 号临时文件
                unlink(ROOT.'tmp/'.$i);
            }
            // 关闭文件
            fclose($fp);
            // 从 redis 中删除这个文件对应的编号这个变量
            $redis->del($name);
        }
    }

    // 测试
    public function text()
    {
        $order = new \models\Order;
        $res = $order->setPaid('259346095908323328',2);
        var_dump($res);
    }

    // 充值
    public function recharge()
    {
        view('user.recharge');
    }

    public function docharge()
    {
        $money = $_POST['money'];
        $order = new Order;
        $res = $order->create($money);
        if($res)
        {
            message("下单成功,请尽快支付",2,"/user/orders");
        }
        else
        {
            message("下单失败,请稍后再试",2,"/user/recharge");
        }
    }

    public function orders()
    {
        $order = new Order;
        $data = $order->search();
        view('user.orders',$data);
    }

    // 登录
    public function login()
    {
        view('user.login');
    }

    public function dologin()
    {
        $email = $_POST['email'];
        $pwd = md5($_POST['password']);
        $user = new User;
        if($user->login($email,$pwd))
        {
            message('登录成功',2,'/blog/index');
        }
        else
        {
            message('账号密码错误',2,'/user/login');
        }
    }

    // 退出
    public function logout()
    {
        $_SESSION = [];
        message('退出成功',2,'/blog/index');
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
            message('该用户名已被注册',2,'/user/login');
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
                message('注册成功',2,'/user/login');
            }
            else
            {
                message('注册失败',2,'/user/register');
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