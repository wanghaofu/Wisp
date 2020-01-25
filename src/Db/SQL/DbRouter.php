<?php
/**
 * Created by PhpStorm.
 * User: tony
 * Date: 2017/12/8
 * Time: 16:09
 */

namespace Wisp\Db\SQL;





class DbRouter
{


    function page($offset, $num)
    {

    }

    function getTableMaxRecorderNum(){

    }

    function getDbMaxRecorderNum(){

    }

    function getTableNum(){


    }

    // 集中初始化 还是分开
    /**
     * 分开初始化 ，分开的话 效率会高些 ，在特定情况下可以不用对整体进行重新计算
     *
     *
     * 不分开 统一初始化 的好处就是在单个的情况下 会有效率优势吗
     *
     *
     *
     *
     *
     *
     * 单元回归测试脚本
     *
     *
     *
     *
     */
}