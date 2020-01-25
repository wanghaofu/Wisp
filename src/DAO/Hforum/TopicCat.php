<?php
namespace Wisp\DAO\Hforum;
use Wisp\Db\QueryBuild\IModel;
/**
* @description 
**/
class TopicCat extends IModel {
   var $id;
   var $pid;
   var $title; // 分类名称 
   var $description; // 说明 
   var $img;
   const F_ID = 'id'; //  
   const F_PID = 'pid'; //  
   const F_TITLE = 'title'; // 分类名称 
   const F_DESCRIPTION = 'description'; // 说明 
   const F_IMG = 'img'; //  
   static $fields=['id', 'pid', 'title', 'description', 'img'];
   function __construct($data=null,$dbName=null){
     $this->__schemaName = 'hforum';
     $this->__tableName = 'topic_cat';
     $this->__primaryKey = 'id'; //    
}
}
