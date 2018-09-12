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

    //显示私有日志
    public function content()
    {
        $id = $_GET['id'];
        $model = new Blog;
        $blog = $model->find($id);

        // 判断是否是自己的日志
        if($_SESSION['id']!=$blog['user_id'])
        {
            die('无权访问');
        }
        view('blog.content',[
            'blog'=>$blog,
        ]);
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
            if($is_show == 1)
            {
                $blog->makeHtml($id);
            }
            message("发表成功,新添加日志ID为:{$num}",2,'/blog/index');
        }
        else
        {
            message("发表失败~",2,'/blog/create');
        }
    }

    // 显示修改日志
    public function edit()
    {
        $id = $_GET['id'];
        $blog = new Blog;
        $data = $blog->find($id);
        view('blog.edit',[
            'data'=>$data,
        ]);
    }

    public function update()
    {
        $title = $_POST['title'];
        $content = $_POST['content'];
        $is_show = $_POST['is_show'];
        $id = $_POST['id'];
        $blog = new Blog;
        $res = $blog->update($title,$content,$is_show,$id);
        if($res)
        {
            if($is_show == 1)
            {
                $blog->makeHTML($id);
            }
            else
            {
                $blog->deleteHtml($id);
            }
            message('修改成功！',2,'/blog/index');
        }
        else
        {
            message('修改失败,请稍后再试~',2,'/blog/edit?id='.$id);
        }
    }

    // 删除日志
    public function delete()
    {
        $id = $_GET['id'];
        $blog = new Blog;
        if($blog->delete($id))
        {
            $blog->deleteHtml($id);
            message("删除成功",2,'/blog/index');
        }
        else
        {
            message("删除失败",2,'/blog/index');
        }
    }
}