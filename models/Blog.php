<?php
namespace models;
use PDO;

class Blog extends Base
{

    public function search()
    {
        $where = 1;
        $value = [];
        // 如果有keyword值并且不为空时添加where条件
        if(isset($_GET['keywords']) && $_GET['keywords'])
        {
            $where .= " AND (title like '%?%' OR content like '%?%')";
            $value[] = $_GET['keywords'];
        }
        // 如果有start_date值并且不为空时添加where条件
        if(isset($_GET['start_date']) && $_GET['start_date'])
        {
            $where .= " AND created_at >= '?'";
            $value[] = $_GET['start_date'];
        }
        // 如果有end_date值并且不为空时添加where条件
        if(isset($_GET['end_date']) && $_GET['end_date'])
        {
            $where .= " AND created_at <= '?'";
            $value[] = $_GET['end_date'];
        }
        // 如果有is_show值并且不为空时添加where条件
        if(isset($_GET['is_show']) && $_GET['is_show'])
        {
            $where .= " AND is_show = ?";
            $value[] = $_GET['is_show'];
        }

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
        $stmt = self::$pdo->prepare("SELECT COUNT(*) FROM blogs WHERE $where");
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
        $stmt = self::$pdo->prepare("SELECT * FROM blogs WHERE $where ORDER BY $orderBy $orderWay LIMIT $limit");    
        $stmt->execute($value);
        // var_dump($stmt);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return [
            'data'=>$data,
            'btns'=>$btns,
        ];
    }
}