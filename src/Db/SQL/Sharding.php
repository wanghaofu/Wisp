<?php
namespace Wisp\Db\SQL;

use Wisp\Setting\Split;
use Wisp\System\Util;
use Wisp\Factory\CacheFactory\CacheFactory;
use Wisp\Factory\DbDefaultFactory\DbSharding;

/**
 * index db 扩展规则 ，根据传入的sql语句 以及切分的key值 返回切分算法最后生生成的sql语句 分库分表后的 生成最后的sql语句
 *
 * 功能说明
 * 参数
 * 1: 切分算法 类方法名 "xx::xx"
 * 2: 切分的字段值 例如username "wangtao" uuid "23423" username 为切分主键 wangtao 为这里需要的值
 * 3: 待转换的sql语句 如果配置文件中包含类匹配的对应表的切分配置 则转换
 *
 * 返回：
 * 返回包含带扩展数据库名或者带扩展表名的sql语句。
 *
 * 生成一个初级db 和分表语句 并进行缓存 下次直接sprintf 替换就OK
 *
 * @author wangtao
 *
 */
class Sharding
{
    static $cache; //is redis

    // 配置文件名称 支持切分设定其他文件名
    static $configFilename = null;
    // 待处理的语句
    var $sql;

    // 动态值
    var $dbIdx = null;

    var $extDbIdx = null;

    // 分库序号
    var $tableIdx = null;

    var $extTableIdx = null;

    // 分表序号
    var $splitValue;

    // 获取的表名和db名称
    var $tableName;

    var $dbName;

    // var $db_split_call_fun;

    // var $table_split_call_fun = array();
    var $sqlCallFun = [ ];

    var $shardingFieldName; //切分主键名称
    var $tableIdxArrCallFun; //扩展集合

    var $tableSplitCallFun; //切分回调函数

    var $dbSplitCallFun;
    var $dbIdxArrCallFun; //db 索引扩展集合函数
    var $dbIdxArr; // db 扩展索引数组


    static $shardingConfig;

    // // [db][md5(sql)]=sql_idx 缓存处理过的语句
    var $shardingSqlCache;

    // 数据库缓存自动替换 字符串
    const DB_PLACE_STR = '%%_DB_IDX';
    // 表自动替换字符串
    const TABLE_PLACE_STR = '%%_TABLE_IDX';

    /**
     * 反回sql语句？！
     *
     * @param unknown $shardingConfig
     * @param string  $splitValue
     *            dbName tableName split callback splitValue
     *            dbIdx tableIdx
     *
     */
    function __construct( $sql = null, $splitParams = null, $dbName = null, $tableName = null )
    {
        self::setCache( CacheFactory::cache() );
        $this->sql = $sql;

        //加载 配置文件
        if ( is_null( self::$shardingConfig ) ) {
            self::initShardingConfig();
        }

        //初始化表名
        if ( !is_null( $dbName ) ) {
            $this->dbName = $dbName;
        }
        // 初始化表名
        if ( !is_null( $tableName ) ) {
            $this->tableName = $tableName;
        } else {
            $this->tableName = $this->getTableName( $sql );
        }

//<<<<<<< HEAD
//        //初始化切分呼叫函数  如果不存在切分配置 则这是切分值为空 并返回
//        if ( !$this->InitTableCallFunConfig( $this->tableName ) ) {
//=======
        //初始化切分呼叫函数  如果不存在切分配置 则这是切分值唯恐 并返回
        if ( !$this->InitTableCallFunConfig(  ) ) {
//>>>>>>> feature/easyBuild

            $this->splitValue = null;

//            $sql = $this->generatorIdxQuery();
            return;
        }

//        初始化切分参数
        if ( is_array( $splitParams ) && isset( $splitParams[ $this->shardingFieldName ] ) && !is_null( $splitParams[ $this->shardingFieldName ] ) ) {
            $this->splitValue = $splitParams[ $this->shardingFieldName ];
        } elseif ( is_object( $splitParams ) && !is_null( $splitParams->$this->shardingFieldName ) ) {
            $this->splitValue = $splitParams->{$this->shardingFieldName};
        } elseif ( !is_null( $splitParams ) && !is_array( $splitParams ) ) {
            $this->splitValue = $splitParams;
        } else {
            $this->splitValue = null;
//            为空情况不分表 应该记录日志
//            Throw new \Exception("splitFiled '{$this->shardingFieldName}' value is not set in " . json_encode($splitParams) . " Please checking!");
        }

        if ( $this->splitValue ) {
            $this->dbIdx = $this->setDbIdx( $this->splitValue );
            $this->setTableIdx( $this->splitValue );
        }



    }


    /**
     * is redis current
     *
     * @param unknown $cache
     */
    public static function setCache( $cache )
    {
        if ( is_null( self::$cache ) ) {
            self::$cache = $cache;
        }
    }

    /**
     * 配置 文件绝对路径 优先级比较高， 设定数据库配置文件加在的绝对路径
     *
     * @param unknown $file
     */
    static public function setConfigFile( $filename )
    {
        if ( file_exists( $filename ) ) {
            self::$configFilename = $filename;
        }
    }

    static function initShardingConfig()
    {
        if ( self::$shardingConfig ) return self::$shardingConfig;

        // 优先获取手动配置的配置文件
        if ( self::$configFilename ) {
            self::$shardingConfig = include_once( self::$configFilename );
        } else {
            self::$shardingConfig = include_once( dirname( dirname( __DIR__ ) ) . '/etc/sharding.inc.php' ); // 默认加在的文件配置路径
        }

        return self::$shardingConfig;
    }


    static public function getShardingConfig()
    {
        //加载 配置文件
        if ( is_null( self::$shardingConfig ) ) {
            self::initShardingConfig();
        }

        return self::$shardingConfig;
    }


    public static function instance( $sql = null, $splitParams = null, $dbName = null, $tableName = null )
    {
        return new static( $sql, $splitParams, $dbName, $tableName );
    }


//    public function setDbIdx($dbIdx)
//    {
//        $this->dbIdx = $dbIdx;
//    }

    /**
     *
     * @param unknown $sql
     * @param unknown $splitParams
     *            array ['username'=>test] or string 'test' if array then auto check is string then set string to splitValue
     * @param unknown $dbName
     * @param unknown $tableName
     *
     * @throws \Exception
     */
    public function generator( $dbIdx = null )
    {
        //传入的优先级比较高 如果有切分值的话还是走定位 切分址优先
        if ( $dbIdx ) {
            $this->dbIdx = $dbIdx;
        }

        if ( !is_null( $this->splitValue ) ) {
             $this->setTableIdx($this->splitValue);
            $this->tableIdx = $this->getTableIdx( $this->splitValue );
            $reSql = $this->generatorIdxQuery();
        } else {
            $tableIdxSet = $this->getTableIdxSet();
            if ( is_array( $tableIdxSet ) ) {
                foreach ( $tableIdxSet as $value ) {
                    $this->tableIdx = $value;
                    $reSql[] = $this->generatorIdxQuery();
                }
            } else {
                $reSql = $this->generatorIdxQuery();
            }
        }

        //@TODO 是否需要将 dbIdx 重制？！


        return $reSql;
//         Util::de($this);

    }




    // this variable for db use from the statement
    // 这个值在db里边设定
    // public function setSplitValue($splitValue)
    // {
    // $this->splitValue = $splitValue;
    // }


    /**
     * 首先查询缓存， 没有再根据回调生成sql的预处理语句 缓存起来供下次使用， 然后替换成最终带扩展的sql
     */
    private function generatorIdxQuery()
    {
        $dbName = $this->dbName; // 根据配置生成扩展规则
        // 生成库扩展字符串
        if ( !is_null( $this->dbIdx ) ) {
            $this->extDbIdx = Split::LINK_TAG . $this->dbIdx; // 生成表扩展
        } else {
            $this->extDbIdx = null;
        }
        // 生成表扩展字符串
        if ( !is_null( $this->tableIdx ) ) {
            $this->extTableIdx = Split::LINK_TAG . $this->tableIdx; // 生成表扩展
        } else {
            $this->tableIdx = null;
        }
        $sqlCacheKey = "{$this->dbName}{$this->extDbIdx}:{$this->tableName}:" . md5( $this->sql );
        $dealSql = null;

        $cache = $this->getCache();
        if ( $cache ) {
            $dealSql = $cache->fetch( $sqlCacheKey );
        }

        if ( empty( $dealSql ) ) {
            $dealSql = $this->nodeQuery( $this->sql );
        }
        if ( $cache ) {
            $cache->save( $sqlCacheKey, $dealSql );
        }
        $sql = str_replace( self::DB_PLACE_STR, $this->extDbIdx, $dealSql );

        $sql = str_replace( self::TABLE_PLACE_STR, $this->extTableIdx, $sql );

        return $sql;
        // 如果配置有分库 或分表 则需需要进行处理
        // if($this->shardingConfig)
    }

    public function getCache()
    {
        if ( !is_null( self::$cache ) ) {
            return self::$cache;
        } else {
            return false;
        }
    }

    /**
     * 提供给回调用
     *  1.原始sql 里的表名前增加库名
     *  2 原始sql 表名后边增加 表扩展占位符 为下部做准备
     *
     *
     * @param unknown $sql
     *
     * @return mixed
     */
    private function nodeQuery( $sql = null, $dbName = null )
    {
        if ( is_null( $sql ) ) {
            $sql = $this->sql;
        }
        if ( is_null( $dbName ) ) {
            $dbName = $this->dbName;
        }

        $pattern = '/(?![\'\"][\w\s]*)(update|into|from)\s+([\w]+)\s*(?![\w\s]*[\'\"])/usi';
        $replacement = "\$1 {$dbName}" . self::DB_PLACE_STR . '.${2}' . self::TABLE_PLACE_STR . '  ';
        $sql = preg_replace( $pattern, $replacement, $sql );

        // 返回默认值
        return $sql;
    }




    public function setDbIdx( $splitValue = null, $dbIdx = null )
    {
        if ( is_numeric( $dbIdx ) ) {
            $this->dbIdx = $dbIdx;

            return $dbIdx;
        }

        if ( is_null( $splitValue ) ) {
            $splitValue = $this->splitValue;
        }


        if ( $this->dbSplitCallFun && $splitValue ) {
            $dbIdx = call_user_func( $this->dbSplitCallFun, $splitValue );
        } else {
            $dbIdx = null;
        }
        $this->dbIdx = $dbIdx;

        return $dbIdx;
    }

//<<<<<<< HEAD


//=======
   public  function getDbIdxByTableName( $dbName, $tableName = null, $splitParams = null )
    {


        //加载 配置文件
        if ( is_null( self::$shardingConfig ) ) {
            self::initShardingConfig();
        }
        $this->dbName = $dbName;
//         de(self::$shardingConfig);
//        if (! is_null($dbName)) {
//            $this->dbName = $dbName;
//        }
        // 初始化表名
        $this->tableName = $tableName;

        // 如果不存切分配置直接返回 不做处理
        if ( !$this->InitTableCallFunConfig( ) ) {
            return null;
        }

//        初始化切分参数
        if ( is_array( $splitParams ) && isset( $splitParams[ $this->shardingFieldName ] ) && !is_null( $splitParams[ $this->shardingFieldName ] ) ) {
            $this->splitValue = $splitParams[ $this->shardingFieldName ];
        } elseif ( is_object( $splitParams ) && !is_null( $splitParams->$this->shardingFieldName ) ) {
            $this->splitValue = $splitParams->{$this->shardingFieldName};
        } elseif ( !is_null( $splitParams ) && !is_array( $splitParams ) ) {
            $this->splitValue = $splitParams;
        } else {
            $this->splitValue = null;
//            为空情况不分表 应该记录日志
//            Throw new \Exception("splitFiled '{$this->shardingFieldName}' value is not set in " . json_encode($splitParams) . " Please checking!");
        }
        if ( !is_null( $this->splitValue ) ) {
            $this->setDbIdx( $this->splitValue );
        }

        return $this->dbIdx;
    }

//>>>>>>> feature/easyBuild
    /**
     * @return mixed 获取表配置集合地点
     */
    public function getTableIdxSet()
    {
        if ( $this->tableIdxArrCallFun ) {
            return call_user_func( $this->tableIdxArrCallFun );
        } else {
            return null;
        }

    }


    /**
     * desc 通过表名获取 该表的切分配置， 重要常用配置 先检查是否有适应的配置文件 并初始化
     *
     * 初始化 db 和表 处理配置
     *
     * @param unknown $sql
     *            return tableCallFun or null
     */
    private function InitTableCallFunConfig()
    {
        $tableName = $this->tableName;
        $dbName = $this->dbName;


        //db sharding config

        //db array c
        if ( isset( self::$shardingConfig[ $dbName ] ) ) {
            $dbCallFunConf = self::$shardingConfig[ $dbName ];
        } else {
            $dbCallFunConf = null;
        }


        if (true == isset( $dbCallFunConf[ 'db_split_call_fun' ][ 'master' ][ 'index_call_fun' ] ) ) {
            $dbIdxArrCallFun = $dbCallFunConf [ 'db_split_call_fun' ][ 'master' ][ 'index_call_fun' ];
        }else{
            $dbIdxArrCallFun = null;
        }


        if ( true == isset( $dbIdxArrCallFun ) && $dbIdxArrCallFun ) {
            $this->dbIdxArr = $dbIdxArrCallFun();
        }


        if ( isset( self::$shardingConfig[ $dbName ][ 'table_split_call_fun' ] ) ) {
            $table_split_call_fun_arr = self::$shardingConfig[ $dbName ][ 'table_split_call_fun' ];
        } else {
            return false;
        }
        // #@todo tablename will get from dao class
        if ( $tableName && array_key_exists( $tableName, $table_split_call_fun_arr ) ) {

            //包支持 配置值位default 会去default的配置
            if ( $table_split_call_fun_arr[ $tableName ] == 'default' && array_key_exists( 'default', $table_split_call_fun_arr ) ) {
                $table_split_call_fun_arr_conf = $table_split_call_fun_arr[ 'default' ];
            } else {
                $table_split_call_fun_arr_conf = $table_split_call_fun_arr[ $tableName ];
            }


            if ( isset( $table_split_call_fun_arr_conf[ 'sharding_field_name' ] ) ) {
                $shardingFieldName = $table_split_call_fun_arr_conf[ 'sharding_field_name' ];
            } else {
                $shardingFieldName = null;
            }
            if ( isset( $table_split_call_fun_arr_conf[ 'table_call_fun' ] ) ) {
                $tableCallFun = $table_split_call_fun_arr_conf[ 'table_call_fun' ];
            } else {
                $tableCallFun = null;
            }


            if ( isset( $table_split_call_fun_arr_conf[ 'db_call_fun' ] ) ) {
                $dbCallFun = $table_split_call_fun_arr_conf[ 'db_call_fun' ];
            } else {
                $dbCallFun = null;
            }

            if ( isset( $table_split_call_fun_arr_conf[ 'idx_set_fun' ] ) ) {
                $idxArrCallFun = $table_split_call_fun_arr_conf[ 'idx_set_fun' ];
            } else {
                $idxArrCallFun = null;
            }
        } else {
            return false;
        }


        // 如果表没有配置db切分 优先是表配置db 索引规则 没有配置则从库里边取默认规则
        if ( empty( $dbCallFun ) && isset( $dbCallFunConf[ 'db_split_call_fun' ][ 'master' ][ 'db_call_fun' ] ) ) {
            $dbCallFun = $dbCallFunConf[ 'db_split_call_fun' ][ 'master' ][ 'db_call_fun' ];
        }


        $this->shardingFieldName = $shardingFieldName;

        $this->dbSplitCallFun = $dbCallFun;
        $this->dbIdxArrCallFun = $dbIdxArrCallFun;


        $this->tableSplitCallFun = $tableCallFun;
        $this->tableIdxArrCallFun = $idxArrCallFun;

        if ( $this->shardingFieldName ) {
            return true;
        } else {
            return false;
        }

        // return $tableCallFun;
    }

    /** 必须通过初始化库名和表名  调用初始化方法后这些参数 这个在对象初始化就就已经可以获取了
     * 这个意味着 参数的舒适化可以分开进行！
     * **/

    public function getShardingFieldName()
    {
        return $this->shardingFieldName;
    }

    public function getTableSplitCallFun()
    {
        return $this->tableSplitCallFun;
    }

    public function  getDbSplitCallFun()
    {
        return $this->dbSplitCallFun;
    }

    public function  getDbIdxArrCallFun()
    {
        return $this->dbIdxArrCallFun;
    }


    public function getDbIdxArr()
    {
        return $this->dbIdxArr;
    }

    //返回切分址
    public function getSplitValue()
    {
        return $this->splitValue;
    }

    /**
     * 反回节点索引 where use this 根据splitValue 和 切分算法生成 表idx
     *
     * @param unknown $sql
     *
     * @throws Exception
     * @return boolean|mixed
     */
    public function getTableIdx( $splitValue = null )
    {

        return $this->tableIdx;
//        if ( is_null( $splitValue ) ) {
//            $splitValue = $this->splitValue;
//        }
//
//        $tableIdx = call_user_func( $this->tableSplitCallFun, $splitValue );
//        $this->tableIdx = $tableIdx;
//
//        return $tableIdx;
    }

    public function setTableIdx( $splitValue = null)
    {
        if ( true === is_null( $splitValue ) ) {
            $splitValue = $this->splitValue;
        }
       return  $this->tableIdx = call_user_func( $this->tableSplitCallFun, $splitValue );

    }



    public function getDbIdx()
    {
        return $this->dbIdx;
    }


    /**
     * 从sql中提取表名 提取单个表名 支持多个表
     *
     * @param unknown $sql
     *
     * @return unknown|boolean
     */
    private function getTableName( $sql = null )
    {
        $pattern = '/(?![\'\"][\w\s]*)(?:update|into|from)\s+([\w]+)\s*(?![\w\s]*[\'\"])/usi';
        // $replacement = "\$1 {$dbName}.\${2}{$tableIdx} ";

        if ( preg_match( $pattern, $sql, $matches ) ) {
            $this->tableName = $matches[ 1 ];
            return $matches[ 1 ];
        } else {
            return false;
        }
    }

    /**
     * 从sql中提取表名 提取多个表明
     *
     * @param unknown $sql
     *
     * @return unknown|boolean
     * @return Example Array
     *         (
     *         [0] => user
     *         [1] => xtab
     *         )
     *
     */
    // private function getTableNames( $sql ＝ null )
    // {
    // $pattern = '/(?![\'\"][\w\s]*)(update|into|from)\s+([\w]+)\s*(?![\w\s]*[\'\"])/usi';
    // // $replacement = "\$1 {$dbName}.\${2}{$tableIdx} ";
    // if( preg_match_all($pattern, $sql, $matches) ) {
    // return $matches[1];
    // }
    //
    // else {
    // return false;
    // }
    // }
}
