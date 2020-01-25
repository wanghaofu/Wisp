<?php
require_once  __DIR__ . '/vendor/autoload.php'; // Autoload files using Composer autoload

use Wisp\DAO\Passport\User;

// method 1  orm method  这种针对日常简单的可以但是不够灵活
$user = new User();
$user->eq(User::F_USERNAME, "wangtao");

//在这里可已进行  效率比较高
//   dao继承的层次可以进行sql 切分的自动组装  数据库扩展方式 和 db扩展名称自动添加
//_dbidx
// _tableIdx

$res = $user->find();



//另外一种就是 key 键的方式 这种方式必需是在 db层面 查询的时候在启动替换  这个是必需的 这种灵活但是 用起来又些麻烦 拼写字段

$user->registerQuery("getall# select * from __TABLE_NAME__ ");
$res = $user->find('getall');



//用相同的名字不同的方式来做入口

$res = $user->query("select * from xx");




//问题：
//通过where 条件进行规整
//where1 :uuid ： uuid = :uuid
//where2 : uuidandnotdel :    uuid = : uuid and statud = 3

//
$user = paddport::user();
->setWehre('where1')
    ->find(['uuid'=>23]);

///*** auto cache func   query example1
$keyParams = serialize($params)
$keyWhere  =  "where2:$keyParams";

$this->cache->set($keyWhere , $user);


///**** update cache func
$user->setWhere('where2');
$user->username = "tony"；
$user->update(['uuid'=>23]);


// auto cache func query example2

$keyParams = serialize($params);
$keyWhere =" here2:$keyParams" ;
$this->cache->del($keyWhere);



//参数 和值共同控制的 where analy
//w1 : uuid  ＝3
//w2: uuid  > 1 and uuid < 10
//w3: uuid < 100

//uuid in ( 12, 23,323) and statud != 0;


update (uuid = 3 )


//then update cache w1 p =3
//w1 : w2 : w3


//up w2

//set status =1 ;

//up cache w1: w2


//1:*































*/