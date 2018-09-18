<?php 
namespace controllers;

class RedbagController
{
    // 加载视图
    public function rob_view()
    {
        view('redbag.rob');
    }

    // 初始化
    public function init()
    {
        $redis = \libs\Redis::getInstance();
        // 设置初始量
        $redis->set("redbag_stock",20);
        // 设置空的集合
        $key = 'redbag_'.date('Ymd');
        $redis->sadd($key,-1);
        // 设置过期时间
        $redis->expire($key,3900);
    }

    public function rob()
    {
        // 判断是否登录
        if(!isset($_SESSION['id']))
        {
            echo json_encode([
                'status_code' => '401',
                'message' => '请先登录',
            ]);
            exit;
        }

        // 判断当前是否在允许时间段 9~11
        if(date('H')<9 || date('H')>11)
        {
            echo json_encode([
                'status_code' => '403',
                'message' => '不在允许时间段',
            ]);
            exit;
        }

        // 判断今天是否抢过
        $key = 'redbag_'.date('Ymd');
        $redis = \libs\Redis::getInstance();
        $exists = $redis->sismember($key,$_SESSION['id']);
        if($exists)
        {
            echo json_encode([
                'status_code' => '403',
                'message' => '今天已经抢过了,明天再来哦~',
            ]);
            exit;
        }

        // 减少库存量
        $stock = $redis->decr('redbag_stock');
        if($stock<0)
        {
            echo json_encode([
                'status_code' => '403',
                'message' => '今天的红包已经抢完了,明天再来哦~',
            ]);
            exit;
        }

        // 下单
        $redis -> lpush('redbag_orders',$_SESSION['id']);

        // 放到集合中代表已经抢过了
        $redis->sadd($key,$_SESSION['id']);

        echo json_encode([
            'status_code' => '200',
            'message' => '恭喜您抢到了今日红包~',
        ]);
    }

    // 监听队列
    public function makeOrder()
    {
        $redis = \libs\Redis::getInstance();
        $redbag = new \models\Redbag;

        // 设置过期时间
        ini_set('default_socket_timeout', -1); 

        echo "开始监听红包队列....\r\n";

        while(true)
        {
            // 从队列中取数据，设置为永久不超时
            $data = $redis->brpop('redbag_orders', 0);

            $userId = $data[1];
            // 下订单
            $redbag->create($userId);

            echo "用户ID为{$userId}的用户抢到了红包~";
        }
    }
}