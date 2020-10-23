<?php
namespace Wisp\DAO\OilDiscount;
use Wisp\Db\QueryBuild\IModel;
/**
* @description 
**/
class Upload extends IModel {
   var $id; // 图片id 
   var $oil_discount_id;
   var $uuid;
   var $ctime; // 创建时间 
   var $mtime; // 修改时间 
   var $fileName; // 文件路径 相对的 包含部分路径 
   const F_ID = 'id'; // 图片id 
   const F_OIL_DISCOUNT_ID = 'oil_discount_id'; 
   const F_UUID = 'uuid'; 
   const F_CTIME = 'ctime'; // 创建时间 
   const F_MTIME = 'mtime'; // 修改时间 
   const F_FILENAME = 'fileName'; // 文件路径 相对的 包含部分路径 
   static $fields=['id', 'oil_discount_id', 'uuid', 'ctime', 'mtime', 'fileName'];
   function __construct($data=null,$dbName=null){
     $this->__schemaName = 'oil_discount';
     $this->__tableName = 'upload';
     $this->__primaryKey = 'id'; // 图片id   
}
}
