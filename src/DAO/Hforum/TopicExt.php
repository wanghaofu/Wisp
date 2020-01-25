<?php
namespace Wisp\DAO\Hforum;
use Wisp\Db\QueryBuild\IModel;
/**
* @description 
**/
class TopicExt extends IModel {
   var $topic_id; // 帖子id 
   var $content; // 帖子内容 
   var $ctime;
   var $mtime;
   const F_TOPIC_ID = 'topic_id'; // 帖子id 
   const F_CONTENT = 'content'; // 帖子内容 
   const F_CTIME = 'ctime'; //  
   const F_MTIME = 'mtime'; //  
   static $fields=['topic_id', 'content', 'ctime', 'mtime'];
   function __construct($data=null,$dbName=null){
     $this->__schemaName = 'hforum';
     $this->__tableName = 'topic_ext';
     $this->__primaryKey = 'topic_id'; // 帖子id   
}
}
