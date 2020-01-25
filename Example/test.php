<?php

//echo __DIR__;
require_once dirname(__DIR__). '/vendor/autoload.php'; // Autoload files using Composer autoload
//
use Wisp\Db\DAO;
use Wisp\System\Util;

//// use Wisp\Sys\Noah;
////example 1 ###############################
//
//// use Wisp\System\Sys;
//
//
//// $q = Sys::Access('optimad');
//
//// $q->registerQuery("getfund#select * from t_user  limit 2");
//
//// $res = $q->find('getfund');
//// Util::de($res);
//
//$q = new DAO('king');
//$q->registerQuery("getArea#select * from ko_area");
//
//$res = $q->find('getArea');
//Util::de($res);
//
//
//
//// //example 2 ###############################
//// //通过自动脚本生成库模型后的调用方式
//// use Wisp\DAO\Optimad;
//
//use Wisp\DAO\King;
//
//$xx = King::Area();
//
//$res = $xx->get(4096);
//
//
//$xx->registerQuery("getArea#select * from __TABLE_NAME__");
//Util::de($res);
//$res = $xx->find('getArea');
//Util::de($res);




// $xx->findFirst($df_name, $stmt_name);


// $res = $xx->get()
// $acc = Optimad::Accelerate();
// // $accelerate->find('asdfas');
// $querys=array(
//     'getxx'=>array(
//         'sql'=>'',
//         'type'=>'true',
//         'cache'=>''
//     ),
//     'updateQxx'=>array(
//         'sql'=>'',
//     ),
    
// );

// $acc ->registerQuery("getAll#select * from __TABLE_NAME__ where ".$acc::F_ADPROJECTID.">=:".$acc::F_ADPROJECTID);

// $acc->adprojectId = 2;
// $result = $acc->find('getAll',array('adprojectId'=>2));
// Util::de($acc);

// $acc->registerQuery('getx##true');

// $acc->registerQuery('getlist#acprojectId=4#true');
// $result = $acc->find('getx');
// Util::de($result);

// Util::de($acc->querys);
// $acc->registerQuery('updateUserId#update __TABLE_NAME__ set cdnUrl="wangtao" where accelerateId=1 ');
// $acc->find('updateUserId');
// $db = $acc->getDb();






// //example 3 ###############################
// //直接初始化DAO 入口 进行操作
// use Wisp\Db\DAO;
// $dao = new DAO('optimad');
// $db = $dao->getDb();

// $db->fetch();
// $dao->init('king');
// $ac = Ac::accessGateWay("test#test");
// $ac->getType();

// $querys="
// // gettype #  select * from __TABLE_NAME__ where ss
// // updatexx # update __TABLE__NAME__ set xx=3



//     ";
// $db->getRows("select * from xx");
// Util::de($result);
// use Wisp\Db\Statement;
// use Wisp\Db\Ac;
// use Wisp\Db\Ac;
// echo SayHello::world();
//var_dump(Noah::db());
// Go to the terminal (or create a PHP web server inside "tests" dir) and type:

// Noah::db();
// Sys::db();
// $db = Sys::db();
// echo "<pre>";
// var_dump($db);
// de($db);
// $row = $db->getRows("select * from ko_fund ");
// var_dump($row);
// echo "</pre>";
// $q = Ac::Fund()->getFundByType(array('fund_type'=>0));
// de( $q );
//         $a = new Fund();
//         $a->getFundByType(array('fund_type'=>0));  //this have a 
//         $s = $a->find('getFundByType',array('fund_type'=>0));
//          $s=    $a -> getFundByType(2);
//         de($s);
//         $a = $a->find('')
//         $x = $a->get('000021');
//         de($x);
//         $a->getFundByType();
//         de($a);
// $r  = $db->getRows($q);
// de($r);
// Ac::item()->getItem(array(
//     'xid' => 12
// ));

// $a = Ac::item();
// $q =  new iSQL('optimad');
//静态常量里边返回的固定类实例可以进行初始化


