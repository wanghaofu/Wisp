<?php
/**
 * This use the default include
 * 警告 非分库应用dsn不要配置 成 数组 形式
 * @var unknown_type
 */
$dbconfig = [ ];

// #主库 配置
$dbConfig[ 'passport' ] = [
    'dsn'      => 'mysql:host=192.168.1.157;port=3306;',
    'user'     => 'passport',
    'password' => 'passport',
    'database' => 'passport',
    'charset'  => 'utf8'
];
$dbConfig[ 'passport2' ] = [
    'dsn'      => 'mysql:host=192.168.1.157;port=3306;',
    'user'     => 'passport',
    'password' => 'passport',
    'database' => 'passport',
    'charset'  => 'utf8'
];
$dbConfig[ 'passport3' ] = [
    'dsn'      => 'mysql:host=192.168.1.157;port=3306;',
    'user'     => 'passport',
    'password' => 'passport',
    'database' => 'passport',
    'charset'  => 'utf8'
];


// #主库 配置  测试服
if ( 0 ) {
    $dbConfig[ 'ship_basic' ] = [
        'dsn'      => 'mysql:host=192.168.1.104;port=3306;',
        'user'     => 'wangtao',
        'password' => 'wangtao1905',
        'database' => 'ship_basic',
        'charset'  => 'utf8'
    ];
} else {
    $dbConfig[ 'ship_basic' ] = [
        'dsn'      => 'mysql:host=192.168.1.157;port=3306;',
        'user'     => 'passport',
        'password' => 'passport',
        'database' => 'ship_basic',
        'charset'  => 'utf8'
    ];

    $dbConfig[ 'ship_basic_ios' ] = [
        'dsn'      => 'mysql:host=192.168.1.157;port=3306;',
        'user'     => 'passport',
        'password' => 'passport',
        'database' => 'ship_basic_ios',
        'charset'  => 'utf8'
    ];
}

// #may be like this
$dbConfig[ 'test' ] = [
    'dsn'      => 'mysql:host=127.0.0.1;port=3306;',
    'user'     => 'root',
    'password' => '',
    'database' => '',
    'charset'  => 'utf8'
];

//// #用户 库配置
//$dbConfig['user'] = array(
//    'dsn' => array(
//        0 => "mysql:host=127.0.0.1;port=3306;",
//        1 => "mysql:host=127.0.0.1;port=3306;",
//        2 => "mysql:host=127.0.0.1;port=3306;",
//        3 => "mysql:host=127.0.0.1;port=3306;",
//        4 => "mysql:host=127.0.0.1;port=3306;",
//        5 => "mysql:host=127.0.0.1;port=3306;",
//        6 => "mysql:host=127.0.0.1;port=3306;"
//    ) // 非分库应用不要配置 成 数组 形式
//, // 数据库连接字符串
//    'dbIdx' => "0:0,1:0,2:0,3:0,4:0,5:0", // x:x 第一位表示数据库扩展索引 ，第二个表示上边配置的服务器索引 警告 非分库应用不要配置 成 数组 形式
//    'database' => 'stra', // 数据库
//    'user' => "stradev", // 登陆用户
//    'password' => "stradev", // 登陆密码
//    'charset' => "utf8"
//)
//;

// # 数据切分算法 do not modify underline
//$dbConfig['user']['data_split'] = array(
//    'db_split_call_fun' => 'Split::userDb',  // 设置库的切分回调
//    'table_split_call_fun' => array(
//        'default' => 'Split::userTable'
//    ) // waring
//
//);
//$dbConfig['stra_index']['data_split'] = array(
//    // 'db_split_call_fun' => 'Split::setDbIdx', //通过 回调 方法确认是否 切分 如果 配置的 方法 为空 或者 不做处理则不切人 否则 进行切分
//    'table_split_call_fun' => array(
//        // 'uuid' =>'Split::uuid' , '<表名>' => '<回调函数>'
//        // 'puuid' =>'Split::puuid',
//        'user' => 'Split::userName'
//    )
//);

return $dbConfig;

