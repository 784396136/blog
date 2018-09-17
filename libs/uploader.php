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

    // 定义属性
    private $_root = ROOT . "public/uploads/";  //图片的一级路径
    private $_ext = ['image/jpeg','image/jpg','image/ejpeg','image/png','image/gif','image/bmp'];   //允许上传的图片后缀
    private $_maxSize = 1024*1024*1.8;  //允许上传的最大尺寸 1.8M
    private $_file;     //上传的图片信息
    private $_suDir;    //二级目录

    // 判断图片上传的类型
    public function _checkType()
    {
        return in_array($this->_file['type'],$this->_ext);
    }

    // 判断图片的大小
    public function _checkSize()
    {
        return $this->_file['size'] < $this->_maxSize;
    }

    // 生成二级路径
    public function _makeDir()
    {
        $dir = $this->_subDir . "/" . date("Ymd");
        if(!is_dir($this->_root . $dir))
        {
            mkdir($this->_root . $dir , 0777, TRUE);
        }
        return $dir . '/';
    }

    // 给上传的图片生成一个唯一的名字
    public function _makename()
    {
        $name = md5( time() . rand(1,9999) );
        $ext = strrchr($this->_file['name'],'.');
        return $name . $ext;
    }

    /*    公开方法    */
    
    // 上传图片
    public function upload($name,$suDir)
    {
        $this->_file = $_FILES[$name];
        $this->_subDir = $suDir;
        if(!$this->_checkType())
        {
            die('不支持的文件类型');
        }
        if(!$this->_checkSize())
        {
            die('图片大小超出1.8M');
        }

        // 创建文件路径
        $dir = $this->_makeDir();

        // 生成唯一文件名
        $name = $this->_makename();
        // 移动文件
        move_uploaded_file($this->_file['tmp_name'],$this->_root .$dir . $name);
        return $dir.$name;
    }
}