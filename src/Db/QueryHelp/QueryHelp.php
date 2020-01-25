<?php
/**
 * Created by PhpStorm.
 * User: tony
 * Date: 2017/9/22
 * Time: 11:03
 */

namespace Wisp\Db\QueryHelp;

## @Todo
//easy query build

trait QueryHelp
{

    private $cacheTime;
    private $sql;
    private $key;

    function key( $key )
    {
        if ( $key )
            $this->key = $key;
        else
            return $this->key;
    }


    function reg( $sql, $key = null )
    {
        if ( false == empty( $sql ) ) {
            $this->sql = $sql;
            $this->key = ( true === isset( $key ) ) ? $key : md5( $sql );
        }


        return $this;
    }

    function qcache( $time )
    {
        $this->cacheTime = $time;
    }

    function getParamsFromModel()
    {
//        $className = get_class($this);
        $class = \ReflectionObject( $this->__models );
//        $class = new \ReflectionClass($className);
        $prop = $class->getProperties();
        $params = [ ];
        if ( empty( $prop ) )
            return false;
        foreach ( $prop as $key => $propObj ) {
            if ( $propObj->class == $className && $propObj->name != 'fields' ) {

                $value = $propObj->getValue( $this );
                if ( !is_null( $value ) )
                    $params[ $propObj->name ] = $value;
            }
        }

        return $params;
    }

    function getParamsFromMember( $object )
    {
        $className = get_class( $object );

        $class = new \ReflectionClass( $className );

        $prop = $class->getProperties();
        $params = [ ];
        if ( empty( $prop ) )
            return false;
        foreach ( $prop as $key => $propObj ) {
            if ( $propObj->class == $className && $propObj->name != 'fields' ) {
                $value = $propObj->getValue( $object );
                if ( !is_null( $value ) )
                    $params[ $propObj->name ] = $value;
            }
        }

        return $params;
    }


}