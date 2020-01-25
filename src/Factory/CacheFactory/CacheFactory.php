<?php
namespace Wisp\Factory\CacheFactory;

// 文件cache
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\ApcuAdapter;
use Symfony\Component\Cache\DoctrineProvider;

class CacheFactory
{
    static $cache = null;

//exit();
   static public function cache()
    {

        if ( is_null( self::$cache ) ) {
            //环境支持apuc则 初始化为apcu 否则 初始化为文件缓存
            if ( ApcuAdapter::isSupported() ) {
                $pool = new ApcuAdapter( 'wisp' );
            } else {
                $pool = new FilesystemAdapter('wisp');
            }
            self::$cache = new DoctrineProvider( $pool );
        }
        return self::$cache;
        //$key = '{}()/\@:';
        /**
         * $key = 'test';
         *
         * $cache->delete( $key );
         * $cache->contains( $key );
         * //$cache->
         * $cache->save( $key, [ 'bar' ], 5 );
         * //$cache->
         * $cache->contains( $key );
         * $res = $cache->fetch( $key );
         * de( $res );
         * $cache->delete( $key );
         * $res = $cache->fetch( $key );
         * de( $res );
         * //$cache->save($key,'bar');
         * //$cache->flushAll();
         * //$res2= $cache->fetch($key);
         * de( $res );
         * $cache->delete( $key );
         * $res = $cache->fetch( $key );
         * de( $res );
         * //$cache->save($key,'bar');
         * //$cache->flushAll();
         * //$res2= $cache->fetch($key);
         **/
    }
}