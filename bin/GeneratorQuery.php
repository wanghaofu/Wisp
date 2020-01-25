<?php
/**
 * Created by PhpStorm.
 * User: tony
 * Date: 2017/7/18
 * Time: 18:31
 * Description: 根据配置生成查询类
 *
 */


$wispPath = dirname( __DIR__ ); //
$vendorPath = dirname( dirname( $wispPath ) );


require_once $vendorPath . '/autoload.php';
define( 'TABLE_PREFIX', 't_|ko_' );//多个请用竖线分割
use Wisp\System\Sys;
use Wisp\System\Util;
use Wisp\Config as WispConfig;


echo $wispPath;
//设置生成路径
WispConfig::setGeneratorPath( $wispPath . '/src/' );


