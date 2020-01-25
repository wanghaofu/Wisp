<?php
/**
 * Created by PhpStorm.
 * User: tony
 * Date: 2017/9/29
 * Time: 17:24
 */

namespace Wisp\Db\QueryBuild;


abstract class IModel
{

    var $__schemaName;
    var $__tableName;
    var $__primaryKey; // 自增id

    static $fields;

//    const DB_NAME = 'test';
//    const TABLE_NAME = '';
//    const PRIMARY_KEY = '';

    // 返回模型本身
    static function init()
    {
        return new static;
    }

    public function fields()
    {
        return static::$fields;
    }

    public function __call($name,$arguments){
            $this->$name = current($arguments);
        return $this;
    }
    /**
     * 返回Cascade对象   参数是 模型本身
     * 乐死
     * $model = new imodel()
     * cascade($model)
     * 简写方式主要用于查询操作
     *
     **/
   static public function Cascade($data = null)
    {
        //TODO
        #修改和添加 操作不支持 参数没法传入
        return cascade( new static($data) );
    }
}