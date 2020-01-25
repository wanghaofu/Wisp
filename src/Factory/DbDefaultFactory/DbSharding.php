<?php
namespace Wisp\Factory\DbDefaultFactory;

/**
 * 基于参数获取扩展db 主db 以及从db的配置
 * 配置解析类 用于解析配置文件 DbfaultDbConfig
 * @author wangtao
 *
 */

use Wisp\Db\SQL\Sharding;

class DbSharding {
    /**
     * * 主数据库配置 **
     */
    static $dbConfigs = array ();
    var $dbConfig = array ();
    var $key;
    var $cacheKey;
    var $split_value;
    var $dbIdx;

    static $shardingConfig = [];
    function __construct($key, $split_value = 'null') {
        $this->key = $key;
        $this->split_value = $split_value;
        $this->cacheKey = $this->getCacheKey ();
    }

   static function getShardingConfig()
    {
        if(empty($shardingConfig))
          self::$shardingConfig = Sharding::getShardingConfig();

        return  self::$shardingConfig ;
    }

 // 获取dbIdxArr  获取db 扩展数组
    static function getDbIdxArr($dbKeyName)
    {
        $shardingConfig =  self::getShardingConfig();
       if( isset($shardingConfig[$dbKeyName]['db_split_call_fun']['master']['index_call_fun']))
       {
           $index_call_fun = $shardingConfig[$dbKeyName]['db_split_call_fun']['master']['index_call_fun'];

           if($index_call_fun) {
               $idxArr =$index_call_fun();
           }else{
               $idxArr = null;
           }
       }else{
           $idxArr = null;
       }
        return $idxArr;
    }

    /**
     * 加载系统数据库配置文件 ！
     * @param unknown $dbConfig
     */
    public static function setDbConfigs($dbConfig)
    {
        self::$dbConfigs = $dbConfig;
    }

    public static function getDbConfig($key, $split_value = 'null') {
        $config = new DbConfig ( $key, $split_value );
        $dbConfig = $config->_getDbConfig ( $key, $split_value );
        return $dbConfig;
    }


    public function setSplitValue($value) {
        $this->split_value = $value;
    }






    private function getDatabase() {
        $dbIdx = $this->getDbIdx ();
        if( is_null($dbIdx)  )
        {
            $database =  $this->dbConfig ['database'];
        }else{
            $database = $this->dbConfig ['database'] .Split::LINK_TAG. $dbIdx;
        }
        return  $database;
    }
    /**
     * 生成及 获取db 扩展索引
     * @throws Exception
     */
    private function getDbIdx() {

        $db_split_call_fun = $this->getDbCallBackFun ();

        if (empty ( $db_split_call_fun )) {
            return null;
        }

        if (empty ( $this->split_value )) {
            throw new Exception ( "split_value is not config for $db_split_call_fun!" );
        }

        return call_user_func ( $db_split_call_fun, $this->split_value );

    }


    /**
     * get db split call from config
     */
    private function getDbCallBackFun() {
        if( isset($this->dbConfig['data_split']) && is_array($this->dbConfig['data_split']))
        {
            $data_split = $this->dbConfig['data_split'];
        }else{
            return false;
        }

        if( array_key_exists('db_split_call_fun', $data_split))
        {
            return $data_split ['db_split_call_fun'];
        }else{
            return false;
        }
    }

    /**
     * @throws Exception
     */
    private function getDsn() {
        $dsnInfo = $this->dbConfig ['dsn'];
        if ( is_array ( $dsnInfo ) )
        {
            $Idx = $this->getDbIdx ();
            $dsnIdx = $this->getDsnIdx ( $Idx );
            return $dsnInfo [$dsnIdx];
        } elseif ($dsnInfo)
        {
            return $dsnInfo;
        } else
        {
            throw new \Exception ( 'dsn is not config!' );
        }
    }
    /**
     * @param unknown $Idx
     * @return mixed
     */
    private function getDsnIdx($Idx) {
        $dbIdxStr = $this->dbConfig ['dbIdx'];
        $dbIdxArr = explode ( ',', $dbIdxStr );
        foreach ( $dbIdxArr as $key => $value ) {
            $dbIdxInfo = explode ( ':', $value );
            if ($dbIdxInfo [0] == $Idx) {
                $DnsIdx = $dbIdxInfo [0];
                return $DnsIdx;
                break;
            }
        }
    }

    private function getCacheKey() {
        return $this->key . $this->split_value;
    }
}


/**
 * Example
 * 警告 非分库应用dsn不要配置 成 数组 形式
 * @var 基本配置示例！ 分布式数据库配置文件
 * 支持数据库分区， 分布算法自定义， 默认支持分段 可赠一致性hash 分表等
 */
###@@@@ This only for example for other database
// $dbconfig = array ();

// ## 单库配置配置
// $dbConfig ['example'] = array (
// 		'dsn' => 'mysql:host=127.0.0.1;port=3306;',
// 		'user' => 'example',
// 		'password' => 'examplepro',
// 		'database' => 'example',
// 		'charset' => 'utf8',
// );

// ##用户库 活动扩展库 示例配置
// $dbConfig ['user'] = array (
// 		'dsn' => array (
// 				0=>"mysql:host=127.0.0.1;port=3306;", // 非分库应用不要配置 成 数组 形式
// 				1=>"mysql:host=127.0.0.2;port=3306;",

// 		), // 数据库连接字符串
// 		'dbIdx' => "0:0,1:0,2:0,3", // x:x 第一位表示数据库扩展索引 ，第二个表示上边配置的服务器索引 notice 非分库应用不要配置 成 数组 形式
// 		//**这里显示制定库所在的服务信息是为了灵活性，可以自由确定某个扩展所在的服务器，当然也可以根据规则自动制定，每个库所在服务器会是一种规律性分布
// 		'database' => 'example', // 数据库
// 		'user' => "example", // 登陆用户
// 		'password' => "examplepro", // 登陆密码
// 		'charset' => "utf8",

// );


//!! 初始化示例
//dbConfig::setDbConfigs($dbConfig);

