<?php
namespace Wisp\Factory\PdoDbFactory;

/**
 * 第三方框架 ，通过其他平台已连接的pdo对象初始化这里
 */
use Wisp\Db\Driver\Db;

class DbFactory
{
    /**
     * 初始化全新的db为每一个数据库操作建立db *
     */
    public static function db(\PDO $pdo)
    {
        $db = new Db();
        $db->connByPdo($pdo);
        return $db;
    }


}

?>
