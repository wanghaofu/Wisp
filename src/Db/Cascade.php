<?php
/**
 * Created by PhpStorm.
 * User: tony
 * Date: 2017/9/29
 * Time: 17:36
 */

namespace Wisp\Db;


class Cascade extends DAO
{
//    function __construct(Imodel $model)
    function __construct( $model = null )
    {
        if ( true === isset( $model ) ) {
            $this->__df = $model;
        }
//        $params =  $this->getParamsFromMember($this->__df);
//        if(false === empty($params)) {
//            $this->__params = array_merge( $params, $this->__params );
//        }
    }

}


