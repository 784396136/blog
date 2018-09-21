<?php
namespace controllers;

use \models\Blog;

class IndexController{
    function index(){

        $blog = new Blog;
        $data = $blog->getblog();

        // 获取活跃用户
        $user = new \models\User;
        $users = $user->getActiveUsers();
        return view('blog.index', [
                "blogs"=>$data,
                "users"=>$users
            ]);
        
    }
}