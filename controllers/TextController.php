<?php
namespace controllers;

use Intervention\Image\ImageManagerStatic as Image;

class TextController
{
    public function image()
    {
        $path = ROOT . 'public/uploads/';
        $image = Image::make($path.'xuexiaoban.jpg');
        $image->insert($path.'water.jpg','top-right');
        $image->save($path."water/water.jpg");
    }
}