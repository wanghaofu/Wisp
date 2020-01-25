<?php
namespace Wisp\DAO;

/** Don't modify this file, this is auto generator by Wisp **/
class PassportMain
{
    var $__schemaName = 'passport_main';

    //  
   static function App()
   {
       require_once( __DIR__.'/PassportMain/AppFormat.php');
       return new PassportMainFormat\AppFormat();
   }
    //  
   static function AppKey()
   {
       require_once( __DIR__.'/PassportMain/AppKeyFormat.php');
       return new PassportMainFormat\AppKeyFormat();
   }}