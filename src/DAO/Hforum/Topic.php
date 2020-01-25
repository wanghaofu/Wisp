<?php
namespace Wisp\DAO\Hforum;
use Wisp\Db\QueryBuild\IModel;
/**
* @description 
**/
class Topic extends IModel {
   var $topic_id; // 帖子id 
   var $cat_id; // 分类id 
   var $title; // 帖子标题 
   var $uuid; // 用户id 
   var $user; // 用户信息 
   var $isTop;
   var $ctime;
   var $mtime;
   const F_TOPIC_ID = 'topic_id'; // 帖子id 
   const F_CAT_ID = 'cat_id'; // 分类id 
   const F_TITLE = 'title'; // 帖子标题 
   const F_UUID = 'uuid'; // 用户id 
   const F_USER = 'user'; // 用户信息 
   const F_ISTOP = 'isTop'; //  
   const F_CTIME = 'ctime'; //  
   const F_MTIME = 'mtime'; //  
   static $fields=['topic_id', 'cat_id', 'title', 'uuid', 'user', 'isTop', 'ctime', 'mtime'];
   function __construct($data=null,$dbName=null){
     $this->__schemaName = 'hforum';
     $this->__tableName = 'topic';
     $this->__primaryKey = 'topic_id'; // 帖子id   
}
}
