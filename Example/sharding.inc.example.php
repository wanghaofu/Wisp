<?php
$shardingConfig['passport']['db_split_call_fun'] = [
    'db_split_call_fun' => 'Split::setDbIdx'
];
// , //通过 回调 方法确认是否 切分 如果 配置的 方法 为空 或者 不做处理则不切人 否则 进行切分
$shardingConfig['passport']['table_split_call_fun'] = array(
    // 'uuid' =>'Split::uuid' , '<表名>' => '<回调函数>'
    // 'puuid' =>'Split::puuid',
    'user' => [
        'call_fun' => 'Wisp\Setting\Split::userName',
        'sharding_field_name' => 'username'
    ],
    'user_ext' => [
        'call_fun' => 'Wisp\Setting\Split::uuid',
        'sharding_field_name' => 'uuid'
    ],
    'user_service' => [
        'call_fun' => 'Wisp\Setting\Split::uuid',
        'sharding_field_name' => 'uuid'
    ]
);

$shardingConfig['passport2']['table_split_call_fun'] = array(
    // 'uuid' =>'Split::uuid' , '<表名>' => '<回调函数>'
    // 'puuid' =>'Split::puuid',
    'user' => [
        'call_fun' => 'Wisp\Setting\Split::userName',
        'sharding_field_name' => 'username'
    ],
    'user_ext' => [
        'call_fun' => 'Wisp\Setting\Split::uuid',
        'sharding_field_name' => 'uuid'
    ],
    'user_service' => [
        'call_fun' => 'Wisp\Setting\Split::uuid',
        'sharding_field_name' => 'uuid'
    ]
);


$shardingConfig['passport3']['table_split_call_fun'] = array(
    // 'uuid' =>'Split::uuid' , '<表名>' => '<回调函数>'
    // 'puuid' =>'Split::puuid',
    'user' => [
        'call_fun' => 'Wisp\Setting\Split::userName',
        'sharding_field_name' => 'username'
    ],
    'user_ext' => [
        'call_fun' => 'Wisp\Setting\Split::uuid',
        'sharding_field_name' => 'uuid'
    ],
    'user_service' => [
        'call_fun' => 'Wisp\Setting\Split::uuid',
        'sharding_field_name' => 'uuid'
    ]
);


$shardingConfig['ship_basic']['db_split_call_fun'] = [
    'db_split_call_fun' => 'Split::setDbIdx'
];
// , //通过 回调 方法确认是否 切分 如果 配置的 方法 为空 或者 不做处理则不切人 否则 进行切分
$shardingConfig['ship_basic']['table_split_call_fun'] = array(
    // 'uuid' =>'Split::uuid' , '<表名>' => '<回调函数>'
    // 'puuid' =>'Split::puuid',
//    'user' => [
//        'call_fun' => 'Wisp\Setting\Split::HmUuid',
//        'sharding_field_name' => 'uid'
//    ],
);

$shardingConfig['ship_basic_ios']['db_split_call_fun'] = [
    'db_split_call_fun' => 'Split::setDbIdx'
];
// , //通过 回调 方法确认是否 切分 如果 配置的 方法 为空 或者 不做处理则不切人 否则 进行切分
$shardingConfig['ship_basic_ios']['table_split_call_fun'] = array(
    // 'uuid' =>'Split::uuid' , '<表名>' => '<回调函数>'
    // 'puuid' =>'Split::puuid',
    //    'user' => [
    //        'call_fun' => 'Wisp\Setting\Split::HmUuid',
    //        'sharding_field_name' => 'uid'
    //    ],
);

return $shardingConfig;