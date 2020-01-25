<?php
namespace Wisp\DAO\PassportMainFormat;
use Wisp\Db\QueryBuild\IModel;
/**
* @description 
**/
class AppFormat extends IModel {
   var $app_id; // 是用来标记你的开发者账号的, 是你的用户id, 这个id 在数据库添加检索, 方便快速查找 
   var $name; // 服务名称 
   var $ctime;
   var $mtime;
   const F_APP_ID = 'app_id'; // 是用来标记你的开发者账号的, 是你的用户id, 这个id 在数据库添加检索, 方便快速查找 
   const F_NAME = 'name'; // 服务名称 
   const F_CTIME = 'ctime'; //  
   const F_MTIME = 'mtime'; //  
   static $fields=['app_id', 'name', 'ctime', 'mtime'];
   function __construct($data=null,$dbName=null){
     $this->__schemaName = 'passport_main';
     $this->__tableName = 'app';
     $this->__primaryKey = 'app_id'; // 是用来标记你的开发者账号的, 是你的用户id, 这个id 在数据库添加检索, 方便快速查找   
}
}
