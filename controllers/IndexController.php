<?php
namespace controllers;

use models\User;
class IndexController{
    function index(){
        $user = new User;
        $name = $user->getName();
        
        return view('user.hello',[
            'name'=>$name,
        ]);
        
    }
}