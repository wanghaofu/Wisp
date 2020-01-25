<?php
/**
 * Created by PhpStorm.
 * User: tony
 * Date: 2017/12/1
 * Time: 16:48
 */

namespace Wisp\Db\QueryHelp;

use Wisp\Factory\CacheFactory\CacheFactory;
use King\Core\Core;

class CacheSyncManager
{
    // register key
    static $predis = null;
    static $cacheService = null;

    const SYNC_TYPE_LIST = 1;
    const SYNC_TYPE_PK_VALUE = 1;  // 如果不包含pkvalue 则就是列表类型 更新和删除的时候进行同步操作可以传递
    var $dbName = null, $tableName = null, $pkValue = null;
    const DCT = 3600; // 存储同步值最大时间3600

//    static $operatorList = [ SYNC_INSERT, SYNC_UPDATE, SYNC_DELETE ];

    function __construct( $dbName, $tableName, $pkValue = null )
    {
        $this->dbName = $dbName;
        $this->tableName = $tableName;
        $this->pkValue = $pkValue;
    }

    static public function init( $dbName, $tableName, $pkValue = null )
    {
        $csm = new static( $dbName, $tableName, $pkValue );

        return $csm;
    }

    private function storeName( $opert, $pkValue = null )
    {
        if ( false == isset( $pkValue ) )
            return sprintf( '%s:%s:%s:LIST', $opert, $this->dbName, $this->tableName );
        else
            return sprintf( '%s:%s:%s:PK:%s', $opert, $this->dbName, $this->tableName, $this->pkValue );
    }

    static public function setPredis( $predis )
    {
        self::$predis = $predis;
    }

    static public function getPredis()
    {
        return self::$predis;
    }

    public function updateListen( $key, $lifeTime = 0, $sync = true )
    {
        $lCache = CacheFactory::cache();


        if ( false !== $lCache->fetch( $key ) ) {
            return true;
        }

        $redis = self::getPredis();

        if ( false == isset( $redis ) ) {
            return false;
        }

        if ( false == isset( $lifeTime ) ) {
            $lifeTime = self::DCT;
        }

        if ( false === $sync ) {
            $lCache->delete( $key );
        } else {
            $lCache->save( $key, '1', $lifeTime );
        }


        if ( true == isset( $this->pkValue ) ) {
            $storeName = $this->storeName( SYNC_PK, $this->pkValue );
            if ( false === $sync ) {
                $redis->del( $storeName );
            } else {
                $redis->setex( $storeName, $lifeTime, $key );
            }

        } else {
            $storeName = $this->storeName( SYNC_LIST );
            if ( false === $sync ) {
                $redis->srem( $storeName, $key );
            } else {
                $redis->sadd( $storeName, $key );
//                $ctime = time();
//                $expireTime = $ctime + 30;
//                Core::instance()->logger()->debug( json_encode( $expireTime) );
//                Core::instance()->logger()->debug( json_encode( $key) );
//                $redis->zadd( $storeName, $expireTime, $key );
            }
        }
    }

    /**
     * 执行删除操作
     */
    public function insertSync()
    {
        $redis = self::getPredis();
        $lCache = CacheFactory::cache();
        if ( false == isset( $redis ) || false == isset($lCache)) {
            return false;
        }
        $storeName = $this->storeName( SYNC_LIST );
//        $redis->del($storeName);
        $keys = $redis->smembers( $storeName );
//        $keys = $redis->zrange( $storeName , 0, -1);
//        Core::instance()->logger()->debug( json_encode( $storeName) );
//        $keys = $redis->zrangebyscore( $storeName , 0, -1, 'withscores');
//        de($keys);
//        Core::instance()->logger()->debug( json_encode( $keys) );
//
//        return;

        if ( $keys && is_array( $keys ) ) {
//                $cacheKey = $lCache->fetchMultiple( $keys );
//                $cacheNameArr = array_keys( $cacheKey );
//                Core::instance()->logger()->debug( json_encode( $cacheNameArr ) );
//                // 获取不同步的key  如果只存在a里边 二比里边不存在的话  会造成同步问题 就是 另外一台服务器需要 ，这个服务器没有cache 就有问题了！
//                $removeArr = array_diff( $keys, $cacheNameArr );
//                Core::instance()->logger()->notice( json_encode( $removeArr ) );
               $redis->del( $keys );
        }
    }

    public function updateSync()
    {
        $this->_updateSync();
    }

    // 删除和修改
    public function deleteSync()
    {
        $this->_updateSync();
    }



    public function _updateSync()
    {
        $redis = self::getPredis();

        $lCache = CacheFactory::cache();

        if ( false == isset( $redis ) || false == isset($lCache)) {
            return false;
        }
        $operatorList = [ SYNC_PK, SYNC_LIST ];
        foreach ( $operatorList as $k => $op ) {
            if ( true == isset( $this->pkValue ) ) {
                $storeName = $this->storeName( $op, $this->pkValue );
                $keys = $redis->smembers( $storeName );
                if ( $keys && is_array( $keys ) ) {

//                    foreach($keys as $sk => $sv){
//
//                        $cacheKey = $lCache->fetchMultiple($keys);
//                        Core::instance()->logger()->debug(json_encode($cacheKey));
//                    }
                    $redis->del( $keys );
                }
            }

            $storeName = $this->storeName( $op );
            $keys = $redis->smembers( $storeName );
            if ( $keys && is_array( $keys ) ) {
                $redis->del( $keys );
            }
        }
    }


    private function cleanExpireKey()
    {
        $redis = self::getPredis();
    }

    /**
     * @param $pks
     */
    public function  multiCacheClean( $pks )
    {

    }


}

/**
 *query
 *
 *$csm =  CacheSyncManager::init();
 * $csm->insertSync($syncKey,30);
 *
 *
 *
 * // update del insert
 *$csm =  CacheSyncManager::init();
 *$csm->sync();
 *
 *
 */