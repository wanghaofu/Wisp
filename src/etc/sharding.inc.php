<?php
const USER_TABLE_SPLIT_NUM = 10;
const USER_DB_SPLIT_NUM = 3;
const USER_SLAVE_DB_NUM = 1;
//const USER_TABLE_SPLIT_NUM = 10;
//const USER_DB_SPLIT_NUM = 8;
//const USER_SLAVE_DB_NUM = 1;

$shardingConfig[ 'passport' ][ 'db_split_call_fun' ] = [
    'default' => [
        //这里取得是数据链接配置的索引 和表的不同表的取得是表扩展的索引  如果空则不分库
        'db_call_fun' => function ( $split_value ) {
            $pos = USER_DB_SPLIT_NUM * USER_TABLE_SPLIT_NUM;
            $dbPos = ( $split_value % $pos ) / USER_TABLE_SPLIT_NUM;
            $dbIdx = floor( $dbPos );

            return $dbIdx;
        },
    ],
    'master'  => [
        'sharding_field_name' => 'uuid',   //
        'split_call_fun'      => function ( $split_value ) {//这里取得是数据链接配置的索引 和表的不同表的取得是表扩展的索引  如果空则不分库
            $pos = USER_DB_SPLIT_NUM * USER_TABLE_SPLIT_NUM;
            $dbPos = ( $split_value % $pos ) / USER_TABLE_SPLIT_NUM;
            $dbIdx = floor( $dbPos );
            return $dbIdx;
        },
        'index_call_fun'      => function () {  //返回db切分扩展集合诉诸
            return range( 0, USER_DB_SPLIT_NUM - 1 );
        }
    ],
];
// , //通过 回调 方法确认是否 切分 如果 配置的 方法 为空 或者 不做处理则不切人 否则 进行切分
$shardingConfig[ 'passport' ][ 'table_split_call_fun' ] = [
    'default'      => [
        'sharding_field_name' => 'uuid',   //
        'db_call_fun'         => function ( $split_value ) {//这里取得是数据链接配置的索引 和表的不同表的取得是表扩展的索引  如果空则不分库
            $pos = USER_DB_SPLIT_NUM * USER_TABLE_SPLIT_NUM;
            $dbPos = ( $split_value % $pos ) / USER_TABLE_SPLIT_NUM;
            $dbIdx = floor( $dbPos );

            return $dbIdx;
        },
        'table_call_fun'      => function ( $split_value ) {
            $pos = USER_DB_SPLIT_NUM * USER_TABLE_SPLIT_NUM;
            $dbPos = ( $split_value % $pos ) / USER_TABLE_SPLIT_NUM;
            $tableIdx = ( $split_value % $pos ) - floor( $dbPos ) * USER_TABLE_SPLIT_NUM;

            return $tableIdx;
        },
        'idx_set_fun'         => function ()    //生成表扩展列表数组
        {
            return range( 0, USER_TABLE_SPLIT_NUM - 1 );
        },
    ],
    'user'         => [
        'sharding_field_name' => 'username',
        'db_call_fun'         => function ( $split_value ) {//这里取得是数据链接配置的索引 和表的不同表的取得是表扩展的索引  如果空则不分库
            $h = sprintf( "%u", crc32( strtolower($split_value) ) );

            $pos = USER_DB_SPLIT_NUM * USER_TABLE_SPLIT_NUM;
            $dbPos = ( $h % $pos ) / USER_TABLE_SPLIT_NUM;
            $dbIdx = floor( $dbPos );

            return $dbIdx;
        },
        'table_call_fun'      => function ( $split_value ) {
            $h = sprintf( "%u", crc32( $split_value ) );
            $pos = USER_DB_SPLIT_NUM * USER_TABLE_SPLIT_NUM;
            $dbPos = ( $h % $pos ) / USER_TABLE_SPLIT_NUM;
            $tableIdx = ( $h % $pos ) - floor( $dbPos ) * USER_TABLE_SPLIT_NUM;

            return $tableIdx;
        },
        'idx_set_fun'         => function () {
            return range( 0, USER_TABLE_SPLIT_NUM - 1 );
        }
    ],
    'user_ext'     => 'default',
    'user_service' => 'default',
    'app' => null
];

return $shardingConfig;
