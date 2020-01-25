<?php
namespace Wisp\DAO;

/** Don't modify this file, this is auto generator by Wisp **/
class ShipBasicIos
{
    var $__schemaName = 'ship_basic_ios';

    // 注册的用户表 
   static function ShipUser()
   {
       require_once( __DIR__.'/ShipBasicIos/ShipUser.php');
       return new ShipBasicIos\ShipUser();
   }}