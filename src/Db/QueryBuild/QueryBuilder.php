<?php
namespace Wisp\Db\QueryBuild;

//use PDO;
use ReflectionClass;
use Wisp\System\Util;
use Wisp\Db\SQL\Sharding;

// use Wisp\Db\QueryBuild\Builder\AbstractBuilder;
// 这里要把查询最终的构造和 最终的执行分开
class QueryBuilder extends QueryData
{

    // const COMMA = ', ';
    // const QUOTE = '`%s`';
    // const PARENTHESIS = '(%s)';
    var $__sql = null;
    protected $__result;
    /**
     *
     * @var PDO
     */
//    protected $pdo;

    public function __construct( $db = null )
    {
        if ( !is_null( $db ) )
            $this->__db = $db;
    }


    /**
     * @return static
     * factory
     */
    static public function instance()
    {
        return new static();
    }
    /**
     * @return static
     * factory
     */
//<<<<<<< HEAD
//    static public function query()
//    {
//        return new static();
//    }
//    public function selectFirst()
//    {
//        return $this->select()->fetch();
//    }
//
//    // /**
//    // * This is top method for insert and update remove only one row !
//    // * @param IModel $model
//    // * @return number
//    // */
//    // public function save(IModel $model)
//    // {
//    // // $q = static::query();
//    // // if ($model->isRowExists()) {
//    // // $q->content = $model->getRowDataForUpdate();
//
//    // // if (empty($q->content)) {
//    // // return 0;
//    // // }
//
//    // // return $q->locate($model)->runUpdate();
//    // // } else {
//    // // $q->content = $model->getRowData();
//    // // return $model->onInserted($q->runInsert());
//    // // }
//    // }
//
//    // public function remove(IModel $model)
//    // {
//    // // $q = static::query();
//
//    // // return $q->locate($model)->limit(1)->runDelete();
//    // }
//
//    /**
//     *
//     * @param
//     *            $target
//     * @return $this
//     * @throws \Exception
//     */
//    public function locate($target)
//    {
//        // $driver = static::getDriver();
//        // $target = $this->convertLocateTarget($target);
//
//        // switch (count(static::$pk)) {
//        // case 0:
//        // throw new \Exception('PK undefined');
//        // default:
//        // array_walk(static::$pk, function ($fld) use ($target, $driver) {
//        // call_user_func([$this, 'andWhere'], $driver->quote($fld), '=', $this->param($target[$fld]));
//        // });
//        // }
//
//        // return $this;
//    }
//    public function init()
//    {
//        return self;
//    }
//    public function find()
//    {
//        if (isset($this->__fields)) {
//            $sql = 'SELECT ' . $this->__fields;
//        } else {
//            $sql = 'SELECT *';
//        }
//        // 获取成员变量的参数
//        $this->setMemberVariableToParams();
//
//        $sql .= sprintf(' FROM %s ', $this->__tableName);
//        $sql = $this->appendWhere($sql);
//        $sql = $this->appendOrder($sql);
//        $sql = $this->appendLimit($sql);
//        $statement = $this->__execute($sql, $this->__params);
//
//        return $statement;
//    }
//    public function insert($builder = null, array $pk = [])
//    {
//        $this->_insert($builder);
//        return $this->pdo->lastInsertId();
//    }
//    public function update($builder = null)
//    {
////         $data = $builder->getArrayCopy();
//        $sql = sprintf('UPDATE %s SET ',$this->__tableName);
//        // 获取成员变量的参数
//        $this->setMemberVariableToParams();
//        list ($params, $part) = $this->makeAssignStatements( $this->__content );
//
//
//        $sql .= $part;
//
//        $sql = $this->appendWhere($sql);
//        $sql = $this->appendOrder($sql);
//        $sql = $this->appendLimit($sql);
//
////         return $sql;
//
//        // if (isset($data['params'])) {
//        // $params += $data['params'];
//        // }
//
//        $statement = $this->__execute($sql, $params);
//
//        return $statement->rowCount();
//    }
//    public function delete($builder)
//    {
////         $data = $builder->getArrayCopy();
//
//        $sql = sprintf('DELETE FROM %s ', $this->__tableName);
//
//        $sql = $this->appendWhere($sql);
//        $sql = $this->appendOrder($sql);
//        $sql = $this->appendLimit($sql);

//=======
//    static public function query()
//    {
//        return new static();
//    }
//>>>>>>> feature/easyBuild


    /**
     *
     * @param unknown $builder
     */
    public function qcount( $builder )
    {
        $data = $builder->getArrayCopy();
         $sql = sprintf('SELECT COUNT(*) FROM %s.%s ', $data['db'], $data['table']);
        $sql .= sprintf( ' FROM %s ', $this->__tableName );

        $sql = $this->appendWhere( $data, $sql );

        return $sql;

        // $statement = $this->__execute($sql, $data['params']);
        // return (int)$statement->fetchColumn();
    }

    /**
     *
     * @param
     *            $data
     * @param
     *            $sql
     *
     * @return string
     */
    protected function appendWhere( $sql )
    {
        if ( false == empty( $this->__where ) ) {
            $sql .= ' WHERE ' . $this->__where;
            $this->__where = '';
        }

        return $sql;
    }

    /**
     *
     * @param
     *            $data
     * @param
     *            $sql
     *
     * @return string
     */
    protected function appendOrder( $sql )
    {
        if ( false == empty( $this->__order ) ) {
            $sql .= ' ORDER BY ' . $this->__order;
            $this->__order = '';
        }

        return $sql;
    }

    /**
     *
     * @param
     *            $data
     * @param
     *            $sql
     *
     * @return string
     */
    protected function appendLimit( $sql )
    {
        if ( false == empty( $this->__limit ) ) {
            $sql .= ' LIMIT ';
            if ( false == empty( $this->__offset ) ) {
                $sql .= $this->__offset . ',';
            }

//            $sql .= $data['limit'];
            $sql .= $this->__limit;
            $this->__limit = '';
        }

        return $sql;
    }


    protected function getParams()
    {
        // 增加从df获取成员变量参数的方法
        return  $this->__params;

    }

    // 设置sharding 参数
    public  function setShardParams($shardParams){
        $this->__shardParams = $shardParams;
        return $this;
    }

    protected function getShardParams(){
        if(true == empty($this->__shardParams))
        {
            return $this->getParams();
        }else {
            return $this->__shardParams;
        }
    }

    public function cleanShardValue()
    {
        $this->__shardParams = null;
        return $this;
    }




    /**
     *
     * @param
     *            $sql
     * @param
     *            $params
     *
     * @return \PDOStatement
     * @throws \Exception
     */
//    protected function __execute( $sql, $params = null ,$shardParams )
//    {
//        if ( is_null( $params ) ) {
//            $params = $this->__params;
//        }
//
//        $sql = Sharding::instance( $sql, $shardParams , $this->__schemaName, $this->__tableName )->generator();
//
//        $this->pdo = $this->db->connect();
//        $statement = $this->pdo->prepare( $sql );
//
//
//        $statement->setFetchMode( PDO::FETCH_ASSOC );
//        foreach ( $params as $key => &$value ) {
//            if ( substr( $key, 0, 3 ) === ':o_' ) {
//                $statement->bindParam( $key, $value, PDO::PARAM_INPUT_OUTPUT, 255 );
//            } else {
//                $statement->bindValue( $key, $value );
//            }
//        }
//
//        $success = $statement->execute();
//        if ( false === $success ) {
//            throw new \Exception( json_encode( [
//                $statement->errorCode(),
//                $statement->errorInfo()
//            ] ) );
//        }
//
//        // $params的out参数有引用，这里通过copy一次的方式解除引用
//        $this->__params = [ ];
//        foreach ( $params as $k => $v ) {
//            $this->__params[ $k ] = $v;
//        }
//
//        return $statement;
//    }

    /**
     *
     * @param
     * @param       $builder
     * @param array $content
     *
     * @return array
     */
    protected function makeAssignStatements( array $content )
    {
        $params = [ ];
        $updates = [ ];
        foreach ( $content as $field => $value ) {
            $key = ':v_' . count( $params );
//            $key = ':' . $field;
            $params[ $key ] = $value;
            $this->__params[ $key ] = $value;
            $updates[] = sprintf( '%s = %s', $this->quote( $field ), $key );
        }

        $part = $this->comma( $updates );
        return [
            $this->__params,
            $part
        ];
    }



    /**
     * 把把模型类成员变量值赋给 query查询参数 @TODO
     *
     * @param unknown $className
     */
    protected function setMemberVariableToParams($obj = null)
    {
        if(true == is_null($this->__df)){
            return false;
        }

        $obj = (true === isset($obj)) ?: $this->__df ;
        // if(false === class_exists($className)) return;
        $className = get_class( $obj );
        $class = new ReflectionClass( $className );
        $prop = $class->getProperties();
        if ( true === empty( $prop ) ) return;

        foreach ( $prop as $key => $propObj ) {
            if ( $propObj->class == $className && $propObj->name != 'fields' ) {
                $value = $propObj->getValue($obj );
                if ( true === isset( $value ) ) {
                    $this->__shardParams[ $propObj->name ] = $value;
                    $this->__content[ $propObj->name ] = $value;
                }
            }
        }
    }


    /**
     * 把把模型类成员变量值赋给 query查询参数 @TODO
     *
     * @param unknown $className
     */
    protected function cleanMemberVariable($obj = null)
    {
        if(true == is_null($this->__df)){
            return false;
        }

        $obj = (true === isset($obj)) ?: $this->__df ;
        // if(false === class_exists($className)) return;
        $className = get_class( $obj );
        $class = new ReflectionClass( $className );
        $prop = $class->getProperties();
        if ( true === empty( $prop ) ) return;

        foreach ( $prop as $key => $propObj ) {
            if ( $propObj->class == $className && $propObj->name != 'fields' ) {
                  $propObj->setValue($obj, null );
            }
        }
    }

    /**
     * only in get  or findfirst will give value to member
     */
    protected function setQueryResultToModelMemberVariable( $result = null )
    {
        if ( is_null( $result ) ) return;
        $className = get_class( $this );
        $obj = new \ReflectionObject( $this );
        $properties = $obj->getProperties();
        foreach ( $properties as $key => $propObj ) {
            if ( $propObj->class == $className && $propObj->name != 'fields' && isset( $result[ $propObj->name ] ) ) {
                $this->$propObj->name = $result[ $propObj->name ];
            }
        }
    }
}