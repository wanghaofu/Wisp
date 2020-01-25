<?php
namespace Wisp\DAO\Passport;
use Wisp\Db\DAO;
/**
* @description 
**/
class HmBind extends DAO {
   var $uuid; // 用户平台账户id 主账号 hmid 或者被第三方直接绑定的 
   var $pid; // 绑定的平台账号 
   var $ptype; // 平台类型 0:幻萌 1: google 2: facebook 3: 
   var $ctime;
   var $mtime;
   const F_UUID = 'uuid'; // 用户平台账户id 主账号 hmid 或者被第三方直接绑定的 
   const F_PID = 'pid'; // 绑定的平台账号 
   const F_PTYPE = 'ptype'; // 平台类型 0:幻萌 1: google 2: facebook 3: 
   const F_CTIME = 'ctime'; //  
   const F_MTIME = 'mtime'; //  
   static $fields=['uuid', 'pid', 'ptype', 'ctime', 'mtime'];
   function __construct($dbName=null){
     $this->__schemaName = 'passport';
     $this->__tableName = 'hm_bind';
     parent::__construct($dbName);
     $this->__primaryKey = 'uuid'; // 用户平台账户id 主账号 hmid 或者被第三方直接绑定的   
}
}
