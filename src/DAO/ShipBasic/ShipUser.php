<?php
namespace Wisp\DAO\ShipBasic;
//use Wisp\Db\DAO;
use Wisp\Db\QueryBuild\IModel;
/**
* @description 注册的用户表
**/
class ShipUser  extends IModel {
   var $puid;
   var $username;
   var $pwd;
   var $pwd2; // password_hash 密码 
   var $uuid;
   var $platform; // 平台 
   var $email;
   var $verify;
   var $regcode;
   var $invite_code;
   var $uid;
   var $audit_status; // 审核状态0 1 
   var $platform_puid; // 平台 puid 
   var $channel_name;
   var $create_time;
   const F_PUID = 'puid'; //  
   const F_USERNAME = 'username'; //  
   const F_PWD = 'pwd'; //  
   const F_PWD2 = 'pwd2'; // password_hash 密码 
   const F_UUID = 'uuid'; //  
   const F_PLATFORM = 'platform'; // 平台 
   const F_EMAIL = 'email'; //  
   const F_VERIFY = 'verify'; //  
   const F_REGCODE = 'regcode'; //  
   const F_INVITE_CODE = 'invite_code'; //  
   const F_UID = 'uid'; //  
   const F_AUDIT_STATUS = 'audit_status'; // 审核状态0 1 
   const F_PLATFORM_PUID = 'platform_puid'; // 平台 puid 
   const F_CHANNEL_NAME = 'channel_name'; //  
   const F_CREATE_TIME = 'create_time'; //  
   static $fields=['puid', 'username', 'pwd', 'pwd2', 'uuid', 'platform', 'email', 'verify', 'regcode', 'invite_code', 'uid', 'audit_status', 'platform_puid', 'channel_name', 'create_time'];
   function __construct($dbName=null){
     $this->__schemaName = 'ship_basic';
     $this->__tableName = 'ship_user';
//     parent::__construct($dbName);
     $this->__primaryKey = 'puid'; //    
}
}
