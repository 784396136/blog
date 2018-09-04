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
}