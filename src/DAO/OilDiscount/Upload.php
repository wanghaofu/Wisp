<?php
namespace Wisp\DAO\OilDiscount;
use Wisp\Db\QueryBuild\IModel;
/**
* @description 
**/
class Upload extends IModel {
   var $id; // 图片id 
   var $oil_discount_id;
   var $url; // 文件路径 相对的 
   var $uuid;
   var $ctime; // 创建时间 
   var $mtime; // 修改时间 
   const F_ID = 'id'; // 图片id 
   const F_OIL_DISCOUNT_ID = 'oil_discount_id'; 
   const F_URL = 'url'; // 文件路径 相对的 
   const F_UUID = 'uuid'; 
   const F_CTIME = 'ctime'; // 创建时间 
   const F_MTIME = 'mtime'; // 修改时间 
   static $fields=['id', 'oil_discount_id', 'url', 'uuid', 'ctime', 'mtime'];
   function __construct($data=null,$dbName=null){
     $this->__schemaName = 'oil_discount';
     $this->__tableName = 'upload';
     $this->__primaryKey = 'id'; // 图片id   
}
}
