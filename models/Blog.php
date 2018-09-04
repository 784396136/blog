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
            $where .= " AND (title like '%{$_GET['keywords']}%' OR content like '%{$_GET['keywords']}%')";
            $value[] = $_GET['keywords'];
        }
        // 如果有start_date值并且不为空时添加where条件
        if(isset($_GET['start_date']) && $_GET['start_date'])
        {
            $where .= " AND created_at >= '{$_GET['start_date']}'";
            $value[] = $_GET['start_date'];
        }
        // 如果有end_date值并且不为空时添加where条件
        if(isset($_GET['end_date']) && $_GET['end_date'])
        {
            $where .= " AND created_at <= '{$_GET['end_date']}'";
            $value[] = $_GET['end_date'];
        }
        // 如果有is_show值并且不为空时添加where条件
        if(isset($_GET['is_show']) && $_GET['is_show'])
        {
            $where .= " AND is_show = {$_GET['is_show']}";
            $value[] = $_GET['is_show'];
        }
        // 进行预处理
        $stmt = self::$pdo->prepare("SELECT * FROM blogs WHERE $where");    
        $stmt->execute($value);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data;
    }
}