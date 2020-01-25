<?php
namespace Wisp\DAO;

/** Don't modify this file, this is auto generator by Wisp **/
class Passport
{
    var $__schemaName = 'passport';

    //  
   static function HmBind()
   {
       require_once( __DIR__.'/Passport/HmBind.php');
       return new Passport\HmBind();
   }
    //  
   static function PidBind()
   {
       require_once( __DIR__.'/Passport/PidBind.php');
       return new Passport\PidBind();
   }
    //  
   static function User()
   {
       require_once( __DIR__.'/Passport/User.php');
       return new Passport\User();
   }
    //  
   static function UserExt()
   {
       require_once( __DIR__.'/Passport/UserExt.php');
       return new Passport\UserExt();
   }
    //  
   static function UserService()
   {
       require_once( __DIR__.'/Passport/UserService.php');
       return new Passport\UserService();
   }}