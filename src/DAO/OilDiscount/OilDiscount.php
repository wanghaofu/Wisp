<?php
namespace Wisp\DAO\OilDiscount;
use Wisp\Db\QueryBuild\IModel;
/**
* @description 
**/
class OilDiscount extends IModel {
   var $coordinate; // 坐标id 
   var $oil_station_name; // 油站名称 
   var $uuid;
   var $ctime;
   var $mtime;
   var $id;
   var $jiu_er;
   var $jiu_wu;
   var $chai_you;
   var $longitude; // 经度 
   var $latitude; // 维度 
   var $start_time;
   var $end_time;
   var $jiu_ba;
   const F_COORDINATE = 'coordinate'; // 坐标id 
   const F_OIL_STATION_NAME = 'oil_station_name'; // 油站名称 
   const F_UUID = 'uuid'; 
   const F_CTIME = 'ctime'; 
   const F_MTIME = 'mtime'; 
   const F_ID = 'id'; 
   const F_JIU_ER = 'jiu_er'; 
   const F_JIU_WU = 'jiu_wu'; 
   const F_CHAI_YOU = 'chai_you'; 
   const F_LONGITUDE = 'longitude'; // 经度 
   const F_LATITUDE = 'latitude'; // 维度 
   const F_START_TIME = 'start_time'; 
   const F_END_TIME = 'end_time'; 
   const F_JIU_BA = 'jiu_ba'; 
   static $fields=['coordinate', 'oil_station_name', 'uuid', 'ctime', 'mtime', 'id', 'jiu_er', 'jiu_wu', 'chai_you', 'longitude', 'latitude', 'start_time', 'end_time', 'jiu_ba'];
   function __construct($data=null,$dbName=null){
     $this->__schemaName = 'oil_discount';
     $this->__tableName = 'oil_discount';
     $this->__primaryKey = 'id'; //    
}
}
