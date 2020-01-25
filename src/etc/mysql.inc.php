<?php
/**
 * This use the default include
 * 警告 非分库应用dsn不要配置 成 数组 形式
 * @var unknown_type
 */
$dbconfig = [ ];

// #主库 配置
//$dbConfig[ 'passport' ] = [
//    'dsn'      => 'mysql:host=192.168.1.157;port=3306;',
//    'user'     => 'passport',
//    'password' => 'passport',
//    'database' => 'passport',
//    'charset'  => 'utf8'
//];



// #may be like this
$dbConfig[ 'ship_basic' ] = [
    'master' => [
        'dsn'      => 'mysql:host=192.168.1.157;port=3306;',
        'user'     => 'passport',
        'password' => 'passport',
        'database' => 'ship_basic',
        'charset'  => 'utf8'
    ]
];

// #may be like this
$dbConfig[ 'acgn' ] = [
    'master' => [
        'dsn'      => 'mysql:host=192.168.1.157;port=3306;',
        'user'     => 'passport',
        'password' => 'passport',
        'database' => 'acgn',
        'charset'  => 'utf8'
    ]
];

// #may be like this
$dbConfig[ 'hforum' ] = [
    'master' => [
        'dsn'      => 'mysql:host=192.168.1.157;port=3306;',
        'user'     => 'passport',
        'password' => 'passport',
        'database' => 'ship_basic_ios',
        'charset'  => 'utf8'
    ]
];
// #may be like this
$dbConfig[ 'passport_main' ] = [
    'master' => [
        'dsn'      => 'mysql:host=192.168.1.157;port=3306;',
        'user'     => 'passport',
        'password' => 'passport',
        'database' => 'passport_main',
        'charset'  => 'utf8'
    ]
];

// #may be like this
$dbConfig[ 'ship_basic_ios' ] = [
    'master' => [
        'dsn'      => 'mysql:host=192.168.1.157;port=3306;',
        'user'     => 'passport',
        'password' => 'passport',
        'database' => 'ship_basic_ios',
        'charset'  => 'utf8'
    ]
];
// #用户 库配置

// #may be like this
$dbConfig[ 'tongren' ] = [
    'master' => [
        'dsn'      => 'mysql:host=192.168.1.157;port=3306;',
        'user'     => 'passport',
        'password' => 'passport',
        'database' => 'tongren',
        'charset'  => 'utf8'
    ]
];
// #用户 库配置


//带库切分 以及主从的 配置格式
// warning : 从库必须是真的从库 ，或者配置成和主库一样的， 否则会导致业务失败 ，因为有检测代码 跳转数据的时候会找不到数据

$dbConfig[ 'passport' ] = [    //主库设定规则 只配置基本的库名以及
                               'default' => [
                                   'dsn'      => 'mysql:host=192.168.1.157;port=3306;',
                                   'user'     => 'passport',
                                   'password' => 'passport',
                                   'database' => 'passport',  //
                                   'charset'  => 'utf8'
                               ],
                               //group 0
                               0         => [
                                   'master' => [ 'dsn' => 'mysql:host=192.168.1.157;port=3306;'
                                   ],
                                   'slave'  => [
                                       0 => [ 'dsn' => 'mysql:host=192.168.1.157;port=3306;' ],
                                   ],
                               ],
                               // group 1
                               1         => [
                                   'master' => [
                                       'dsn'      => 'mysql:host=192.168.1.157;port=3306;',
                                       'user'     => 'passport',
                                       'password' => 'passport',
                                       //'dsn'      => 'mysql:host=114.55.250.51;port=3306;',
                                       //'user'     => 'passport',
                                       //'password' => 'OCj7ovoxhs9aypfwuixr',
                                   ],
                                   'slave'  => [
                                       0 => [
                                           'dsn' => 'mysql:host=192.168.1.157;port=3306;',
                                           //'dsn' => 'mysql:host=114.55.250.51;port=3306;',
                                           //                                           'user'     => 'passport',
                                           //                                           'password' => 'OCj7ovoxhs9aypfwuixr',
                                       ]
                                   ],
                               ],
                               // group 1
                               2         => [
                                   'master' => [ 'dsn' => 'mysql:host=192.168.1.157;port=3306;' ],
                                   'slave'  => [
                                       0 => [ 'dsn' => 'mysql:host=192.168.1.157;port=3306;' ], //user password database charset 没有配置走common取
                                   ]
                               ]

];


//带主从不带切分的配置    注意带库切分的和不带库切分的配置结构是有却别的 不能通用 是为了强调区别。
$dbConfig[ 'example2' ] = [    //主库设定规则 只配置基本的库名以及


                               'default' => [
                                   'dsn'      => 'mysql:host=192.168.1.157;port=3306;',
                                   'user'     => 'passport',
                                   'password' => 'passport',
                                   'database' => 'testdb3',  //
                                   'charset'  => 'utf8',
                               ],

                               'master'  => [
                                   'dsn' => 'mysql:host=192.168.1.157;port=3306;',
                               ],
                               'slave'   => [
                                   0 => [ 'dsn' => 'mysql:host=192.168.1.157;port=3306;' ],
                                   1 => [ 'dsn' => 'mysql:host=192.168.1.157;port=3306;' ],
                               ],


];


return $dbConfig;

