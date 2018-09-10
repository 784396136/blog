<?php
namespace controllers;
use libs\Mail;
class MailController
{
    public function send()
    {
        $redis = \libs\Redis::getInstance();
        $mailer = new Mail;

        // 设置PHP永不超时
        ini_set('default_socket_timeout', -1);

        echo "发邮件队列启动成功..\r\n";

        while(true)
        {
            $data = $redis->brpop('email',0);
            $message = json_decode($data[1], TRUE);
            $mailer->send($message['title'], $message['content'], $message['from']);
            echo "发送邮件成功！继续等待下一个。\r\n";
        }

    }
}