<?php
require_once dirname(__DIR__). '/vendor/autoload.php'; // Autoload files using Composer autoload
//
use Wisp\Db\DAO;
use Wisp\System\Util;
use Wisp\DAO\Passport\User;

de('hello');
de("this is second");


//
//$user = new User();
//
//de($user);



//$user->eq(User::F_PASSWORD,'asdf');


//de($user->find());


$user = User::query()->eq(User::F_USERNAME,'tonyu');

$user->username= 'tony';
$user->update();





?>