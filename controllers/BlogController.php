<?php
namespace controllers;
use models\Blog;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class BlogController
{

    public function makeExcel()
    {
        // 数据库中取出数据
        $blog = new \models\Blog;
        $data = $blog->getNew();
       

        // 获取当前标签页
        $spreadsheet = new Spreadsheet();
        // 获取当前工作
        $sheet = $spreadsheet->getActiveSheet();

        // 设置第1行内容
        $sheet->setCellValue('A1', '标题');
        $sheet->setCellValue('B1', '内容');
        $sheet->setCellValue('C1', '发表时间');
        $sheet->setCellValue('D1', '是发公开');

        // 从第2行写入数据
        $i = 2;
        foreach($data as $v)
        {
            $sheet->setCellValue('A'.$i, $v['title']);
            $sheet->setCellValue('B'.$i, $v['content']);
            $sheet->setCellValue('C'.$i, $v['created_at']);
            $sheet->setCellValue('D'.$i, $v['is_show']==1?'公开':'私有');

            $i++;
        }

        $date = date("Ymd");
        
        // 生成 Excel 文件
        $writer = new Xlsx($spreadsheet);
        $writer->save(ROOT . 'excel/'.$date.'.xlsx');

        // 调用 header 函数设置协议头，告诉浏览器开始下载文件

        // 下载文件路径
        $file = ROOT . 'excel/'.$date.'.xlsx';
        // 下载时文件名
        $fileName = '最新的20条日志-'.$date.'.xlsx';
        // echo "<pre>";
        // var_dump($file);
        // echo "<br>";
        // var_dump(ROOT . 'NewBlog.xlsx');
        // die;
        // 告诉浏览器这是一个二进程文件流    
        Header ( "Content-Type: application/octet-stream" ); 
        // 请求范围的度量单位  
        Header ( "Accept-Ranges: bytes" );  
        // 告诉浏览器文件尺寸    
        Header ( "Accept-Length: " . filesize ( $file ) );  
        // 开始下载，下载时的文件名
        Header ( "Content-Disposition: attachment; filename=" . $fileName );    

        // 读取服务器上的一个文件并以文件流的形式输出给浏览器
        readfile($file);
    }

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
        $display = $blog->getDisplay($id); 
        echo json_encode([
            'display' => $display,
            'email' => isset($_SESSION['email']) ? $_SESSION['email'] : '',
            'money' => isset($_SESSION['money']) ? $_SESSION['money'] : '',
            'avatar' =>isset($_SESSION['avatar']) && $_SESSION['avatar'] == '' ? '/public/uploads/avatar/default.jpg' : $_SESSION['avatar'],
        ]);
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
                $blog->makeHtml($num);
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
        if($data['user_id']!=@$_SESSION['id'])
        message("没有权限",2);
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
        $id = $_POST['id'];
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