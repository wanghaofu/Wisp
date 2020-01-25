<?php
namespace Wisp\DAO\Hforum;
use Wisp\Db\QueryBuild\IModel;
/**
* @description 
**/
class TopicBack extends IModel {
   var $id; // 回帖id 
   var $topic_id; // 帖子id 
   var $pid; // 回帖父id 
   var $uuid; // 用户id 
   var $user; // 用户信息 
   var $content; // 回帖内容 
   var $ctime;
   var $mtime;
   const F_ID = 'id'; // 回帖id 
   const F_TOPIC_ID = 'topic_id'; // 帖子id 
   const F_PID = 'pid'; // 回帖父id 
   const F_UUID = 'uuid'; // 用户id 
   const F_USER = 'user'; // 用户信息 
   const F_CONTENT = 'content'; // 回帖内容 
   const F_CTIME = 'ctime'; //  
   const F_MTIME = 'mtime'; //  
   static $fields=['id', 'topic_id', 'pid', 'uuid', 'user', 'content', 'ctime', 'mtime'];
   function __construct($data=null,$dbName=null){
     $this->__schemaName = 'hforum';
     $this->__tableName = 'topic_back';
     $this->__primaryKey = 'id'; // 回帖id   
}
}
