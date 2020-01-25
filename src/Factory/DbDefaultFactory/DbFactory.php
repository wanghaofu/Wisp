<?php
namespace Wisp\Factory\DbDefaultFactory;

use Wisp\Db\Driver\Db;

// 这里应该调整成可以外部调用的示例

/**
 * 执行链接操作 返回db连接对象
 * 依赖关系 DbFactory--->DbConfig
 *                 ---> mysql.inc.php 数据库配置文件
 * 负责数据库数据库连接
 *
 * @var unknown
 */

//默认配置文件
define('WISP_CONFIG_DIR', dirname(dirname(__DIR__)) . '/etc/mysql.inc.php');

class DbFactory
{

    static $dbObjCache = array();

    var $db;

    var $split_value;

    static $dbConfig = array();

    static $filename = '';


    /**
     * 配置 文件绝对路径  优先级比较高， 设定数据库配置文件加在的绝对路径
     *
     * @param unknown $file
     */
    static public function setConfigFile($filename = null)
    {
//        if (file_exists($filename)) {
        DbConfig::setConfigFile( $filename );
//        }
    }

    /**
     * 初始化全新的db为每一个数据库操作建立db *
     */
    static function db($key,$dbIndex=null,$salve=false, $autoCommit=true)
    {
//        $dbConfig = DbConfig::getDbConfig($key);
//        $db = self::init_db($dbConfig);
//        return $db;
        self::setConfigFile();

        return self::ConnDb($key,$dbIndex,$salve, $autoCommit);
    }


    /**
     * 初始化全新的db为每一个数据库操作建立db *
     */
    static function ConnDb($dbKeyName,$dbIndex=null,$salve=false, $autoCommit=true)
    {
        $dbConfig = DbConfig::getDbConfigByNameAndIndex($dbKeyName,$dbIndex,$salve);
        //TODO  主库的配置规则需要统一一下
        if(!is_null($dbIndex)){
            $dbConfig['database'] =  $dbConfig['database'].'_'.$dbIndex;
        }
        if(true == $salve) {
            $salve = $salve ? "salve" : "master";
//            $dbConfig['slaveIndex']
        }

        $dbCacheKey = "{$dbKeyName}:{$dbIndex}:{$salve}";
        //检查是否建立连接 连接对象如果已经建立 没有建立则创建，否则取cache
        if(!isset(self::$dbObjCache[$dbCacheKey])){
            self::$dbObjCache[$dbCacheKey]= self::init_db($dbConfig,$autoCommit);
        }

        $db = self::$dbObjCache[$dbCacheKey];
//        de(self::$dbObjCache);
        return $db;
    }



    static  function init_db($dbConfig, $autoCommit = true, $persistent = false)
    {
        $db = new Db($dbConfig['dsn'], $dbConfig['user'], $dbConfig['password'], $dbConfig['database'], $autoCommit, $dbConfig['charset'], $persistent);
        if (isset($GLOBALS['gMirrorId']) && $GLOBALS['gMirrorId'] > 0) {
            $db->readOnly = true;
        }
        if (! $autoCommit)
            $db->begin();

        return $db;
    }

    /**
     * no
     * Enter description here .
     *
     * .. this  must move to sharding @todo
     *
     * @param unknown_type $sql

     */
//    function baseNodeQuery($sql)
//    {
//        $dbName = $this->dbConfig['database'];
//
//        $pattern = '/(?![\'\"][\w\s]*)(update|into|from)\s+([\w]+)\s*(?![\w\s]*[\'\"])/usi';
//        $replacement = "\$1 {$dbName}.\${2}";
//        $sql = preg_replace($pattern, $replacement, $sql);
//        return $sql;
//    }
}

?>
