<?php
namespace Wisp\Db\Driver;

use King\Core\Core;
/**
 * ****************************************************************
 * Name: 数据库操作类 ( 基于 PDO )
 * Author: 王涛 ( Tony )
 * Email: wanghaofu@163.com
 * QQ: 595900598
 * 2015年7月27日 Modify
 *
 * ****************************************************************
 */
/*
 * 示例 : $db = new db ( 'mysql:host=127.0.0.1;port=3306;', 'root', 'password',
 * 'database_name', true, 'utf8' );
 */

class Db
{

    var $id;

    var $dsn;

    var $user;

    var $password;

    var $database;

    var $charSet;

    var $ignoreError;

    var $attributes;

    var $conn;
// 数据库连接
    var $queryCount;
// 查询次数
    var $affectedRows;
// 影响行数 ( 每次 Query 后改变 )
    var $debug = true;
// 调试模式
    var $debugLineSplit = '<br />';

    var $charSplit = '`';

    var $transaction = false;

    var $inTransaction = false;


    var $readOnly = false;
// 是否只读
    var $startTime;

    var $endTime;

    var $statSql;

    var $statSqlLimit = 10;
// 历史 Query 条数
    var $traceEnabled = false;

    var $tracer = null;

    var $dbConfigs = [ ];

    var $writeOperations = [
        'ALTER ',
        'CREATE ',
        'DROP ',
        'DELETE ',
        'INSERT ',
        'REPLACE ',
        'TRUNCATE ',
        'UPDATE '
    ];


    var $query = null;

    var $le_result = null;


// 预备数据库配置
    var $dbIdxArray = [ ];

    var $multiFlag = false;

    var $split_value;

    var $nodeQuery = false;
    var $dbIdx = null;
    var $tableIdx = null;
    var $sqlCallFun = [ ];

    static $inDsnTransaction = [ ]; //dsn 共享事务对象 在一次连接中？
    // 定义写操作
    static $iquery = [ ];

    static $dbNodeConns = [ ];

    var $connTime = null;
    const CONN_EXPIRE_TIME = 10; //300 检查一次 主要针对cli模式

    var $cli = false;

// 构造默认
    public function __construct( $dsn = null, $user = '', $password = '', $database = null, $autoCommit = false, $charSet = null, $persistent = false, $ignoreError = false, $timeout = 10 )
    {
        $this->cli = ( php_sapi_name() == 'cli' ) ? true : false;

        if( true == $this->cli && 10 == $timeout)
        {
            $timeout = 300;
        }

        if ( is_array( $dsn ) ) {

            $this->db_multi( $dsn );
        } elseif ( !empty( $dsn ) ) {
            $this->dsn = $dsn;
            $this->user = $user;
            $this->password = $password;
            $this->database = $database;
            $this->charSet = $charSet;
            $this->ignoreError = $ignoreError;

            $this->id = md5( $this->dsn . $this->user . $this->database );

            $this->attributes = [
                \PDO::ATTR_AUTOCOMMIT => $autoCommit,
                \PDO::ATTR_PERSISTENT => $persistent,
                \PDO::ATTR_TIMEOUT    => $timeout
            ];

            $this->conn = null;
            $this->queryCount = 0;
            $this->startTime = '';
            $this->statSql = [ ];
            $this->endTime = '';
        }

    }

    public function setDebug( $debug = true )
    {
        $this->debug = true;
    }

    public function connByPdo( \PDO $pdo = null )
    {
        $this->conn = null;
        $this->queryCount = 0;
        $this->startTime = '';
        $this->statSql = [ ];
        $this->endTime = '';
        // 如果外部传入连接则直接给支
        if ( $pdo instanceof \PDO && !empty( $pdo ) ) {
            $this->conn = $pdo;
        } else {
            throw new \Exception( ' Is not Pdo object.' );
        }
    }

    public function setDatabase( $database )
    {
        $this->database = $database;
    }

// 初始化随几数据库配置
    public function db_multi( $dbConfigs = null )
    {
        $this->multiFlag = true;

        if ( is_array( $dbConfigs ) ) {
            $this->dbConfigs = $dbConfigs;
            $this->dbIdxArray = array_keys( $this->dbConfigs );
            shuffle( $this->dbIdxArray );
        }

        $dbIdxSession = $_COOKIE[ 'db_idx_session' ]; // 直接获取用户位置
        if ( is_numeric( $dbIdxSession ) ) //
        {
            $this->dbIdx = intval( $dbIdxSession );
        } else {
            $this->dbIdx = array_shift( $this->dbIdxArray );
        }

        $dbConfig = $this->dbConfigs[ $this->dbIdx ];
        $this->db( $dbConfig[ 'dsn' ], $dbConfig[ 'user' ], $dbConfig[ 'password' ], $dbConfig[ 'database' ], $dbConfig[ 'auto_commit' ], $dbConfig[ 'charset' ], $dbConfig[ 'persistent' ] );
    }


// 连接数据库
    public function connect( $expire = false )
    {
        $dsn = $this->dsn;
        $connKey = self::getConnKey( $dsn );
        $conn = &$this->conn;

        if ( !$conn || $expire ) {
            if ( ( isset( self::$dbNodeConns[ $connKey ] ) && self::$dbNodeConns[ $connKey ] instanceof \PDO ) && false == $expire ) { //如果连接存在
                $conn = self::$dbNodeConns[ $connKey ];
                $this->conn = $conn;
            } else {
                try {
                    $conn = new \PDO( $dsn, $this->user, $this->password, $this->attributes );
                    self::$dbNodeConns[ $connKey ] = $conn;
                    $this->conn = $conn;
                    if ( true === $expire ) {
                        Core::instance()->logger()->error( 'db lost re connect ok ! ' );
                    }
                    if ( $conn && $this->multiFlag )
                        setcookie( 'db_idx_session', $this->dbIdx ); // 保存库定位用于分布式
                } catch ( \PDOException $e ) {
                    if ( count( $this->dbIdxArray ) > 0 && $this->multiFlag ) {
                        setcookie( 'db_idx_session', null );
                        $_COOKIE[ 'db_idx_session' ] = null;
                        $this->db_multi();
                        $this->connect();
                    } elseif ( $this->ignoreError ) {
                        echo( "<ERROR><div style='padding:20px;'>服务器忙，请稍后访问！</div>" );
                    }
                    $this->log( $e->getMessage() );
                }
            }

            $this->useDatabase( $this->database, $conn );
            if ( $this->charSet && $conn ) {
                $conn->query( "set names '{$this->charSet}';" );
            }
            if ( $this->transaction ) {
                $this->begin();
            }
        }
        $this->connTime = time();

        return $conn;
    }


    /**
     * 检查连接是否可用
     *
     * @param Link $dbconn 数据库连接
     *
     * @return Boolean
     */
    private function pdo_ping()
    {
        try {
            if ( $this->conn ) {
                $serviceInfo = $this->conn->getAttribute( \PDO::ATTR_SERVER_INFO );
                Core::instance()->logger()->info( json_encode( $serviceInfo ) );
                if ( $serviceInfo == 'MySQL server has gone away' ) {
                    return false;
                }
            } else {
                return false;
            }
        } catch ( \PDOException $e ) {
            if ( strpos( $e->getMessage(), 'MySQL server has gone away' ) !== false ) {
                return false;
            }
        }

        return true;
    }


// 连接数据库 返回pdo对象 不选择数据库 不缓存
    public function connectOnlyPdo()
    {
        $dsn = $this->dsn;
        $connKey = self::getConnKey( $dsn );

        $conn = &$this->conn;
        if ( !$conn ) {
            if ( isset( self::$dbNodeConns[ $connKey ] ) && self::$dbNodeConns[ $connKey ] instanceof \PDO ) { //如果连接存在
                $conn = &self::$dbNodeConns[ $connKey ];
            } else {
                try {
                    $conn = new \PDO( $dsn, $this->user, $this->password, $this->attributes );
                } catch ( \PDOException $e ) {
                    if ( !$this->ignoreError ) {
                        echo( "<ERROR><div style='padding:20px;'>服务器忙，请稍后访问！</div>" );
                        throw ( $e );
                    }
                }
            }
            if ( $this->charSet && $conn ) {
                $conn->query( "set names '{$this->charSet}';" );
            }
            if ( $this->transaction ) {
                $this->begin();
            }
        }

        return $conn;
    }

// 使用数据库选择库
    public function useDatabase( $database, $conn = null )
    {
        if ( !$conn )
            $conn = $this->conn;
        if ( !$conn )
            return false;
        if ( $database ) {
            $conn->query( "USE $database;" );

            return !$conn->errorCode() ? true : false;
        }
    }

    public function transaction( $status = null )
    {

        if ( is_null( $status ) && isset( self::$inDsnTransaction[ $this->dsn ] ) )
            return self::$inDsnTransaction[ $this->dsn ];
        elseif ( is_null( $status ) && !isset( self::$inDsnTransaction[ $this->dsn ] ) ) {
            return self::$inDsnTransaction[ $this->dsn ] = false;
        } elseif ( false == is_null( $status ) ) {
            self::$inDsnTransaction[ $this->dsn ] = $status;
        }


    }

    /**
     * @return bool
     * 定期检查连接是否活动
     */
    private function checkConn()
    {
        if ( is_null( $this->connTime ) ) {
            $res = false;
        } else {
            $currentTime = time();
            $expireTime = $currentTime - $this->connTime;
            if ( $expireTime >= self::CONN_EXPIRE_TIME ) {
                if ( $ping = $this->pdo_ping() ) { //达到检查点后 西安进行连接测试 如果连接存在则返回真不尽兴连接重制
                    Core::instance()->logger()->notice( 'db status = ' . $ping );
                    $res = true;
                    $this->connTime = $currentTime;
                } else {
                    $res = false;
                }
            } else {
                $res = true;
//                Core::instance()->logger()->info( 'lastConnTime = ' . $expireTime );
            }
        }

        return $res;


    }

    public function conn()
    {
        $conn = &$this->conn;


        $cli = $this->cli;
        if ( $conn && false == $cli ) {
            return $conn;
        } elseif ( false == $cli ) {
            return $this->connect();
        }

        if ( true == $cli ) {
            $noExpire = $this->checkConn();
            if ( true == $noExpire && $conn ) {
                return $conn;
            } elseif ( false == $noExpire ) {
                return $this->connect( true );
            } else {
                return $this->connect();
            }
        }

    }
// #####事务处理代码
// 开始事务
    public function begin()
    {
        $res = true;
//        if ( !$this->conn ) {
        $this->conn();
//        }
        //获取全局状态
        if ( false == $this->transaction() ) {
            $res = $this->conn->beginTransaction();
            self::$inDsnTransaction[ $this->dsn ] = true;
        }

        return $res;
    }

// 提交事务
    public function commit()
    {
        $res = true;

        if ( !$this->conn() ) {
            throw new \Exception( 'conn is not exists ' );
        }
        if ( true == $this->transaction() ) {
            $res = $this->conn->commit();
            $this->transaction( false );
            $res = true;
        }

        return $res;
    }

// 回滚事务
    public function rollback()
    {
        $res = true;
        if ( !$this->conn() ) {
            throw new \Exception( 'conn is not exists ' );
        }

        if ( true == $this->transaction() ) {
            $res = $this->conn->rollback();
            $this->transaction( false );
        }

        return $res;
    }


    /**
     * Exceutes an SQL statement ,returning a result set as a PDOStatement obj
     *
     * @param unknown $strSql
     * @param string  $params
     *
     * @throws \Exception
     *
     * 执行失败 返回false  查询结果呢statmentPdo
     */
    public function query( $strSql, $params = null )
    {
        //重置pdostatment
        $this->le_result = null;
        //保存查询
        $this->query = $strSql;
        ####################### 分库分表的回调操作点 ###########################
        // debug 调试
        if ( $this->debug ) {
            if ( is_null($params) ) {
                Core::instance()->logger()->notice($strSql);
            }else{
                    Core::instance()->logger()->notice( $strSql, $params );
                }

        }
        // 只读操作
        if ( $this->readOnly ) {
            $writeOperations = $this->writeOperations;
            while ( list ( $key, $item ) = @each( $writeOperations ) ) {
                if ( preg_match( "/^$item/is", $strSql ) ) {
                    $this->affectedRows = 1;

                    return true;
                }
            }
        }

        //取得连接 延迟连接操作 只有在第一次查询的时候才链接
        $conn = $this->conn();

        //查询次数统计
        $this->queryCount++; // 查询次数增加
        $this->affectedRows = 0; // 重置影响行数

        //设置查询其实时间
        if ( empty( $this->startTime ) ) {
            $this->startTime = array_sum( explode( ' ', microtime() ) );
        }

        //保存查询 如果大于限定则 弹出
        array_push( $this->statSql, $strSql );
        if ( count( $this->statSql ) > $this->statSqlLimit )
            array_shift( $this->statSql );
        // 开始查询 调用连接资源
        try {
            $statement = $conn->prepare( $strSql );
            if ( !is_null( $params ) ) {
                foreach ( $params as $key => &$value ) {

                    $key = (false === strpos($key, ':')) ? ":" . $key : $key;

//                    de($key);
                        $bindRes = $statement->bindValue($key, $value);
//                    $bindRes = $statement->bindParam( $key, $value);
                    if ( $bindRes === false ) {
                        $this->log( "field: {$key} value: {$value} is error" );
                    }
                }
            }
            $statement->execute();
            if($statement->errorCode() != '00000'){
                $errorInfo = $statement->errorInfo();
                $this->log('SQL_ERROR:'.$strSql. ' ErrorMessage: ' .$errorInfo[2]);
            }
        } catch ( \PDOException $e ) {
            if ( $this->ignoreError ) {
                echo( "<ERROR><div style='padding:20px;'>服务器忙，请稍后访问！</div>" );
            }
            $res = false;
            $this->log( "SQL_ERROR: $strSql ! errorMessage: " . $e->getMessage() ); //prepare 的异常处理

        }

        $errorCode = $statement->errorCode();
        $res = ( $errorCode !== '00000' ) ? false : $statement;
        if ( true == $res ) {
            $this->affectedRows += intval( $statement->rowCount() ); //影响行数子集计算的！
            $this->le_result = $statement;
        } else {
            $errorInfo = $conn->errorInfo();

            throw new \Exception( ' db Execute fail' . $errorInfo[ 2 ] );  //这里执行失败必须抛出异常？ 意味着程序结束了吗？
        }

        return $res;
    }



###-------------------------------------under line is for statement----------------------------
    /**
     * !notice  this only for Statement insert update del
     * @throws \Exception
     */
    public function affected_rows( $sql = null )
    {
        if ( $sql ) {
            $this->query( $sql );
        }

        if ( $this->le_result instanceof \PDOStatement ) {
            $rowNum = $this->le_result->rowCount();
        } else {
            throw new \Exception ( 'PDOStatement Object is null This medhod call must by Statement' );
        }
        $this->le_result = NULL;

        return $rowNum;
    }

    /**
     * !!!! important for 查询
     * 可适用于 查询
     * 获取查询结果 ！ may be have sql
     *
     * @return Ambigous <boolean, multitype:>
     */
    public function fetch( $sql = null )
    {
        if ( $sql ) {
            $this->query( $sql );
        }
        if ( $this->le_result ) {
            $stmt = $this->le_result;
            $result = $stmt->fetchAll( \PDO::FETCH_ASSOC );
        } elseif ( is_string( $sql ) && !empty( $sql ) ) {
            $stmt = $this->query( $sql );
            if ( !$stmt )
                return false;
            $result = $stmt->fetch( \PDO::FETCH_ASSOC );
        } else {
            throw new \Exception( "DB Exception, SQL is Empty: [ {$sql} ]! " );
        }
        $this->le_result = NULL; //释放连接语句
        return $result;
    }

    public function fetch_all( $sql = null )
    {
        if ( $sql ) {
            $this->query( $sql );
        }
        $rows = [ ];
        while ( $tmp = $this->fetch() ) {
            $rows[] = $tmp;
        }

        return $rows;
    }

    public function fetch_one()
    {
        if ( $this->le_result === NULL ) {
            return NULL;
        }

        $row = mysqli_fetch_row( $this->le_result );
        restore_error_handler();
        $data = array_key_exists( 0, $row ) ? $row[ 0 ] : NULL;

        $this->le_result = NULL;

        // 取得結果を返す
        return $data;
    }

    /**
     * 适用 daemon开发 快速入手 用于 插入 删除 修改
     * 建议 直接执行sql delete updata insert 返回影响行数
     *
     * @param string $sql
     * @param bool   $commit
     *
     * @return number
     */
    public function exec( $sql, $commit = true )
    {
        $this->conn();
        $this->statSql[] = $sql;
        $res = $this->conn->exec( $sql ); //!
        if ( $commit == true ) {
            $this->commit();
        }

        return $res;
    }

    /**
     *
     * 查询数据， 获取单条
     *
     * @param string $sql
     * //完整的sql语句
     *
     */
    public function getRow( $sql )
    {
       $this->query( $sql );
        if ( !$this->le_result ) {
            return false;
        }else {
            $result = $this->le_result->fetch( \PDO::FETCH_ASSOC );
        }

        return $result;
    }

    /**
     * 查询多行 直接查询
     *
     * @param string $sql
     */
    public function getRows( $sql )
    {

        $this->query( $sql );
        if ( !$this->le_result ) {
            return false;
        }else {
            $result = $this->le_result ->fetchAll( \PDO::FETCH_ASSOC );
        }

        return $result;
    }

    /**
     * 统计符合条件的记录条数
     *
     * @param unknown $dbTable
     * @param string  $condition
     * @param string  $fields
     *
     * @return mixed|boolean
     */
    public function count( $dbTable, $condition = '', $fields = '*' )
    {
        if ( $condition != '' ) {
            $condition = "WHERE $condition";
        }
        $strSql = " SELECT COUNT($fields) AS count_records FROM $dbTable $condition";
        $res = $this->query( $strSql );
        if ( $res ) {
            $countRecords = $res->fetch( \PDO::FETCH_ASSOC );

            return $countRecords[ 'count_records' ];
        } else {
            return false;
        }
    }

    /**
     * 最后插入 ID 反回最后插入的id
     */
    public function lastInsertId()
    {
        if ( !$this->conn )
            return false;
        $lastInsertId = intval( $this->conn->lastInsertId() );

        return $lastInsertId;
    }

    // 关闭连接
    public function close()
    {
        if ( $this->traceEnabled && $this->tracer ) {
            $this->tracer->close();
        }
        $this->inTransaction = false;
        $this->conn = null;
    }

    // #---------------------------------------------------- I an is split line under is for slice database and table-----------------------------
    /**
     *
     * /**
     * for cache link make the key! 多服务器连接查询
     *
     * @param string $dsn
     */
    private static function getConnKey( $dsn )
    {
        $connkey = md5( $dsn );

        return $connkey;
    }


    /**
     * 获取错误代码
     * debug method
     * 错误调试相关代码
     */
    function errorCode( $conn = null )
    {
        if ( !$conn )
            $conn = $this->conn;
        if ( !$conn )
            return false;
        if ( $this->le_result ) {
            $errorCode = intval( $this->le_result->errorCode() );
        } else {
            $errorCode = intval( $conn->errorCode() );
        }

        return $errorCode;
    }

    // 获取数据执行错误信息
    public function getErrorMsg( $conn = null )
    {
        if ( !$conn )
            $conn = $this->conn;
        if ( !$conn )
            return false;

        if ( $this->le_result ) {
            $errorInfo = $this->le_result->errorInfo();
        } else {
            $errorInfo = $conn->errorInfo();
        }
        $errorMsg = $errorInfo[ 2 ];

        return $errorMsg;
    }

    // 输出调试 SQL
    public function showDebug()
    {
        $this->endTime = array_sum( explode( ' ', microtime() ) );
        while ( list ( $key, $item ) = @each( $this->statSql ) ) {
            echo $item . $this->debugLineSplit;
        }
        echo '<br />Query Time: ' . ( $this->endTime - $this->startTime ) . $this->debugLineSplit;
        if ( $this->conn && $this->conn->errorCode() > 0 ) {
            $errorInfo = $this->conn->errorInfo();
            echo 'Error: ' . $this->conn->errorCode() . ' - ' . $errorInfo[ 2 ] . $this->debugLineSplit;
        }
        echo $this->debugLineSplit;

        $this->startTime = '';
        $this->endTime = '';
        $this->statSql = [ ];
    }

    // 输出 用于内部调试 @todo may be write log
    private function log( $var, $sql = '' )
    {
        Core::instance()->logger()->error( $var );
        if ( $this->conn ) {
            Core::instance()->logger()->error( $this->getErrorMsg() . 'sql' . $sql );
        } else {
            $errorMessage = '';
        }
        if ( true == $this->debug ) {
            $breakLine = ( php_sapi_name() == 'cli' ) ? "\n" : '<br/>';
            echo $var . $breakLine;
            if ( $errorMessage ) {
                echo $errorMessage . $breakLine;
            }

        }
    }
}

?>

