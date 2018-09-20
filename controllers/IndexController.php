<?php
namespace controllers;

use \models\Blog;

class IndexController{
    function index(){

        $blog = new Blog;
        $data = $blog->getblog();
        
        return view('blog.blogs', $data);
        
    }
}