<?php
namespace Wisp\DAO\PassportMainFormat;
use Wisp\Db\QueryBuild\IModel;
/**
* @description 
**/
class AppKeyFormat extends IModel {
   var $id; // 自增id 
   var $app_id; // app 分配的id 
   var $app_key; // app_key 和 app_secret 是一对出现的账号, 同一个 app_id 可以对应多个 app_key+app_secret, 这样 平台就可以分配你不一样的权限 
   var $app_secret; // 服务链接密钥 
   var $ctime;
   var $mtime;
   const F_ID = 'id'; // 自增id 
   const F_APP_ID = 'app_id'; // app 分配的id 
   const F_APP_KEY = 'app_key'; // app_key 和 app_secret 是一对出现的账号, 同一个 app_id 可以对应多个 app_key+app_secret, 这样 平台就可以分配你不一样的权限 
   const F_APP_SECRET = 'app_secret'; // 服务链接密钥 
   const F_CTIME = 'ctime'; //  
   const F_MTIME = 'mtime'; //  
   static $fields=['id', 'app_id', 'app_key', 'app_secret', 'ctime', 'mtime'];
   function __construct($data=null,$dbName=null){
     $this->__schemaName = 'passport_main';
     $this->__tableName = 'app_key';
     $this->__primaryKey = 'id'; // 自增id   
}
}
