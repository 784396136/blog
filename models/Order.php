<?php
namespace models;
use PDO;

class Order extends Base
{
    // 更新订单状态
    // 更新为已支付
    public function setPaid($sn)
    {
        $stmt = self::$pdo->prepare("UPDATE orders SET status = 1 , pay_time = now() WHERE sn = ?");
        
        return $stmt->execute([
            $sn,
        ]);
    }

    // 更新为一退款
    public function refund($sn)
    {
        $stmt = self::$pdo->prepare("UPDATE orders SET status = 2 WHERE sn = ?");
        
        return $stmt->execute([
            $sn,
        ]);
    }

    // 根据订单查询
    public function findBySn($sn)
    {
        $stmt = self::$pdo->prepare("SELECT * FROM orders WHERE sn = ?");
        $stmt->execute([
            $sn,
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }   

    // 下订单
    public function create($money)
    {
        $flake = new \libs\Snowflake(0);
        $stmt = self::$pdo->prepare("INSERT INTO orders(user_id,money,sn) VALUES(?,?,?)");
        return $stmt->execute([
            $_SESSION['id'],
            $money,
            $flake->nextId(),
        ]);
    }

    public function search()
    {
        $where = 'user_id ='.$_SESSION['id'];

        /*-------------------排序--------------------*/
        // 默认排序条件
        $orderBy = 'created_at';
        $orderWay = 'desc';

        if(isset($_GET['order_by']) && $_GET['order_by']=='display')
        {
            $orderBy = 'display';
        }
        if(isset($_GET['order_way']) && $_GET['order_way']=='asc')
        {
            $orderWay = 'asc';
        }

        /*-----------------翻页--------------------*/
        //每页条数
        $perpage = 10;
        // 当前页
        $page = isset($_GET['page']) ? max($_GET['page'],1) : 1;
        // 起始值
        $offset = ($page-1)*$perpage;
        // 拼出limit语句
        $limit = $offset.",".$perpage;
        
        /*-----------------翻页按钮------------------ */
        $stmt = self::$pdo->prepare("SELECT COUNT(*) FROM orders WHERE $where");
        $stmt->execute();
        $count = $stmt->fetch( PDO::FETCH_COLUMN );
        $perCount = ceil($count/$perpage);
        $btns = '';
        for($i=1;$i<=$perCount;$i++)
        {
            $parms = getUrl(['page']);
            $class = $page==$i ? 'btns-active' : '';
            $btns .= "<a class='{$class}' href='?{$parms}page={$i}'>$i</a>";
        }
       

        // 进行预处理
        $stmt = self::$pdo->prepare("SELECT * FROM orders WHERE $where ORDER BY $orderBy $orderWay LIMIT $limit");    
        $stmt->execute();
        // var_dump($stmt);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return [
            'data'=>$data,
            'btns'=>$btns,
        ];
    }
}