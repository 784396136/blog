<?php
namespace libs;

class Log
{
    private $fp;
    // 参数：日志文件名
    public function __construct($fileName)
    {
        // 打开日志文件
        $this->fp = fopen(ROOT . 'logs/'.$fileName.'.log', 'a');
    }

    // 向日志文件中追加内容
    public function log($content)
    {
        // 获取当前时间
        $date = date('Y-m-d H:i:s');
        // 拼出日志内容的格式  （在文件中 "\r\n" 是换行的意思）
        $c = $date . "\r\n";
        $c .= str_repeat('=', 120) . "\r\n";    //  str_repeat：获取 120 个 =
        $c .= $content . "\r\n\r\n";
        fwrite($this->fp, $c);
    }
}