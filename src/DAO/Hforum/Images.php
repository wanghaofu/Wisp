<?php
namespace Wisp\DAO\Hforum;
use Wisp\Db\QueryBuild\IModel;
/**
* @description 
**/
class Images extends IModel {
   var $image_id; // 图片id 
   var $url;
   var $name;
   var $uuid; // 用户id 
   var $topic_id; // 帖子id 
   var $ctime;
   var $mtime;
   const F_IMAGE_ID = 'image_id'; // 图片id 
   const F_URL = 'url'; //  
   const F_NAME = 'name'; //  
   const F_UUID = 'uuid'; // 用户id 
   const F_TOPIC_ID = 'topic_id'; // 帖子id 
   const F_CTIME = 'ctime'; //  
   const F_MTIME = 'mtime'; //  
   static $fields=['image_id', 'url', 'name', 'uuid', 'topic_id', 'ctime', 'mtime'];
   function __construct($data=null,$dbName=null){
     $this->__schemaName = 'hforum';
     $this->__tableName = 'images';
     $this->__primaryKey = 'image_id'; // 图片id   
}
}
