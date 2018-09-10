<?php
namespace controllers;

class UploadController
{
    public function upload()
    {
         // 接收图片
         $file = $_FILES['image'];

         // 生成随机的文件名。
         $name = time();
 
         // 移动图片
         move_uploaded_file($file['tmp_name'], ROOT . 'public/uploads/'.$name.'.png');
 
         /*
         {
         "success": true/false,
         "msg": "error message", # 可选
         "file_path": "[real file path]"
         }*/
 
         // 把数组转成 JSON 并返回
         echo json_encode([
             'success' => true,
             'file_path' => '/public/uploads/'.$name.'.png',
         ]);
    }
}