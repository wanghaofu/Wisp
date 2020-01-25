<?php
namespace Wisp;

use Wisp\Factory\DbDefaultFactory\DbFactory;
use Wisp\Db\SQL\Sharding;
/**
 *
 * 外部系统调用可以在这里输入外部参数， 分离应用和框架  外部框架配置入口
 *
 * Class WispConfig
 * @package Wisp
 */


class Config
{
    static $generatorPath = './DAO/';

    static public function setGeneratorPath( $path = null )
    {
        if ( !is_null( $path ) )
            self::$generatorPath = $path;
    }


    static public function setDbConfig($fileName)
    {
        DbFactory::setConfigFile($fileName);
    }

    static public function setShardingConfig($fileName)
    {
        Sharding::setConfigFile($fileName);
    }

    static public function getGeneratorPath()
    {
        return self::$generatorPath;
    }

}

