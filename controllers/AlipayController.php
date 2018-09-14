<?php
namespace controllers;

use Yansongda\Pay\Pay;

class AlipayController
{
    // hemxfd1193@sandbox.com

    public $config = [
        'app_id' => '2016091700531217',
        // 通知地址
        'notify_url' => 'https://d1f06784.ngrok.io/alipay/notify',
        // 跳回地址
        'return_url' => 'http://localhost:3333/alipay/return',
        // 支付宝公钥
        'ali_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAwdZYqwin5jfltN0y1tMDrt8Mj2NcyY3DVn1H3p6Er9IFZ5IMmPk4p16BLyrma7EuMDSmerCIndvmqjOTPtXPmxknf4wK8IHxyw96yDGm2YDBLTqZZAXYihud/S4iKAJDXyRA7hyxnkndc0ac7hNzx9UtnpY0l9wk/Pph5hDdTfCPRcb16qTzDimTaChYSRBc1d3PI9yuBIiAJHdguhwHYfNcw6Mo+F3sauaIBWFqaXf5bIOUSHF2p8w6rvcaQxWiJFxLjqNqE9V3Pnou/cYnyZvtTZyMLSuai65NrLvV1JejOD1CpsDN/t1Es7kM84qkGgzOyffUyd0I8JQ7UzV18QIDAQAB',
        // 商户应用密钥
        'private_key' => 'MIIEpgIBAAKCAQEA72gY7gZb78zrcrLFtXnf0uYLT71MWETiTzE05CFen8/Eq8e7NuuNE+Ob+zvj3jandWoEKV2kXsoBstUU71ylMaiVbbu1KOab3SFh4bYBvQwXVwJUEMAgis7BrMgeOw6gp7UgCvLVGpwzd9LxbsP23IR5PS0PQ1KiSLoMzp3NOMFrZ5JiIZDFZbNufLbdw3et+OYc4EWo8orhRc8FUbr6lAYegtDcc6LoP8k9xARvTn4hO6DGzqNEGJfug1dejLcRSBoB7pD/7Rmj04qPqSC8dKrP2BHdfUpU4ENn0H22Ay/1GYRzxnGfOVR70lOhq+GxN6sO4UiYnV2Pc5/E40sq1wIDAQABAoIBAQCQtb2L8vATjSwskn8LaPWwBzmDI4tIN6sL71RnKPyHYREiPIKfedY21XftbpAYO7URNdn6Hw5B9Zz9OeuDm96Gm75nK/UfGfXQvmLqChgW634YK+IgleGVxdAv3m7Xh8n4VXVe6NWDVjOOFEW3jGBlfvXB0sDNEt/hUgR3x87KpIQ5T/Xu/0m9kbPE9scwB2H28pGtCeXEpAqh2hx6WTuvoeGgJMqlQettsrbGu5H4CYpFuon+MHAhmoz5BolthyDEJMdogcKPb5JSztpZYug8gyMEvnExvCX4Fz1JgHIpcCzdjMLFDj3d9ibIqRsaVgfuxl4iHMz2J6eXjdrctFvxAoGBAPm6JYOgRoeqQO1nqul7Gc3LXS2D1ge8wXedlz1YZq56iaq/rJEt+5oaAi7E3DxGRnE/igF6g99vnevuwpN6SwfC1N92JLDoa84zYxdJEcFLUJO/MTY9LycZ5auaWVQoYe1mkKvMZc+rC8IRBqGSc84pPOK/2bP0DH1SlzRXlDXVAoGBAPVrleWovcU+DiKV0zoE70qziPTydMFw3e3QJoZGTEIDPthHFmuzLIqzznLOFGGW6N7XcS5ZptD+rRDC9VM4eAQ55WDWOSH+ZZLrLLJ/BJ4vUJiPuU+Us+Y1iWy+2UDEpk94x88mscwJ+TvWUS010UoMXEdY0o/N7a5rfxGYc1f7AoGBAO3Tt5Jpe4IMRJVT21NMZ0C0YmLMYXMw8ldGgBhuLiwQizdQH7qLvkf+aQG9fbjxfHix+G58DUcnd5CP7EKjfv0/MZg3Xa4VS0Yvbjlo3z3kyjJLLUTodBWa9j29W0FozApZWYIjwPpLfYEu0c/iN+OpJlEUQPK+g35+v32bwoyZAoGBAK5tV61RoAb3Eu7L08OxzC0oqxeE1yzBPkqOnULj5cEpM1peM/Y581dlcj6wb1Xo/vua3etWdrSlvXVjSx9pzAsc5wVLXHHnsGOaWDtJAYw4Dq1PwSJpiu78b/7lKd3ZpmZnboOxeb6N/CDmMu3SCTz6yf8/hvAEKYDFEBv+YURPAoGBANe1Ids0RiM3Csdx0KEw4a5PWlVgNt1g3MLRtjNV3t7Gtg9nE24pRaAmFfaG1c7HEup2tqKTOIMBG0fP3jNdktTaAKn0GYw9VBvHecA7U5BMIeubSI5ZuP5bZcIhu53uPJyVqjr4EjaZiJBYlolH6v6eYWJoA7yGx/4uXSRT4rkc',
        // 沙箱模式（可选）
        'mode' => 'dev',
    ];
    // 发起支付
    public function pay()
    {
        $sn = $_POST['sn'];
        $order = new \models\Order;
        $data = $order->findBySn($sn);

        // 如果订单没有支付并且是自己的订单就跳转到支付
        if($data['status']==0)
        {
            if($data['user_id'] == $_SESSION['id'])
            {
                // 跳转到支付宝
                $alipay = Pay::alipay($this->config)->web([
                    'out_trade_no' => $sn,
                    'total_amount' => $data['money'],
                    'subject' => '智聊系统用户充值 '.$data['money'].'元',
                ]);
                $alipay->send();
            }
            else
            {
                die('还未开放为好友支付哦~');
            }
        }
        else
        {
            die('订单状态不正确~');
        }
    }
    // 支付完成跳回
    public function return()
    {
        echo '<h1>支付成功！</h1> <hr>';
       
        $data = Pay::alipay($this->config)->verify(); // 是的，验签就这么简单！
        echo "<pre>";
        var_dump( $data->all() );
    }
    // 接收支付完成的通知
    public function notify()
    {
        $alipay = Pay::alipay($this->config);
        try{
            $data = $alipay->verify(); // 是的，验签就这么简单！
            // 这里需要对 trade_status 进行判断及其它逻辑进行判断，在支付宝的业务通知中，只有交易通知状态为 TRADE_SUCCESS 或 TRADE_FINISHED 时，支付宝才会认定为买家付款成功。
            // 1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号；
            // 2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额）；
            if($data->trade_status == 'TRADE_SUCCESS' || $data->trade_status == 'TRADE_FINISHED')
            {
                $order = new \models\Order;
                $orderInfo = $order->findBySn($data->out_trade_no);
                if($orderInfo['status']==0)
                {
                    $order->startTrans();
                    // 更新订单为已支付
                    $res1 = $order->setPaid($data->out_trade_no);
                    $user = new \models\User;
                    $res2 = $user->addMoney($orderInfo['money'],$orderInfo['user_id']);
                    if($res1 && $res2)
                    {
                        $order->commit();
                    }
                    else
                    {
                        $order->rollback();
                    }
                }
            }

        } catch (\Exception $e) {
            echo '失败：';
            var_dump($e->getMessage()) ;
        }
        // 返回响应
        $alipay->success()->send();
    }

    // 退款
    public function refund()
    {
        $sn = $_POST['sn'];
        $order = new \models\Order;
        $data = $order->findBySn($sn);
        // 生成唯一退款订单号 2018091221001004210200503880
        $refundNo = md5( rand(1,99999) . microtime() );
        try{
            // 退款
            $ret = Pay::alipay($this->config)->refund([
                'out_trade_no' => $sn,    // 之前的订单流水号
                'refund_amount' => $data['money'],              // 退款金额，单位元
                'out_request_no' => $refundNo,     // 退款订单号
            ]);
            if($ret->code == 10000)
            {
                $refund = new \models\Refund;
                $refund->create($sn,$data['money'],$refundNo);
                $order = new \models\Order;
                $user = new \models\User;
                // 开启事务
                $order->startTrans();
                $res1 = $order->refund($sn);
                $res2 = $user->minusMoney($data['money'],$data['user_id']);
                if($res1 && $res2)
                {
                    $order->commit();
                    echo '退款成功！';
                }
                else
                {
                    $order->rollback();
                }
                
            }
        }
        catch(\Exception $e)
        {
            var_dump( $e->getMessage() );
        }
    }
}