<?php
return [
    'redis' => [
        'scheme' => 'tcp',
        'host'   => '127.0.0.1',
        'port'   => 6379,
    ],
    'db' => [
        'host' => '127.0.0.1',
        'dbname' => 'blog',
        'user' => 'root',
        'pass' => '',
        'charset' => 'utf8',
    ],
    'email' => [
        'mode' => 'production',    // 值：debug  和 production
        'port' => 25,
        'host' => 'smtp.126.com',
        'name' => 'a784396136@126.com',
        'pass' => 'wang784396136',   //授权码
        'from_email' => 'a784396136@126.com',
        'from_name' => '全栈1班',
    ]
];