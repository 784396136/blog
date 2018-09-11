<?php
namespace models;
use PDO;
use libs\Redis;

class Blog extends Base
{

    public function search()
    {
        $where = 'user_id ='.$_SESSION['id'];
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

    // 获取浏览量
    public function getDisplay($id)
    {
        // 用id拼出键名
        $key = "blog-{$id}";

        // 连接Redis
        $redis = \libs\Redis::getInstance();

        // 判断hash中是否有这个键如果有就操作内存，没有就从数据库取
        if($redis->hexists('blog_displays',$key))
        {
            // 累加 并返回值
            $newNum = $redis->hincrby('blog_displays',$key,1);
            return $newNum;
        }
        else
        {
            $stmt = self::$pdo->prepare('SELECT display FROM blogs WHERE id = ?');
            $stmt->execute([$id]);
            $display = $stmt->fetch( PDO::FETCH_COLUMN );
            $display ++;
            // 保存到Redis
            $redis->hset('blog_displays',$key,$display);
            return $display;
        }
    }

    // 生成静态页
    public function content_to_html()
    {
        $stmt = self::$pdo->query("SELECT * FROM blogs");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // 开启缓冲区
        ob_start();
        // 生成静态页
        foreach($data as $v)
        {
            view('blog.content',[
                'blog'=>$v,
            ]);
            // 取出缓冲区的内容
            $str = ob_get_contents();
            // 生成静态页
            file_put_contents(ROOT.'public/contents/'.$v['id'].'.html',$str);
            // 清空缓冲区
            ob_clean();
        }
    }

    // 定期写回数据库
    public function displayToDb()
    {
        echo '123';
        // 先取出Redis中的数据
        $redis = \libs\Redis::getInstance();
        
        $data = $redis->hgetall('blog_displays');
        foreach($data as $k=>$v)
        {
            $id = str_replace('blog-','',$k);
            $sql = "UPDATE blogs SET display = ? WHERE id = ?";
            $value = [$v,$id];
            $stmt = self::$pdo->prepare($sql);
            $stmt->execute($value);
        }
    }

    // 添加日志
    public function add($title,$content,$is_show)
    {
        $stmt = self::$pdo->prepare("INSERT INTO blogs(title,content,is_show,user_id) VALUES(?,?,?,?)");
        $ret = $stmt->execute([
            $title,
            $content,
            $is_show,
            $_SESSION['id'],
        ]);
        if(!$ret)
        {
            echo '失败';
            // 获取失败信息
            $error = $stmt->errorInfo();
            echo '<pre>';
            var_dump($error);
            exit;
        }
        return self::$pdo->lastInsertId();
    }

    // 删除日志
    public function delete()
    {
        $id = $_GET['id'];
        $stmt = self::$pdo->prepare("DELETE FROM blogs WHERE id = ? AND user_id = ?");
        return $stmt->execute([
            $id,
            $_SESSION['id'],
        ]);
    }
}