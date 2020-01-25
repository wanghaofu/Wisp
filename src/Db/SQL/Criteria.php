<?php
namespace Wisp\Db\SQL;
use Wisp\Db\Object;
use Wisp\Exception\DBException;
class Criteria extends Object
{
    // ----[ Class Constants ]----------------------------------------
    const TYPE_IS_GET = 0x01;

    const TYPE_IS_MGET = 0x02;

    const TYPE_IS_FIND = 0x03;

    const TYPE_IS_VALUE = 0x04;

    const TYPE_IS_EXEC = 0x05;

    const TYPE_IS_LAST_INSERT_ID = 0x06;

    const TYPE_IS_BEGIN = 0x07;

    const TYPE_IS_COMMIT = 0x08;

    const TYPE_IS_ROLLBACK = 0x09;

    const TYPE_IS_CREATE_DB = 0x0A;

    public /* int */     $type = NULL;
//<<<<<<< HEAD
//
//    public /* string */  $df_name = NULL;
//
//    public               $tableName =null;
//
//    public /* string */  $stmt_name = NULL;
//
//    public  /* string */ $statement = null;
//
//    public /* mixed */   $params = NULL;
//
//    public               $pk = null;
//
//    public /* int */     $offset = NULL;
//
//    public /* int */     $limit = NULL;
//
//    public /* int */     $hint = NULL;
//
//    public /* boolean */ $use_master = FALSE;
//
//    public /* array */   $extra_dsn = NULL;
//
//    public               $db =null;
//
//    public              $dbName = null;
//
//    public               $dbDebug = false;
//
//=======
    public               $tableName =null;
    public /* string */  $stmt_name = NULL;
    public  /* string */ $statement = null;
    public /* mixed */   $params = NULL;
    public               $shardParams = null;
    public               $pk = null;
    public /* int */     $offset = NULL;
    public /* int */     $limit = NULL;
    public /* int */     $hint = NULL;
    public /* boolean */ $use_master = FALSE;
    public /* array */   $extra_dsn = NULL;
    public               $db =null;
    public               $dbName = null;
    public               $dbDebug = false;
//>>>>>>> feature/easyBuild
    public final /* mixed */
    function __get(/* string */ $name)
    {
        if (property_exists($this, $name) === FALSE) {
            $ex_msg = 'Permission denied to get criteria {name} %s';
            $ex_msg = sprintf($ex_msg, $name);
            throw new DBException($ex_msg);
        }
        return $this->$name;
    }
    public final /* void */
    function __set(/* string */ $name,
        /* mixed  */ $val)
    {
        if (property_exists($this, $name) === FALSE) {
            $ex_msg = 'Permission denied to set criteria {name} %s';
            $ex_msg = sprintf($ex_msg, $name);
            throw new DBException($ex_msg);
        }
        $this->$name = $val;
    }
    
    public /* string */
    function serialize(/* void */)
    {
        $data = array();
        $filter = \ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PROTECTED | \ReflectionProperty::IS_PRIVATE;
        foreach ($this->getProperties($filter) as $prop) {
            $name = $prop->getName();
            $data[$name] = $this->$name;
        }
        return serialize($data);
    }
    public /* void */
    function unserialize(/* string */ $serialized)
    {
        parent::__construct();
        $data = unserialize($serialized);
        foreach ($data as $name => $value) {
            $this->$name = $value;
        }
    }
}
;
