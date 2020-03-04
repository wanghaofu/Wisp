<?php
namespace Wisp\Db;

use Wisp\Db\SQL\Criteria;
use Wisp\Db\SQL\Statement;
use Wisp\Db\QueryHelp\QueryHelp;
use Wisp\Db\QueryBuild\QueryBuilder;
use Wisp\Db\QueryBuild\IModel;

use Wisp\Db\QueryHelp\CacheSyncManager;

/**
 * 数据库入口facade 类文件
 * 主要的数据库访问代理 依赖系统初始化
 * DAO.php
 */
// final
class DAO extends QueryBuilder
{

    use QueryHelp;
//    use CacheManager;
    static protected $__redisService = null;
    static protected $__cacheService = null;
    protected $__schemaName = null;
    protected $__tableName = NULL;
    protected $__primaryKey = null;
    protected $__pkValue = null;
    protected $__db = null;

    // db 连接对象暂时
    protected $querys = [ ];
    // 数据库初始化类型用来识别如何初始化数据库 默认框架默认的形式 否则是直接传递如pdo连接对象 用于和其他系统融合
    const INIT_DEFAULT_PLATFORM = 0;
    const INIT_THIRD_PLATFORM = 1;
    // cache 类型 根据cache类型 决定用什么cache
    const CACHE_REDIS = 1;
    const CACHE_APCU = 2;
    const CACHE_FILE = 3;
    protected $dbDebug = false;

    const SYNC_TYPE_INSERT = 1;
    const SYNC_TYPE_UPDATE = 2;
    const SYNC_TYPE_DELETE = 3;

    protected $cacheSync = null;


    protected $obj = false; //返回结果是否转换为对象


//    private $cacheTime = null;

    // 可以直接连接 method 入口方式
    /**
     * 初始化对象 参数为数据库名称或者连接符号
     *
     * @param
     *            InitType 0 | 1 notice 第三方的以连接pdo对象 不许在System里边构造好
     *
     * @todo 这个逻辑稍后要再优化下
     *
     * @param mixed $__schema
     *            pdo or schemaName
     */
//    public function __construct( $schemaName = null, $InitType = self::INIT_DEFAULT_PLATFORM )
    public function __construct( $schemaName = null )
    {
        if ( !is_null( $schemaName ) ) {
            $this->__schemaName = $schemaName;
        }
//        $this->__InitType = $InitType;
//        //缓存资源给 sharding
//        parent::__construct( $this->getDb() );
//
    }

    public function debug( $dbDebug = true )
    {
        $this->dbDebug = $dbDebug;

        return $this;
    }

//    public function debug( $dbDebug = true )
//    {
//        $this->dbDebug = $dbDebug;
//    }
//
//    static public function setCacheService( $cache )
    /**
     * @param $cache
     *
     * @DOTO this will remove
     * @return bool
     */
    static public function setCacheService( $cache )
    {
        return false;
    }

    static public function getCacheService()
//>>>>>>> feature/easyBuild
    {
        return false;
    }

    static public function setRedisCacheService( $cache )
    {
        self::$__redisService = $cache;
    }

    static public function getRedisCacheService()
    {
        return self::$__redisService;
    }

    /**
     * @param $cacheTime
     * @param $syncOpert  is string or Array
     *
     * <<<<<<< HEAD
     *
     * @throws DBException
     *
     * @param
     *                    schemaName 数据库名称可以更改连接的数据库 @todo this not supert for this if need create another dao
     *
     * @return \Wisp\Db\Driver\Db
     */
//    public function getDb()
//    {
//        return null;
//        if ( empty( $this->__schemaName ) ) {
//            throw new DBException( "dbName is not set" );
//        }
//        //TODO  数据库连接部分需要重新调整  连接要移到statment中去了
//        switch ( $this->__InitType ) {
//            case self::INIT_THIRD_PLATFORM:
//                $db = Sys::db();
//                $db->setDatabase( $this->__schemaName );
//                break;
//            case self::INIT_DEFAULT_PLATFORM:
//            default:
//                $db = Sys::db( $this->__schemaName );
//        }
//
//        return $db;
//=======
//     * @return $this
//     */

    public function cache( $cacheTime, $sync = null )
    {
        $this->cacheTime = $cacheTime;
        $this->cacheSync = $sync;

        return $this;
    }

    /**
     * 不建议支持此中方法 目前
     */
    protected function setSchemaName( $schemaName )
    {
        $this->__schemaName = $schemaName;
    }

    protected function setTableName( $tableName )
    {
        $this->__tableName = $tableName;
    }

    protected function setPk( $primaryKey )
    {
        $this->__primaryKey = $primaryKey;
    }

    private function _getSchemaName()
    {
        if ( false == empty( $this->__df->__tableName ) ) {
            $this->__schemaName = $this->__df->__schemaName;
        }

        return $this->__schemaName;
    }

    private function _getTableName()
    {
        // 利用反射或者调用this 类名 可能转义算法稍后定
        if ( false == empty( $this->__df->__tableName ) ) {
            $this->__tableName = $this->__df->__tableName;
        }

        return $this->__tableName;
    }


    private function _getPrimaryKey()
    {
        if ( false == empty( $this->__df->__primaryKey ) ) {
            $this->__primaryKey = $this->__df->__primaryKey;
        }

        return $this->__primaryKey;
    }

    private function __setAccessParams( $df = null )
    {
        if ( false == empty( $this->__df->__tableName ) ) {
            $this->__tableName = $this->__df->__tableName;
        }
        if ( false == empty( $this->__df->__tableName ) ) {
            $this->__schemaName = $this->__df->__schemaName;
        }
        if ( false == empty( $this->__df->__primaryKey ) ) {
            $this->__primaryKey = $this->__df->__primaryKey;
        }
    }

    public function reg( $stmt_key, $sql, $cacheTime = null )
    {
        if ( true == isset( $this->querys[ $stmt_key ] ) ) {

            if ( true == isset( $this->querys[ $stmt_key ][ 'cache' ] ) ) {
                $sct = $this->querys[ $stmt_key ][ 'cache' ];

                if ( $sct != $this->cacheTime ) {
                    $this->querys[ $stmt_key ][ 'cache' ] = $this->cacheTime;
                    $this->cacheTime = null;
                } elseif ( true == isset( $cacheTime ) && $sct != $cacheTime ) {
                    $this->querys[ $stmt_key ][ 'cache' ] = $cacheTime;
                }
            }

            return $this;
        }
        $this->querys[ $stmt_key ][ 'sql' ] = $sql;
        if ( true === isset( $this->cacheTime ) ) {
            $this->querys[ $stmt_key ][ 'cache' ] = $this->cacheTime;
            $this->cacheTime = null;
        } elseif ( true === isset( $cacheTime ) ) {
            $this->querys[ $stmt_key ][ 'cache' ] = $cacheTime;
        }

        return $this;
    }


    /**
     * sql 语句注册
     *
     * @param unknown $query
     *            [qk][sql]=" select * from __TABLE_NAME__";
     *            [qk][cache]=1800;
     */
    public function registerQuery( $query, $stmt_key = null, $cache = null )
    {
        if ( true == isset( $this->querys[ $stmt_key ] ) ) {
            return false;
        }

        if ( true === isset( $stmt_key ) ) {
            $this->querys[ $stmt_key ][ 'sql' ] = $query;
            if ( true === isset( $cache ) ) {
                $this->querys[ $stmt_key ][ 'cache' ] = $cache;
            }

            return true;
        }

        if ( is_array( $query ) ) {
            $this->querys = array_merge( $this->querys, $query );

            return true;
        }

        if ( is_string( $query ) ) {
            if ( strpos( $query, '#' ) !== false ) {
//            if( strpos('#',$query)){
                $tmpQueryArr = explode( '#', $query, 3 );
                $queryConf = [ ];
                $stmt_key = null;
                foreach ( $tmpQueryArr as $key => $qinfo ) {
                    $qinfo = trim( $qinfo );
                    switch ( $key ) {
                        case 0:
                            $stmt_key = $qinfo;
                            break;
                        case 1:  //这里如果是数字的话说明是缓存 则当作cache处理
                            if ( is_numeric( $qinfo ) ) {
                                $queryConf[ $stmt_key ][ 'cache' ] = $qinfo;
                            } elseif ( !is_numeric( $qinfo ) && !empty( $qinfo ) ) {
                                $queryConf[ $stmt_key ][ 'sql' ] = $qinfo;
                            }
                            break;
                        case 2:  //如果第二位存在 第三位则还是sql sql必须存在  那如果cache不存在 则 第二位则就是 type了 ！
                            $sql = $qinfo;
                            $queryConf[ $stmt_key ][ 'sql' ] = $sql;
                            $this->query = $sql;
//                        $queryConf[$stmt_key]['cache'] = $qinfo;
                            break;
                        case 3:
                            $type = $qinfo;
                            $queryConf[ $stmt_key ][ 'type' ] = $qinfo;
                            break;
                        case 4:
                            $queryConf[ $stmt_key ][ 'field' ] = $qinfo;
                        default:
                    }
                }

                if ( isset( $type ) && !empty( $type ) ) {
                    if ( is_string( $sql ) && $sql ) {
                        $queryConf[ $stmt_key ][ 'sql' ] = "select * from __TABLE_NAME__ where " . $sql;
                    } else {
                        $queryConf[ $stmt_key ][ 'sql' ] = "select * from __TABLE_NAME__";
                    }
                }
            }
        }
        if ( true === isset( $stmt_key ) && true === isset( $queryConf[ $stmt_key ] ) ) {
            $this->querys = array_merge( $this->querys, $queryConf );
        }

        return $this;
    }
//    public /* mixed */function get( $params, $cacheTime = null, $hint = NULL, $use_master = FALSE )
//    {
//        $key = '__get' . $this->__tableName . $this->__schemaName;
//        $res = $this->getCache( $key, $params );
//        if ( $res ) {
//            return $res;
//        }
//        $criteria = new Criteria();
//        $criteria->type = $criteria->getConstant( 'TYPE_IS_GET' );
//        $criteria->tableName = $this->__tableName;
//        $criteria->dbName = $this->__schemaName;
//        $criteria->params = $params;
//        $criteria->pk = $this->__primaryKey;
//        $criteria->dbDebug = $this->dbDebug;
//        $stmt = new Statement( $criteria );
//        $res = $stmt->execute();
//        $this->setCache( '__get', $params, $res, $cacheTime );
//
//        return $res;
//    }

    public function getByPk( $pkValue )
    {
        $primaryKey = $this->_getPrimaryKey();

        if ( false == empty( $primaryKey ) ) {
            $this->setPkValue( $pkValue );

            return $this->eq( $primaryKey, $pkValue )->get();
        } else {
            return false;
        }
    }

    private function setPkValue( $params )
    {

        if ( true == isset( $params ) && false == is_array( $params ) ) {
            $this->__pkValue = $params;
        } else {
            $pk = $this->_getPrimaryKey();
            if ( true == isset( $params[ $pk ] ) ) {
                $this->__pkValue = $params[ $pk ];
            }
        }
    }

    private function getPkValue()
    {
        return $this->__pkValue;
    }


    public function get( $params = null, $cacheTime = null, $hint = NULL, $use_master = FALSE )
    {
        $res = $this->limit( 1 )->gets( $params, $cacheTime, $hint, $use_master );

        if ( true === is_array( $res ) ) {
            $res = current( $res );
        }

        return $res;
    }

    public function count( $df = null )
    {
        if ( true == isset( $df ) ) {
            $this->__df = $df;
        }

        $this->select( "count({$this->__df->__primaryKey}) as num" );
        $res = $this->gets( $df );
        $total = 0;
        if ( true == is_array( $res ) ) {
            foreach ( $res as $k => $v ) {
                if ( true == isset( $v[ 'num' ] ) && true == is_numeric( $v[ 'num' ] ) ) {
                    $total += $v[ 'num' ];
                }
            }
        }

        return $total;
    }

    /**
     * @param null $builder
     *
     * @return mixed
     */
    public function gets( $builder = null, $cacheTime = null, $hint = NULL, $use_master = FALSE )
    {
        if ( true == isset( $builder ) ) {
            $this->_setContent( $builder );
        }

        if ( isset( $this->__fields ) ) {
            $sql = 'SELECT ' . $this->__fields;
        } else {
            $sql = 'SELECT *';
        }
        $params = [ ];
        // 获取成员变量的参数

        if ( true === is_array( $this->__content ) ) {
            foreach ( $this->__content as $field => $value ) {
//            $key = ':v_' . count( $params );
                $key = ':' . $field;
                $params[ $key ] = $value;
                $this->__params[ $key ] = $value;
//                $fields[ $this->quote( $field ) ] = $key;
//                $fields[ $this->quote( $field ) ] = $this->warpFieldValue($field,$key);
            }
        }

        $tableName = $this->_getTableName();
        $sql .= sprintf( ' FROM %s ', $tableName );
        $sql = $this->appendWhere( $sql );
        $sql = $this->appendOrder( $sql );
        $sql = $this->appendLimit( $sql );
        $params = $this->getParams();

        $shardParams = $this->getShardParams();
        $this->setSchemaName( $this->_getSchemaName() );
        $this->setTableName( $this->_getTableName() );
        $stmtName = md5( $sql . serialize( $params ) );

        $this->reg( $stmtName, $sql );
        $res = $this->find( $stmtName, $params, null, null, false, $shardParams );


        return $res;
    }

    private function repareParams()
    {

    }


    private function _setContent( $builder = null )
    {
        if ( true == isset( $builder ) ) {
            if ( $builder instanceof IModel ) {
                $this->__df = $builder;
                // 获取成员变量的参数
                $this->setMemberVariableToParams();
            } elseif ( is_array( $builder ) ) {
                if ( isset( $this->__content ) === true )
                    $this->__content = $this->__content + $builder;
                else {
                    $this->__content = $builder;
                }
            }
        }
    }

    /**
     *
     * @param
     * @param       $builder
     *
     * @return \PDOStatement
     * @throws \Exception
     */
    public function mget( $keys, $hint = NULL, $use_master = FALSE )
    {
        $criteria = new Criteria();
        $criteria->type = $criteria->getConstant( 'TYPE_IS_MGET' );
        $criteria->tableName = $this->__tableName;
        $criteria->dbName = $this->__schemaName;
        $criteria->pk = $this->__primaryKey;
        $criteria->params = $keys;
        $criteria->hint = $hint;
        $criteria->dbDebug = $this->dbDebug;
        $criteria->use_master = ( $use_master !== FALSE ) ? TRUE : FALSE;
        $stmt = new Statement( $criteria );
        $res = $stmt->execute();

        return $res;
    }
   /**基于对象构建的参数类型 **/
    public function insert( $builder = null )
    {
        $this->_setContent( $builder );
        $params = [ ];
        $fields = [ ];

        if ( true === is_array( $this->__content ) ) {
            foreach ( $this->__content as $field => $value ) {
                $key = ':v_' . count( $params );
                    $params[$key] = $value;
                $this->__params[ $key ] = $value;

                 $fields[ $this->quote( $field ) ] = $this->warpFieldValue($field,$key);
            }
        }
        $sql = sprintf('INSERT INTO %s %s VALUES %s', $this->__df->__tableName, $this->wrap($this->comma(array_keys($fields))), $this->wrap($this->comma(array_values($fields))));
        $this->setSchemaName( $this->__df->__schemaName );
        $this->setTableName( $this->__df->__tableName );
        $stmtName = md5( $sql );
        $this->reg( $stmtName, $sql );

        return $this->execute( $stmtName, $this->getParams(), null, $this->getShardParams(), self::SYNC_TYPE_INSERT );
    }


    private function  warpFieldValue($field,$key)
    {
        $warpField = $this->__df->warpField;
        if( $warpField[$field]){
            $key = sprintf("$warpField[$field]",$key);
            $fields[ $this->quote( $field ) ] = $key;
        }
        return $key;
    }


    private function getTableName()
    {
        return $this->__tableName;
    }

    private function getDbName()
    {
        return $this->__schemaName;
    }


    public function update( $builder = null )
    {

        if ( true == isset( $builder ) ) {
            $this->_setContent( $builder );
        }
//         $data = $builder->getArrayCopy();
        // 获取成员变量的参数
        $params = [ ];
        $fields = [ ];
        // 获取成员变量的参数

        if ( true === is_array( $this->__content ) ) {
            foreach ( $this->__content as $field => $value ) {
//            $key = ':v_' . count( $params )
                $key = ':v_' . count( $params );
                $params[ $key ] = $value;
                $this->__params[ $key ] = $value;
                $fields[ $this->quote( $field ) ] = $this->warpFieldValue($field,$key);
            }
        }
        $sql = sprintf( 'UPDATE %s SET ', $this->_getTableName() );
//        de($params = $this->getParams());
        list ( $params, $part ) = $this->makeAssignStatements( $this->__content );
        $sql .= $part;

        $sql = $this->appendWhere( $sql );
        $sql = $this->appendOrder( $sql );
        $sql = $this->appendLimit( $sql );

        $this->setSchemaName( $this->_getSchemaName() );
        $this->setTableName( $this->_getTableName() );
        $stmtName = md5( $sql );
        $this->reg( $stmtName, $sql );

        return $this->execute( $stmtName, $this->getParams(), null, $this->getShardParams(), self::SYNC_TYPE_UPDATE );


//        return $statement->rowCount();
    }

    public function delete( $builder = null )
    {
//         $data = $builder->getArrayCopy();
        if ( true == isset( $builder ) ) {
            $this->_setContent( $builder );
        }
//         $data = $builder->getArrayCopy();
        // 获取成员变量的参数
        $params = [ ];
        $fields = [ ];
        // 获取成员变量的参数
        if ( true === is_array( $this->__content ) ) {
            foreach ( $this->__content as $field => $value ) {
                $key = ':v_' . count( $params );
                $params[ $key ] = $value;
                $this->__params[ $key ] = $value;
                $fields[ $this->quote( $field ) ] = $key;
            }
        }
        $sql = sprintf( 'DELETE FROM %s ', $this->_getTableName() );

        $sql = $this->appendWhere( $sql );
        $sql = $this->appendOrder( $sql );
        $sql = $this->appendLimit( $sql );

        $this->setSchemaName( $this->_getSchemaName() );
        $this->setTableName( $this->_getTableName() );
        $stmtName = md5( $sql );
        $this->reg( $stmtName, $sql );

        return $this->execute( $stmtName, $this->getParams(), null, $this->getShardParams(), self::SYNC_TYPE_DELETE );

    }


    public function setDebug( $debug = true )
    {
        $this->dbDebug == $debug;
    }


    /**
     * this 依赖于本身的find
     *
     * @param unknown $df_name
     * @param unknown $stmt_name
     * @param string  $params
     * @param string  $hint
     * @param string  $use_master
     */
    public function findFirst( $stmt_name, $params = NULL, $hint = NULL, $use_master = FALSE, $shardParams = null )
    {

        $tmp = $this->find( $stmt_name, $params, NULL, 1, $use_master, $shardParams );
        $row = ( is_array( $tmp ) && !empty( $tmp ) ) ? current( $tmp ) : $tmp;

        return $row;
    }


    /**
     *
     * @param unknown $stmt_name
     * @param string  $params
     * @param string  $offset
     * @param string  $limit
     * @param string  $hint
     * @param string  $use_master
     *
     * @return mixed
     */
    public function find( $stmt_name, $params = NULL, $offset = NULL, $limit = NULL, $use_master = FALSE, $shardParam = null )
    {
        //先从chache中查看
//        $sql = $this->sql;
        $res = $this->getCache( $stmt_name, $params );

        $this->setPkValue( $params );

        if ( $res ) {
            $this->clean();
            return $res;
        }
        $criteria = new Criteria();
        $criteria->type = $criteria->getConstant( 'TYPE_IS_FIND' );
        $criteria->tableName = $this->_getTableName();
        $criteria->dbName = $this->_getSchemaName();
        $criteria->use_master = $use_master;

        if ( true == isset( $shardParam ) ) {
            $criteria->shardParams = $shardParam;
        }


        $criteria->statement = $this->getStmt( $stmt_name );  //语句有了
        $criteria->stmt_name = $stmt_name;//语句有了

        $criteria->params = $params;
        //语句扩展的参数
        $criteria->offset = $offset;
        $criteria->limit = $limit;
        $criteria->dbDebug = $this->dbDebug;

        $stmt = new Statement( $criteria );
        $res = $stmt->execute();
        // 写入缓存
        $this->setCache( $stmt_name, $params, $res );

        $this->clean();

        return $res;
    }
//
    /**
     *
     * @param string $stmt_name
     * @param array  $params
     * @param mixed  $hint
     * @param string $use_master
     *
     * @return boolean
     */
    private function toValue( $stmt_name, $params = NULL, $hint = NULL, $use_master = FALSE )
    {
        $criteria = new Criteria();
        $criteria->type = $criteria->getConstant( 'TYPE_IS_VALUE' );
        $criteria->stmt_name = $stmt_name;
        $criteria->tableName = $this->_getTableName();
        $criteria->dbName = $this->__schemaName;
        $criteria->params = $params;
        $criteria->hint = $hint;
        $criteria->dbName = $this->__schemaName;
        $criteria->db = $this->getDb();
        $criteria->dbDebug = $this->dbDebug;
        $criteria->use_master = ( $use_master !== FALSE ) ? TRUE : FALSE;
//         $criteria->extra_dsn = $extra_dsn;

        $stmt = new Statement( $criteria );

        return $stmt->execute();

    }

    private function getStmt( $stmt_key = null )
    {
        if ( true === isset( $stmt_key ) && true === isset( $this->querys[ $stmt_key ][ 'sql' ] ) ) {
            $query = $this->querys[ $stmt_key ][ 'sql' ];
        } elseif ( true === isset( $this->sql ) ) {
            $query = $this->sql;
        }
        if ( false === isset( $query ) ) {
            throw new \Exception( "query statment  is not exist" );
        }

        return $query;
    }


    public function execute( $stmt_name, $params = NULL, $hint = NULL, $shardParam = null, $syncType = null )
    {
        $criteria = new Criteria();
        $criteria->type = $criteria->getConstant( 'TYPE_IS_EXEC' );
        $criteria->params = $params;
        $criteria->tableName = $this->_getTableName();
        $criteria->dbName = $this->_getSchemaName();
        $criteria->shardParams = $this->getShardParams();
        $criteria->hint = $hint;

        if ( true == isset( $shardParam ) ) {
            $criteria->shardParams = $shardParam;
        } else {
            $criteria->shardParams = $params;
        }
        $criteria->statement = $this->getStmt( $stmt_name );  //语句有了
        $criteria->use_master = TRUE;
        $criteria->dbDebug = $this->dbDebug;
        $stmt = new Statement( $criteria );


        $res = $stmt->execute();

        if ( $res !== false ) {
            $this->updateSync( $criteria->statement, $params, self::SYNC_TYPE_INSERT );
        }
        $this->clean();

        return $res;
    }

    /**
     * @param null $params
     * @param null $hint
     *
     * @return array|null
     */
    public function lastInsertId( $params = null, $hint = NULL )
    {
        $criteria = new Criteria();
        $criteria->type = $criteria->getConstant( 'TYPE_IS_LAST_INSERT_ID' );
        $criteria->hint = $hint;
        $criteria->params = $params;
        $criteria->tableName = $this->_getTableName();

        $criteria->dbName = $this->_getSchemaName();
        $criteria->use_master = TRUE;
        $criteria->dbDebug = $this->dbDebug;
        $stmt = new Statement( $criteria );

        return $stmt->execute();
    }

    /**
     * @param null $params
     * @param null $hint
     * @ 需要传入参数 否则会对所有库进行事务启动 这可能就是之前问题的根源 。另外呢不同切分算法 最好都进行事务启动和提交，相同算法的可以公用事务
     * 因为跨库缘故可能事务！
     *
     * @return array|null
     */

    public function beginTransaction( $params = null, $hint = NULL )
    {
        $criteria = new Criteria();
        $criteria->type = $criteria->getConstant( 'TYPE_IS_BEGIN' );
        $criteria->hint = $hint;
        $criteria->tableName = $this->_getTableName();
        $criteria->dbName = $this->_getSchemaName();

        $criteria->shardParams = $this->getShardParams();
        $criteria->params = $params;
        $criteria->use_master = TRUE;
        $criteria->dbDebug = $this->dbDebug;
        $stmt = new Statement( $criteria );

        return $stmt->execute();
    }

    /**
     * @description
     *
     * //<<<<<<< HEAD
     * //     * @param unknown $df_name
     * //=======
     * //>>>>>>> feature/easyBuild
     * @param unknown $hint
     */
    function commit( $params = null, $hint = NULL )
    {
        $criteria = new Criteria();
        $criteria->type = $criteria->getConstant( 'TYPE_IS_COMMIT' );
        $criteria->params = $params;
        $criteria->tableName = $this->_getTableName();
        $criteria->dbName = $this->_getSchemaName();
        $criteria->shardParams = $this->getShardParams();
        $criteria->dbDebug = $this->dbDebug;

        $criteria->use_master = TRUE;

        $stmt = new Statement( $criteria );

        return $stmt->execute();
    }

    /**
     *
     * @param unknown $hint
     */
    public function rollBack( $params = null, $hint = NULL )
    {
        $criteria = new Criteria();
        $criteria->type = $criteria->getConstant( 'TYPE_IS_ROLLBACK' );
        $criteria->tableName = $this->_getTableName();
        $criteria->dbName = $this->_getSchemaName();
        $criteria->shardParams = $this->getShardParams();
        $criteria->params = $params;
        $criteria->dbDebug = $this->dbDebug;
        $criteria->use_master = TRUE;

        $stmt = new Statement( $criteria );

        return $stmt->execute();
    }

    public function createDB( $params = null, $hint = NULL )
    {
        $criteria = new Criteria();
        $criteria->type = $criteria->getConstant( 'TYPE_IS_CREATE_DB' );
        $criteria->dbName = $this->__schemaName;
        $criteria->params = $params;
        $criteria->use_master = TRUE;
        $criteria->dbDebug = $this->dbDebug;
        $stmt = new Statement( $criteria );

        return $stmt->execute();
    }


    /**
     * @todo
     **/
//    public function __call( $method, $params = null )
//    {
//        // 基于注释的写法
//        /**
//        $class = new \ReflectionClass( $this->dfName );
//        $doc = $class->getMethod( $method )->getDocComment();
//        $qpat = '/query\s*([^\n]*)/';
//        preg_match_all( $qpat, $doc, $qm );
//        $query = $qm[ 1 ][ 0 ];
//        $st = new Statement();
//
////      $query = $st->find($query, current($params));
//         * **/
////        return $query;
//    }


    public static function __callStatic( $name, $arguments )
    {
        $self = new static();

        return $self->find( $name, $arguments );
    }


    ##TODO cach 有问题需要调整成 cache方式
    /**
     * @param null $stmt_name
     * @param null $params
     */
    private function setCache( $stmtName = null, $params = null, $res = null, $cacheTime = null )
    {
        $redisClient = self::getRedisCacheService();
        if ( !$redisClient ) {
            return false;
        }

        if ( is_null( $cacheTime ) )
            $cacheTime = $this->getCacheTime( $stmtName );
        if ( $cacheTime === false ) {
            return false;   //没有设置缓存时间直接返回
        }

        $cacheKey = $this->cacheKey( $stmtName, $params );

        $res = $redisClient->setex( $cacheKey, $cacheTime, serialize( $res ) );

        if ( false !== $res && true == isset( $this->cacheSync ) ) {
            $this->setSyncListen( $params, $cacheKey, $cacheTime );
        }
    }


    private function getCache( $stmtName = null, $params = null )
    {
        $redisClient = self::getRedisCacheService();
        if ( !$redisClient ) {
            return false;
        }
        $cacheTime = $this->getCacheTime( $stmtName );
        if ( false === $cacheTime ) {
            return false;
        }
        $cacheKey = $this->cacheKey( $stmtName, $params );
        $res = $redisClient->get( $cacheKey );

        if ( true == isset( $this->cacheSync ) ) {
            $this->setSyncListen( $params, $cacheKey, $cacheTime );
        }

        if ( $res ) {
            return unserialize( $res );
        } else {
            return false;
        }
    }

    private function setSyncListen( $params, $cacheKey, $lifeTime )
    {
        $rcs = self::getRedisCacheService();
        if ( true == isset( $rcs ) ) {
            CacheSyncManager::setPredis( $rcs );
            $this->setPkValue( $params );
            $csm = new CacheSyncManager( $this->_getSchemaName(), $this->_getTableName(), $this->getPkValue() );
            $csm->updateListen( $cacheKey, $lifeTime, $this->cacheSync );
        }
    }

    private function updateSync( $statment, $params, $syncType )
    {
        if ( false === isset( $syncType ) ) {
            $mn = preg_match( '/^insert\s+into/usi', trim( $statment ) );
            if ( $mn > 0 ) {
                $syncType = self::SYNC_TYPE_INSERT;
            } else {
                $syncType = self::SYNC_TYPE_UPDATE;
            }
        }
        $rcs = self::getRedisCacheService();
        if ( true == isset( $rcs ) ) {
            CacheSyncManager::setPredis( $rcs );
            $this->setPkValue( $params );
            $csm = new CacheSyncManager( $this->_getSchemaName(), $this->_getTableName(), $this->getPkValue() );
            switch ( $syncType ) {
                case self::SYNC_TYPE_INSERT:
                    $csm->insertSync();
                    break;
                case self::SYNC_TYPE_UPDATE:
                case self::SYNC_TYPE_DELETE:
                default:
                    $csm->updateSync();
            }
        }
    }

    private function cacheKey( $stmtName = null, $params = null )
    {
        $serializeParams = function ( $params ) {
            $k = serialize( $params );


            return $k;
        };
        $cacheKey = md5( sprintf( "DBCACHE:%s:%s", $stmtName, $serializeParams( $params ) ) );
//        $cacheKey = sprintf( "DATABASECACHE:%s:%s:%s", $this->__tableName, $stmt_name, $serializeParams( $params ) );
        return $cacheKey;
    }

    private function getCacheTime( $stmtName )
    {
        if ( isset( $this->querys[ $stmtName ] ) && isset( $this->querys[ $stmtName ][ 'cache' ] ) && $this->querys[ $stmtName ][ 'cache' ] > 0 && is_numeric( $this->querys[ $stmtName ][ 'cache' ] ) ) {
            $cacheTime = intval( trim( $this->querys[ $stmtName ][ 'cache' ] ) );
        } else {
            $cacheTime = false;
        }

        return $cacheTime;
    }

    public function clean()
    {
        $this->__content = null;
        $this->__fields = null;
        $this->__table = null;
        $this->__where = null;
        $this->__order = null;
        $this->__limit = null;
        $this->__offset = null;
        $this->__params = [ ];
        $this->cleanMemberVariable();
        $this->cacheSync = null;
        $this->cacheTime = null;
        $this->__shardParams = null;
//        $this->__whereParamNames = null;
//        $this->__df= null;
    }

    // 将返回的结果转换会为模型对象，默认师叔祖
    public function obj(){
            $this->obj = true;
    }


}

