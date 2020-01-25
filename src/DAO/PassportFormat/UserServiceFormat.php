<?php
namespace Wisp\DAO\PassportFormat;
use Wisp\Db\QueryBuild\IModel;
/**
* @description 
**/
class UserServiceFormat extends IModel {
   var $id; // 自增id 
   var $uuid; // 用户ID 
   var $puid; // 老平台id 
   var $uid; // 游戏内角色id 
   var $agent; // 客户端标识 
   var $app_id; // app id ； 0为舰R 
   var $area_id; // 游戏分区id 
   var $device_id; // 设备id 
   var $ctime;
   var $mtime;
   const F_ID = 'id'; // 自增id 
   const F_UUID = 'uuid'; // 用户ID 
   const F_PUID = 'puid'; // 老平台id 
   const F_UID = 'uid'; // 游戏内角色id 
   const F_AGENT = 'agent'; // 客户端标识 
   const F_APP_ID = 'app_id'; // app id ； 0为舰R 
   const F_AREA_ID = 'area_id'; // 游戏分区id 
   const F_DEVICE_ID = 'device_id'; // 设备id 
   const F_CTIME = 'ctime'; //  
   const F_MTIME = 'mtime'; //  
   static $fields=['id', 'uuid', 'puid', 'uid', 'agent', 'app_id', 'area_id', 'device_id', 'ctime', 'mtime'];
   function __construct($data=null,$dbName=null){
     $this->__schemaName = 'passport';
     $this->__tableName = 'user_service';
     $this->__primaryKey = 'id'; // 自增id   
}
}
