<?php
/**
 * Created by PhpStorm.
 * User: tony
 * Date: 16/9/2
 * Time: 17:36
 */

use Wisp\WispConfig;


//生成schmea 的时候没有对 外部依赖的配置 ，所以生成方法这里配置这种形式不行

//怎么才能 生成文件只能类化 让项目继承在项目外进行配置生成
//生成工具不再内化
//用自动工具生成项目扩展的生成脚本，并自行配置就可以



//设置生成 schema 路径
$schemaFileName = dirname(__DIR__);
WispConfig::setGeneratorPath($schemaFileName);





//设置数据库配置文件路径
//example
$fileName ='/srv/www/config/db.inc.php';
WispConfig::setDbConfig($fileName);