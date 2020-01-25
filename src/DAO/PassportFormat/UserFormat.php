<?php
namespace Wisp\DAO\PassportFormat;
use Wisp\Db\QueryBuild\IModel;
/**
* @description 
**/
class UserFormat extends IModel {
   var $username; // 用户名 
   var $uuid;
   var $password; // 密码1 password_hash default 
   var $password2; // 密码2 md5 
   var $conflict; // 帐号是否冲突 不冲突 :0 ; 冲突: 1 次字段为临时 当冲突完成后 将清理掉 
   var $os; // 操作系统类型 andorid: 0 ; ios: 1  次字段为临时 当冲突完成后 将清理掉 
   var $ctime;
   var $mtime;
   const F_USERNAME = 'username'; // 用户名 
   const F_UUID = 'uuid'; //  
   const F_PASSWORD = 'password'; // 密码1 password_hash default 
   const F_PASSWORD2 = 'password2'; // 密码2 md5 
   const F_CONFLICT = 'conflict'; // 帐号是否冲突 不冲突 :0 ; 冲突: 1 次字段为临时 当冲突完成后 将清理掉 
   const F_OS = 'os'; // 操作系统类型 andorid: 0 ; ios: 1  次字段为临时 当冲突完成后 将清理掉 
   const F_CTIME = 'ctime'; //  
   const F_MTIME = 'mtime'; //  
   static $fields=['username', 'uuid', 'password', 'password2', 'conflict', 'os', 'ctime', 'mtime'];
   function __construct($data=null,$dbName=null){
     $this->__schemaName = 'passport';
     $this->__tableName = 'user';
     $this->__primaryKey = 'username'; // 用户名   
}
}
