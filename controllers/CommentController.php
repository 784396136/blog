<?php
namespace controllers;

class CommentController
{
    public function comments()
    {
        // 接收原始数据
        $data = file_get_contents('php://input');
        // 手动处理
        $_POST = json_decode($data,TRUE);
        if(!isset($_SESSION['id']))
        {
            echo json_encode([
                'status_code' => '403',
                'message' => '请先登录',
            ]);
            exit;
        }

        $content = e($_POST['content']);
        $blog_id = $_POST['blog_id'];
        if($_SESSION['avatar']=='')
        {
            $avatar = '/uploads/avatar/default.jpg';
        }
        else
        {
            $avatar = $_SESSION['avatar'];
        }

        

        $model = new \models\Comment;
        $model->add($content,$blog_id);

        echo json_encode([
            'status_code' => '200',
            'message' => '发表成功',
            'data' => [
                'content' => $content,
                'avatar' => $avatar,
                'email' => $_SESSION['email'],
                'created_at' => date("Y-m-d H:i:s"),
            ]
        ]);
    }
 
    public function comment_list()
    {
        $id = $_GET['id'];
        $comment = new \models\Comment;
        $data = $comment->getComments($id);
        foreach($data as $k => $v)
        {
            if($data[$k]['avatar']=='')
            $data[$k]['avatar'] = '/uploads/avatar/default.jpg';
        }
        
        echo json_encode([
            'status_code' => '200',
            'data' => $data,
        ]);
    }
}