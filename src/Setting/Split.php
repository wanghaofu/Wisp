<?php
namespace Wisp\Setting;


class Split
{

    //1个库120万用户 单表10万用户 12个表一个库
    const USER_DB_SIZE = 2000000; //not zero
    const USER_INDEX_SPLIT_NUM = 5000000;

    const USER_TABLE_SPLIT_NUM = 10;

    const LINK_TAG = '_';


    const USER_TABLE_MAX_IDX = 9;

    static function default_rule( $split_value )
    {
        return null;
    }

    //通行证 用户名 用户切分算法  目前按照1亿用户分配到20个表中
    static function userName( $split_value )
    {
        //操作系统依赖不建议 ，但是效率很高
        //method 1
        $h = sprintf( "%u", crc32( $split_value ) );
        $idx = intval( fmod( $h, self::USER_TABLE_MAX_IDX ) );

        return $idx;

        //method 2
// 	    $idx = base_convert( md5( $split_value), 6, 10 ) % 20;
// 	    return $idx;

    }

    //有用户id的表切分算法
    static function uuid( $split_value )
    {
        return $split_value % max( 1, self:: USER_TABLE_SPLIT_NUM );
    }


    //有用户id的表切分算法
    static function HmUuid( $split_value )
    {
        return $split_value % max( 1, 10 );
    }

    static function puuid( $split_value )
    {
        $mdKey = md5( $split_value );

        return $mdKey[ 0 ];
    }


    // example
    static function setDbIdx( $splitValue )
    {
        return null;
    }

    static function userTable( $split_value )
    {
        return max( 1, $split_value % max( 1, self::USER_TABLE_SPLIT_NUM ) );
    }

    static function userDb( $split_value )
    {
        $dbIndex = intval( ( $split_value - 1 ) / self::USER_DB_SIZE );
        $dbIndex = max( 0, $dbIndex );

        return $dbIndex;
    }
    // $offset : 偏移量, 用于定位数据库
    // $size : 每个数据库的最大偏移量
// 	function init_user_db($offset, $size, $autoCommit = false)
}



