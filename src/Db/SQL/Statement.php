<?php
namespace Wisp\Db\SQL;

use Wisp\Db\SQL\Criteria;
use Wisp\Db\BaseObject;
use Wisp\Db\SQL\Sharding;
use Wisp\Factory\DbDefaultFactory\DbFactory;

class Statement
{
    const FETCH_MODE_NUM = 0x01;
    const FETCH_MODE_ASSOC = 0x02;
    const BIND_TYPE = 'type';
    const BIND_VAL = 'value';
    const BIND_ORIG_TYPE = 'orig-type';
    const BIND_ORIG_VAL = 'orig-value';
    const VAR_TYPE_IS_NULL = 'NULL';
    const VAR_TYPE_IS_BOOL = 'boolean';
    const VAR_TYPE_IS_LONG = 'integer';
    const VAR_TYPE_IS_DOUBLE = 'double';
    const VAR_TYPE_IS_STRING = 'string';
    const P_HOLDER_STMT = 'stmt';
    const P_HOLDER_BIND = 'bind';
    const P_HOLDER_NAME = 'name';




    /**
     *
     * @var Criteria
     */
    protected $criteria = NULL;

    /**
     *
     * @var DataFormat
     */
    protected $data_format = NULL;

    /**
     *
     * @var string
     */
    protected $query = NULL;

    /**
     *
     * @var array
     */
    protected $bind_values = [ ];

    /**
     *
     * @var array
     */
    protected $bind_value_details = [ ];


    protected $db = null;

    protected $sharding = NULL;


    protected $global = false; //全局查询，在分库分表情况下， 默认不开启全局查询 没有主键会报错

    protected $count = false;
    /**
     * 初始化重要 入口
     * @param Criteria $criteria
     * @throws DBException
     */
    public function __construct( Criteria $criteria )
    {
        $this->criteria = $criteria;
    }
    /**
     * This is  mixed
     *
     * @param string $ignore_icptr
     */
    public function execute( $ignore_icptr = FALSE )
    {
        $this->prepare();
        return $this->perform();
    }

    /**
     * only get mget find value exec is for repare
     * @throws DBException
     */
    private final function prepare()
    {
        $this->init();
        switch ( $this->criteria->type ) {
            case Criteria::TYPE_IS_GET:
                $this->prepareForGet();
                break;
            case Criteria::TYPE_IS_MGET:
                $this->prepareForMultiGet();
                break;
            case Criteria::TYPE_IS_FIND:
            case Criteria::TYPE_IS_VALUE:
            case Criteria::TYPE_IS_EXEC:
                $this->prepareForGeneric();
                break;
            case Criteria::TYPE_IS_CREATE_DB:
                $this->prepareForCreateDB();
                break;
            case Criteria::TYPE_IS_LAST_INSERT_ID:
            case Criteria::TYPE_IS_BEGIN:
            case Criteria::TYPE_IS_COMMIT:
            case Criteria::TYPE_IS_ROLLBACK:
                break;
            default:
                $ex_msg = 'Unsupported execution type of Criteria {type} %d';
                $ex_msg = sprintf( $ex_msg, $this->criteria->type );
                throw new DBException( $ex_msg );
        }
    }

    /**
     *
     * @throws DBException
     */
    private final function init()
    {
        $this->query = NULL;
        $this->bind_values = [ ];
        if ( ( $this->criteria instanceof Criteria ) === FALSE ) {
            $ex_msg = 'Invalid Criteria type {class} %s';
            $ex_msg = sprintf( $ex_msg, get_class( $this->criteria ) );
            throw new DBException( $ex_msg );
        }
    }

    /**
     * @param null $params
     *
     * @throws DBException
     */
    private function prepareForGet( $params = null )
    {
        // 抽出KEY
        $cardinal_key = $this->criteria->pk;
        if ( $cardinal_key === NULL ) {
            $ex_msg = 'Not found required primary_key, or fetch_key {df} %s';
            $ex_msg = sprintf( $ex_msg, get_class( $this->data_format ) );
            throw new DBException( $ex_msg );
        }
        // query 实体准备
        if ( is_array( $cardinal_key ) === FALSE ) {
            $query = 'SELECT * FROM __TABLE_NAME__ WHERE %1$s = :%1$s';
            $query = sprintf( $query, $cardinal_key );
            $params = [
                $cardinal_key => $this->criteria->params
            ];
        } else {
            // 结合构造
            $condition = NULL;
            foreach ( $cardinal_key as $_key ) {
                $condition = ( $condition === NULL ) ? sprintf( '%1$s = :%1$s', $_key ) : sprintf( '%2$s AND %1$s = :%1$s', $_key, $condition );
            }
            $query = 'SELECT * FROM __TABLE_NAME__ WHERE ' . $condition;
            $params = $this->criteria->params;
        }
        $this->processQuery( $query, $params );
    }
    /**
     * preparefor statement and params
     */
    private function prepareforGeneric()
    {
        $query = $this->criteria->statement;
        $params = $this->criteria->params;
        $this->processQuery( $query, $params );
    }

    private function prepareforCreateDB()
    {

    }


    /**
     * sharding in here
     * @param $sql
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    private function shardingQuery( $dbIdx = null )
    {
        return $this->sharding->generator();
    }

    private final function perform()
    {
        $result = null;
        $shardParams = (true === isset($this->criteria->shardParams))?$this->criteria->shardParams : $this->criteria->params;
        $sharding = Sharding::instance( $this->query, $shardParams, $this->criteria->dbName, $this->criteria->tableName );
        $this->sharding = &$sharding;
        $splitValue = $sharding->getSplitValue();
        $splitDBFun = $sharding->getDbSplitCallFun();
        //存在分库
        if ( true === isset($splitValue) ) {
            $result = $this->_perform();
        } elseif( $splitDBFun ) { //具有分库的全库搜索
            $dbArr = $sharding->getDbIdxArr();
            if ( $dbArr && is_array( $dbArr ) ) {
                foreach ( $dbArr as $dbIdx ) {
                    $sharding->setDbIdx( null, $dbIdx );  //db 连接的时候要用到
//                    if( $this->count && $this->count >= $this->criteria->limit)
                    $data = $this->_perform();
                    if ( is_array( $result ) && is_array( $data ) )
                        $result = array_merge( $result, $data );
                    else {
                        $result = $data;
                    }

                }
                $this->sharding->setDbIdx( null );  //重置
            }
        }else{ // 无分库
            $result = $this->_perform();
        }


        return $result;
    }

    private final function _perform()
    {
        $result = false;
        $driver = $this->getDriver();

        $is_success = TRUE;
        switch ( $this->criteria->type ) {
            case Criteria::TYPE_IS_LAST_INSERT_ID:
                $result = $is_success = $driver->lastInsertId();
                break;
            case Criteria::TYPE_IS_BEGIN:
                $result = $is_success = $driver->begin();
                break;
            case Criteria::TYPE_IS_COMMIT:
                $result = $is_success = $driver->commit();
                break;
            case Criteria::TYPE_IS_ROLLBACK:
                $result = $is_success = $driver->rollback();
                break;
            case Criteria::TYPE_IS_GET:
            case Criteria::TYPE_IS_MGET:
            case Criteria::TYPE_IS_FIND:
            case Criteria::TYPE_IS_VALUE:

            $querys = $this->shardingQuery();
            $params = $this->criteria->params;

            if ( !is_array( $querys ) ) {
                $querys = [ $querys ];
            }

            foreach ( $querys as $query ) {

                $is_success = $driver->query( $query, $params );
                if ( $is_success ) {
                    $data = $this->fetchResult();
                    if ( is_array( $result ) && is_array( $data ) )
                        $result = array_merge( $result, $data );
                    else {
                        $result = $data;
                    }
                }
            }

                break;
            case Criteria::TYPE_IS_EXEC:  //这个要区分析啊
                //do ready
                //this is return pdoStatement
                $querys = $this->shardingQuery();
                $params = $this->criteria->params;

                if ( !is_array( $querys ) ) {
                    $querys = [ $querys ];
                }
                foreach ( $querys as $query ) {
                    $is_success = $driver->query( $query, $params );
                    if ( false === $is_success ) {
                        throw new DBException($driver->getErrorMsg(), $driver->errorCode());
                    }else{
                        $data = $this->fetchResult();
                        if (  $result  && is_numeric( $data ) )
                        {
                            $result += $data ;
                        }else{
                            $result = $data ;
                        }
                    }
                }

                break;
            case Criteria::TYPE_IS_CREATE_DB:
                $result = $is_success = $driver->query( $this->query, $this->bind_values );
                break;
            default:
                $ex_msg = 'Unsupported execution type of Criteria {type} %d';
                $ex_msg = sprintf( $ex_msg, $this->criteria->type );
                throw new DBException( $ex_msg );
        }
        // 这里也抛出了异常
//        if ( $is_success === FALSE ) {
//            throw new DBException($driver->getErrorMsg(), $driver->errorCode());
//        }

        return $result;
    }


    function fetchResult()
    {
        switch ( $this->criteria->type ) {
            case Criteria::TYPE_IS_GET:
                $result = $this->fetchResultForGet();
                break;
//            case Criteria::TYPE_IS_MGET:
//                $result = $this->fetchResultForMultiGet();
//                break;
            case Criteria::TYPE_IS_FIND:
                $result = $this->fetchResultForFind();
                break;
            case Criteria::TYPE_IS_VALUE:
                $result = $this->fetchResultForValue();
                break;
            case Criteria::TYPE_IS_EXEC:
                $result = $this->fetchResultForExec();
                break;
            default:
                $ex_msg = 'Unsupported execution type of Criteria {type} %d';
                $ex_msg = sprintf( $ex_msg, $this->criteria->type );
                throw new DBException( $ex_msg );
        }

        return $result;
    }

    function fetchResultForGet()
    {
        $result = NULL;
        $driver = $this->getDriver();
        $fetch_mode = self::FETCH_MODE_ASSOC;
        $result = $this->fetchResultForFind();

        return $result;
    }


    public function fetchResultForFind(/* void */ )
    {
        $result = [ ];
        $driver = $this->getDriver();

        return $driver->fetch();

    }

    function fetchResultForValue()
    {
        return $this->getDriver()->fetch_one();
    }

    function fetchResultForExec(/* void */ )
    {
        return $this->getDriver()->affected_rows();
    }

    private function processQuery( $query, $params )
    {
        $pholder = [ ];
//        $query = preg_replace( '/\s+/', ' ', $query );
        $pholder[ self::P_HOLDER_STMT ] = $query;
        $this->setupQuery( $pholder, $params );
    }

    protected function setupQuery( $pholder, $params )
    {
        $query = $pholder[ self::P_HOLDER_STMT ];


        if ( $this->criteria->limit !== NULL ) {
            $query .= ' LIMIT '. intval( $this->criteria->limit);
        }
        if ( $this->criteria->offset !== NULL ) {
            $query .= ' OFFSET '.intval($this->criteria->offset );
        }


        if ( strpos( $query, '__TABLE_NAME__' ) !== false ) {
            $tbl_name = $this->criteria->tableName;
            $query = str_replace( '__TABLE_NAME__', $tbl_name, $query );
        }
        $query = preg_replace( '/\x5c:/', ':', $query );
        $this->query = $query;
    }


    /**
     * 只构造连接
     * 表名 用来确认大概的数据库 切分于表切分参数
     */
    private final function getDriver( $dbIdx = null )
    {
        //需要sharding 能够根据表名 以及参数 返回一个表的Idx  然后 dbFactory才能确认一个连接
//        $this->criteria->dbName,$this->criteria->tableName,$this->criteria->params,$slave

        $slave = !$this->criteria->use_master;
        if ( !$dbIdx ) {
            $dbIdx = $this->sharding->getDbIdx();
        }
        $this->db = DbFactory::ConnDb( $this->criteria->dbName, $dbIdx, $slave );
        // 设置debug
        if ( $this->criteria->dbDebug == true ) {
            $this->db->setDebug();
        }

        return $this->db;

    }


}

class DBException extends \Exception
{
}
