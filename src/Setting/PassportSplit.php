<?php
namespace Wisp\Setting;


/**
 * Class PassportSplit
 * @package Wisp\Setting
 *   均衡hash 分库 分表算法
 */
class PassportSplit extends Split
{
    const USER_DB_SPLIT_NUM = 3;  // 库数量 扩展索引  [0,1,2]
    const USER_TABLE_SPLIT_NUM = 10; // 表数量 扩展索引 【0,1,2,3,4,5,6,7,8,9,10】



    static function dbUserSplit( $userName){
        $h=sprintf("%u",crc32($userName));

        $pos = self::USER_DB_SPLIT_NUM * self::USER_TABLE_SPLIT_NUM;
        $dbPos = ($h % $pos) / self::USER_TABLE_SPLIT_NUM;
        $dbIdx = floor($dbPos);
        return $dbIdx;
    }

    static function tableUserSplit($userName)
    {
        $h=sprintf("%u",crc32($userName));
        $pos = self::USER_DB_SPLIT_NUM * self::USER_TABLE_SPLIT_NUM;
        $dbPos = ($h % $pos) / self::USER_TABLE_SPLIT_NUM;
        $tableIdx = ($h% $pos ) - floor($dbPos) * self::USER_TABLE_SPLIT_NUM;
        return $tableIdx;
    }


    //基本分库算法
    static function dbUserExtSplit( $uuid )
    {
        $pos = self::USER_DB_SPLIT_NUM * self::USER_TABLE_SPLIT_NUM;
        $dbPos = ( $uuid % $pos ) / self::USER_TABLE_SPLIT_NUM;
        $dbIdx = floor( $dbPos );

        return $dbIdx;
    }

    static function tableUserExtSplit($uuid)
    {
        $pos = self::USER_DB_SPLIT_NUM * self::USER_TABLE_SPLIT_NUM;
        $dbPos = ($uuid % $pos) / USER_TABLE_SPLIT_NUM;
        $tableIdx = ($uuid % $pos ) - floor($dbPos) * USER_TABLE_SPLIT_NUM;
        return $tableIdx;
    }



}