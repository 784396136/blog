<?php
namespace controllers;
use models\Blog;

class BlogController
{
    public function index()
    {
        $blog = new Blog;
        $data = $blog->search();
        view('blog.blogs',$data);
    }
    
    // 为所有的日志生成静态页
    public function content_to_html()
    {
        $blog = new Blog;
        $blog->content_to_html();
    }

    public function display()
    {
        // 接受日志ID
        $id = (int)$_GET['id'];
        $blog = new Blog;
        echo $blog->getDisplay($id);
    }

    //将内存中的数据写回数据库
    public function toDb()
    {
        $blog = new Blog;
        $blog->displayToDb();
    }
    
    // 发表日志
    public function create()
    {
        view('blog.create');
    }

    public function docreate()
    {
        $title = $_POST['title'];
        $content = $_POST['content'];
        $is_show = $_POST['is_show'];
        $blog = new Blog;
        $num = $blog->add($title,$content,$is_show);
        if($num)
        {
            message("发表成功,新添加日志ID为:{$num}",2,'/blog/index');
        }
        else
        {
            message("发表失败~",2,'/blog/create');
        }
    }

    // 删除日志
    public function delete()
    {
        $blog = new Blog;
        if($blog->delete())
        {
            message("删除成功",2,'/blog/index');
        }
        else
        {
            message("删除失败",2,'/blog/index');
        }
    }
}