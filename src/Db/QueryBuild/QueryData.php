<?php

namespace Wisp\Db\QueryBuild;

use ArrayObject;

/**
 * Class AbstractBuilder
 *
 * @package @@desc base query parame repare 扩展ArrayObject 隐式声明了 查询参数的成员变量
 *
 * //  * extends \ArrayObject
 */
class QueryData
{

    const COMMA = ', ';

    const QUOTE = '`%s`';

    const PARENTHESIS = '(%s)';

    const CONNECTION = 'default';

    protected static $pk = [ ];

    protected $__df;


    protected $__fields;
    protected $__table;
    protected $__where;
    protected $__order;
    protected $__limit;
    protected $__offset;
    protected $__content;
    protected $__params = [ ];
    protected $__shardParams;
//    protected $__whereParamNames;
    static $fields;

    public function __construct( $input = [ ] )
    {
        // parent::__construct($input + [
        // 'params' => [],
        // ], self::ARRAY_AS_PROPS);
    }


    // if use where then must set this params  must is an array

    public function setShardParam( $shardingParams )
    {
        if ( true == is_array( $shardingParams ) ) {
            $this->__shardParams = $shardingParams;
        }

    }


    /**
     *
     * @param $field
     * @param $value
     *
     * @return $this
     */
    const DB = '`passport`';
    // ############### #########################
    /**
     *等于
     *
     * @param $field
     * @param $value
     *
     * @return $this
     */
    public function eq( $field, $value )
    {
        $this->__shardParams[ $field ] = $value;

        return $this->whereFieldOp( $field, '=', $value );
    }

    /**
     * 不等于
     *
     * @param $field
     * @param $value
     *
     * @return $this
     */
    public function neq( $field, $value )
    {
        return $this->whereFieldOp( $field, '<>', $value );
    }


    /**
     * 大于
     *
     * @param $field
     * @param $value
     *
     * @return $this
     */
    public function gt( $field, $value )
    {
        return $this->whereFieldOp( $field, '>', $value );
    }

    /**
     *小于
     *
     * @param $field
     * @param $value
     *
     * @return $this
     */
    public function lt( $field, $value )
    {
        return $this->whereFieldOp( $field, '<', $value );
    }

    /**
     *大于等于
     *
     * @param $field
     * @param $value
     *
     * @return $this
     */
    public function gte( $field, $value )
    {
        return $this->whereFieldOp( $field, '>=', $value );
    }

    /**
     *小于等于
     *
     * @param $field
     * @param $value
     *
     * @return $this
     */
    public function lte( $field, $value )
    {
        return $this->whereFieldOp( $field, '<=', $value );
    }




    // ################# underline write method is read hard #############################


    // ###################### @todo base set #####################

    /**
     * 标准查询字符串
     *
     * @param unknown $fields
     */
    public function select( $fields )
    {
        $this->__fields = $fields;

        return $this;
    }

    /**
     * 标准查询表名
     *
     * @param unknown $table
     */
    public function from( $table )
    {
        $this->__tableName = $table;

        return $this;
    }

    /**
     *
     * @param array $data
     */
    public function content( array $data )
    {
        $this->__content = $data;

        return $this;
    }
    // ########################

    // ########## 查询参数 准备方法 ##################
    public function limit( $limit, $offset = null )
    {
        $this->__limit = $limit;
        $this->__offset = $offset;

        return $this;
    }


    /**
     *
     * @param
     *            $args
     *
     * @return $this
     */
    public function where( $args )
    {

        $args = func_get_args();
        if ( true === isset( $args[ 1 ] ) && true === is_array( $args[ 1 ] ) ) {
            $this->whereParam( $args[ 0 ], $args[ 1 ] );
            unset( $args[ 1 ] );
        }

        return call_user_func_array( [
            'self',
            '_where'
        ], array_map( [
            'static',
            'quoteField'
        ], $args ) );
    }

    public function whereParam( $args, $params )
    {

//        if ( is_string( $args ) ) {
//            $this->paramWhereParam( $args );
//        }
//        if(true === isset($args[2]) && true === is_array($args[2])){
        if ( true === isset( $params ) && true === is_array( $params ) ) {
            foreach ( $params as $key => $value ) {
                $this->param( $value, $key );
            }
        }

        return $this;
    }
//    protected function paramWhereParam( $args )
//    {
//        $pattern = "/:([\w_]+)/usi";
//        preg_match_all( $pattern, $args, $matches );
//        $this->__whereParamNames = $matches[ 1 ];
//        de( $this->__whereParamNames );
//    }

    /**
     *
     * 先给参数加括号，然后调用父的andwhere 方法 这个where可以直接设置字符串按照正常途径设置字符组合
     *
     * @param
     *            $args
     *
     * @return $this
     */
    public function andWhere( $args )
    {
        return call_user_func_array( [
            'self',
            '_andWhere'
        ], array_map( [
            'static',
            'quoteField'
        ], func_get_args() ) );
    }

    /**
     *
     * @param
     *            $args
     *
     * @return $this
     */
    public function order( $args )
    {
        return call_user_func_array( [
            'self',
            '_order'
        ], array_map( [
            'static',
            'quoteField'
        ], func_get_args() ) );
    }

    /**
     *
     * @param
     *            $field
     * @param
     *            $value
     *
     * @return $this
     */
    protected function whereFieldOp( $field, $op, $value )
    {
//        return $this->andWhere( $field, $op, $this->param( $value, $field ) );
        return $this->andWhere( $field, $op, $this->param( $value ) );
    }



    // ############### this method is call for the QueryStrSet ###############
    /**
     *
     * @param
     *            $args
     *
     * @return $this
     */
    private function _where( $args )
    {
        if ( isset( $this->where ) ) {
            trigger_error( 'overriding where clause', E_USER_WARNING );
        }
        $this->__where = implode( ' ', func_get_args() );

        return $this;
    }

    private function _andWhere( $args )
    {
        $exp = implode( ' ', func_get_args() );
        if ( isset( $this->__where ) ) {
            $this->__where .= ' AND ' . $exp;
        } else {
            $this->__where = $exp;
        }

        return $this;
    }

    private function _order( $args )
    {
        if ( isset( $this->__order ) ) {
            trigger_error( 'overriding order clause', E_USER_WARNING );
        }
        $args = func_get_args();
        $this->__order = implode( ' ', $args );

        return $this;
    }

    public function setParam( $params )
    {

        if ( true === isset( $params ) && true == is_array( $params ) ) {
            $this->__params = ( false === empty( $this->__params ) ) ? array_merge( $this->__params, $params ) : $params;
        }

    }
    // ############################ #######################
    /**
     * repare??
     *
     * @param unknown $value
     * @param unknown $key
     *
     * @return string
     */
    protected function param( $value, $key = null )
    {
        if ( is_null( $key ) ) {
            $key = 'p_' . count( $this->__params );
        }

//<<<<<<< HEAD
//    public function params(array $values, $keyPrefix = null)
//    {
//
//        if (is_null($keyPrefix)) {
//            $keyPrefix = 'p_' . count($this->__params);
//        }
//
//        $keys = [];
//        foreach ($values as $k => $value) {
//            $key = ':' . $keyPrefix . '_' . $k;
//            $this->__params[$key] = $value;
//            $keys[] = $key;
//=======
        $key = ":$key";
        if ( true === isset( $key ) ) {
            $this->__shardParams[ $key ] = $value;
//>>>>>>> feature/easyBuild
        }
        $this->__params[ $key ] = $value;

        return $key;
    }

//    public function params( array $values, $keyPrefix = null )
//    {
//
//        if ( is_null( $keyPrefix ) ) {
//            $keyPrefix = 'p_' . count( $this->__params );
//        }
//
//        $keys = [ ];
//        foreach ( $values as $k => $value ) {
//            $key = ':' . $keyPrefix . '_' . $k;
//            $this->__params[ $key ] = $value;
//            $keys[] = $key;
//        }
//        $this->__params[ $key ] = $value;
//
//        return $key;
//    }

    /**
     *
     * @param unknown $arg
     *
     * @return string|unknown
     */
    protected function quoteField( $arg )
    {
//        $fields = static::$fields
        if ( true === isset( $this->__df ) ) {
            $fields = $this->__df->fields();
            if ( in_array( $arg, $fields ) ) {
                $arg = self::quote( $arg );
            }
        }

        return $arg;

    }

    protected static function comma( $values )
    {
        return implode( static::COMMA, $values );
    }

    protected static function wrap( $inner )
    {
        return sprintf( static::PARENTHESIS, $inner );
    }

    protected static function quote( $inner )
    {
        return sprintf( static::QUOTE, $inner );
    }
}

?>