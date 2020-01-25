<?php
/**
 * Created by PhpStorm.
 * User: tony
 * Date: 2017/9/22
 * Time: 11:03
 */

namespace Wisp\Db\SQL;

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
    function sql($sql)
    {
        $this->sql= $sql;
        return $this;
    }

    function cache($time)
    {
        $this->cacheTime = $time;
    }
}