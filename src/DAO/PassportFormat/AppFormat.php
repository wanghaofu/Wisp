<?php
namespace Wisp\DAO\PassportFormat;
use Wisp\Db\QueryBuild\IModel;
/**
* @description 
**/
class AppFormat extends IModel {
   var $app_id; // 自增id 
   var $name; // 服务名称 
   var $app_key; // 服务链接密钥 
   const F_APP_ID = 'app_id'; // 自增id 
   const F_NAME = 'name'; // 服务名称 
   const F_APP_KEY = 'app_key'; // 服务链接密钥 
   static $fields=['app_id', 'name', 'app_key'];
   function __construct($data=null,$dbName=null){
     $this->__schemaName = 'passport';
     $this->__tableName = 'app';
     $this->__primaryKey = 'app_id'; // 自增id   
}
}
