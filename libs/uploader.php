<?php
namespace libs;

class Uploader{
    private function __construct(){}
    private function __clone(){}
    private static $_obj = null;

    public static function make()
    {
        if(self::$_obj===null)
        {
            self::$_obj = new self;
        }
        return self::$_obj;
    }
}