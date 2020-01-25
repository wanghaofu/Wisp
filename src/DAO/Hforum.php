<?php
namespace Wisp\DAO;

/** Don't modify this file, this is auto generator by Wisp **/
class Hforum
{
    var $__schemaName = 'hforum';

    //  
   static function Images()
   {
       require_once( __DIR__.'/Hforum/Images.php');
       return new Hforum\Images();
   }
    //  
   static function Topic()
   {
       require_once( __DIR__.'/Hforum/Topic.php');
       return new Hforum\Topic();
   }
    //  
   static function TopicBack()
   {
       require_once( __DIR__.'/Hforum/TopicBack.php');
       return new Hforum\TopicBack();
   }
    //  
   static function TopicCat()
   {
       require_once( __DIR__.'/Hforum/TopicCat.php');
       return new Hforum\TopicCat();
   }
    //  
   static function TopicExt()
   {
       require_once( __DIR__.'/Hforum/TopicExt.php');
       return new Hforum\TopicExt();
   }}