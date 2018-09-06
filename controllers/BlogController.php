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
    
}