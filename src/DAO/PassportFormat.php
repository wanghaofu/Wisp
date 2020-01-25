<?php
namespace Wisp\DAO;

/** Don't modify this file, this is auto generator by Wisp **/
class Passport
{
    var $__schemaName = 'passport';

    //  
   static function HmBind()
   {
       require_once( __DIR__.'/PassportFormat/HmBindFormat.php');
       return new PassportFormat\HmBindFormat();
   }
    //  
   static function PidBind()
   {
       require_once( __DIR__.'/PassportFormat/PidBindFormat.php');
       return new PassportFormat\PidBindFormat();
   }
    //  
   static function User()
   {
       require_once( __DIR__.'/PassportFormat/UserFormat.php');
       return new PassportFormat\UserFormat();
   }
    //  
   static function UserExt()
   {
       require_once( __DIR__.'/PassportFormat/UserExtFormat.php');
       return new PassportFormat\UserExtFormat();
   }
    //  
   static function UserService()
   {
       require_once( __DIR__.'/PassportFormat/UserServiceFormat.php');
       return new PassportFormat\UserServiceFormat();
   }}