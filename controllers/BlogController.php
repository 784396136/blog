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
    
    // 生成静态页
    public function content_to_html()
    {
        $blog = new Blog;
        $data = $blog->search();
       
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

    
}