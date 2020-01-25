<?php
namespace Wisp\Factory\DbDefaultFactory;
/**
 * Created by PhpStorm.
 * User: tony
 * Date: 2017/8/7
 * Time: 17:04
 */


class ConnectionPool{

    var $dbName;
    var $slave;


   static  $conPool = []; //['key']=>[conn]

    function __construct($dbName,$dbIdx,$slave = false){
            $this->dbName = $dbName;
            $this->slave = $slave;
    }



    static function getDb($dbName,$dbIdx,$slave=false)
    {
         $cp = new ConnectionPool($dbName,$dbIdx,$slave);

        $cp->db();








    }

    function db(){

        $connKey = $this->makeConnKey();

        //拿到配置文件 ？ 配置文件的定义

        //首先定义数据配置的形式
        /**
         *    'dsn'      => 'mysql:host = 192.168.1.157;port = 3306;',
        'user'     => 'passport',
        'password' => 'passport',
        'database' => 'passport_0',
        'charset'  => 'utf8'
         *
         *
         * [db]  config config
         *
         * // group [0] [1] [2] [3]
         * /根据idx区/
         * [db][default]]=>[
         * 'dsn'      => 'mysql:host = 192.168.1.157;port = 3306;',
        'user'     => 'passport',
        'password' => 'passport',
        'database' => 'passport_0',
        'charset'  => 'utf8'];
         * //db group 0
         * [db][0]=[

         * 'master'=>['dns'=>'192.168.2.10', 'idx'=>0]
         * 'slave'=>[            //数组 形式 从 一主多从的形式
             0=>['dns'=>192.168.2.11],
             1=>['dns'=>192.168.2.12],
         * ]
         *
         *    * [db][group2]=>
         * 'common'=>['user'=>'','password'=>'','database==>'passport','idx'= 1],],
         * 'master'=>['dns'=>'192.168.2.10']
         * 'slave'=>[            //数组 形式 从 一主多从的形式
        0=>['dns'=>192.168.2.11],
        1=>['dns'=>192.168.2.12],
         * ]
         *
         *
        * [db][master]=[
 *
*
*
* ]
 *
         * [db][slav
         * e][0]=[
 *          0=>'192.158.1.1',
 *          1=>'192.168.1.2',
 *          2=>'192.168.1.1',
 * ]]
 **/



    }


    function makeConnKey()
    {
        //创建连接key
        if($this->slave == true)
        {
            $slaveTag = 'slave';
        }else{
            $slaveTag = '';
        }
        $connKey = $this->dbName.$this->dbIdx.slaveTag;
    }



    function setConfigFile()
    {


    }


    function getConf(){



    }


}