<?php
namespace Wisp\DAO\PassportFormat;
use Wisp\Db\QueryBuild\IModel;
/**
* @description 
**/
class PidBindFormat extends IModel {
   var $id; // 自增id 
   var $pid; // 用户平台账户id 
   var $uuid; // hm账号id 
   var $ptype; // 平台类型 0:幻萌 1: google 2: facebook 3: 
   var $ctime;
   var $mtime;
   var $bind_status; // 第三方绑定状态 0:和hm临时账号绑定 1: 和幻萌账号绑定 
   const F_ID = 'id'; // 自增id 
   const F_PID = 'pid'; // 用户平台账户id 
   const F_UUID = 'uuid'; // hm账号id 
   const F_PTYPE = 'ptype'; // 平台类型 0:幻萌 1: google 2: facebook 3: 
   const F_CTIME = 'ctime'; //  
   const F_MTIME = 'mtime'; //  
   const F_BIND_STATUS = 'bind_status'; // 第三方绑定状态 0:和hm临时账号绑定 1: 和幻萌账号绑定 
   static $fields=['id', 'pid', 'uuid', 'ptype', 'ctime', 'mtime', 'bind_status'];
   function __construct($data=null,$dbName=null){
     $this->__schemaName = 'passport';
     $this->__tableName = 'pid_bind';
     $this->__primaryKey = 'id'; // 自增id   
}
}
