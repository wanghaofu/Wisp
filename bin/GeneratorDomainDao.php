#!/usr/bin/env php
<?php
/**
 * GeneratorDomainDao.php
 *
 * php ./GeneratorDomainDao.php
 * Description:生成数据模型
 *
 */
//namespace Wisp;


$wispPath = dirname( __DIR__ ); //
$vendorPath = dirname( dirname( $wispPath ) );


require_once $vendorPath . '/autoload.php';
define( 'TABLE_PREFIX', 't_|ko_' );//多个请用竖线分割
use Wisp\System\Sys;
use Wisp\System\Util;
use Wisp\Config as WispConfig;
use Wisp\Db\SQL\Sharding;

echo $wispPath;
//设置生成路径
WispConfig::setGeneratorPath( $wispPath . '/src/' );

//todo namespace will be generstor
// $db= Sys::dbExt('optimad');
define( 'TOOL_DEBUG', true );

// $db->find("")

class GeneratorDomainDao
{

    static function run($gp =null)
    {




        if($gp)
        {
            WispConfig::setGeneratorPath( $gp );
        }
        $dbName = 'oil_discount';
        $sh = new Sharding('','',$dbName,'');
        $dbIdxArr = $sh->getDbIdxArr();
        if(false === empty($dbIdxArr)){
            $rIdx = array_rand($dbIdxArr,1);
            $extDbName = $dbName.'_'.$rIdx;

        }else{
            $extDbName = $dbName;
        }

//=======
//        $dbName = 'hforum';
//>>>>>>> feature/easyBuild
//$dbName = 'ship_basic';
        $db = Sys::db( $dbName );
        $daoInfos = [ ];

// $sql = "show create table {$table}";
// $sql ="desc {$table}";
// $sql = "show columns from {$table}";
//
        $schemas = [ ];

// lower(schema_name) schema_name
//获取所有有的库
        $sqlSchema = "SELECT
    schema_name
    FROM
    information_schema.schemata
    WHERE
    schema_name NOT IN (
        'mysql',
        'information_schema',
        'test',
        'search',
        'tbsearch',
        'sbtest',
        'dev_ddl',
        'performance_schema'
    )";


        $databases = $db->getRows( $sqlSchema );

        $schema_cons = [ ];

        foreach ( $databases as $key => $value ) {
            $schema = new DatabaseInfo();
            $schemaName = current( $value );
            if ( $schemaName != $extDbName ) {
                continue;
            }
            $schema->schema_name = $dbName;
            $schemas[ $schemaName ] = $schema;

            $schemaClassName = generatorName( $schemaName );
            $schemaCon = [ ];
            $schemaCon[ 'head' ] = "<?php
namespace Wisp\DAO;

/** Don't modify this file, this is auto generator by Wisp **/
class {$schemaClassName}
{
    var \$__schemaName = '{$dbName}';
";

            ## 查看某一个库中的所有表  重要
            $sql = "SELECT
    table_name,
    create_time updated_at,
    table_type,
    ENGINE,
    table_rows num_rows,
    table_comment,
    ceil(data_length / 1024 / 1024) store_capacity
    FROM
    information_schema.TABLES
    WHERE
    table_schema = '{$extDbName}'
    AND table_name NOT LIKE 'tmp#_%' ESCAPE '#'";

            $tables = $db->getRows( $sql );
            //table
            $storeTableClassName = [ ];
            foreach ( $tables as $key => $table ) {
                $tabObj = new TableStruct();

                if ( !preg_match( '/[0-9a-zA-Z]+/s', $table[ 'table_name' ] ) ) {
                    continue;
                }

                $tabObj->tableName = $table[ 'table_name' ];
                $tabObj->comment = $table[ 'table_comment' ];
                //获取字段
                $sql = "select * from information_schema.COLUMNS where TABLE_SCHEMA='{$extDbName}' and TABLE_NAME='{$tabObj->tableName}'";

                $fields = $db->getRows( $sql );
                $fieldInfo = [ ];
                //构造文件
                $tableClassName = generatorTableName( $tabObj->tableName );

                if ( in_array( $tableClassName, $storeTableClassName ) ) {
                    continue;
                } else {
                    $storeTableClassName[] = $tableClassName;
                }
                $schemaCon[ 'method' ][] = "
    // {$tabObj->comment} 
   static function {$tableClassName}()
   {
       require_once( __DIR__.'/{$schemaClassName}/{$tableClassName}.php');
       return new {$schemaClassName}\\{$tableClassName}();
   }";

                $tableCon = [ ];

                //@TODO will change for namespace
                $tableCon[ 'header' ] = "<?php
namespace Wisp\DAO\\{$schemaClassName};
use Wisp\Db\DAO;
/**
* @description {$tabObj->comment}
**/
class {$tableClassName} extends DAO {
";
                //dbName
                $tableCon[ 'schemaName' ] = "     \$this->__schemaName = '{$dbName}';\n";
                //dbName
                $tableCon[ 'tableName' ] = "     \$this->__tableName = '" . generatorBaseTableName( $tabObj->tableName ) . "';\n";
                foreach ( $fields as $key => $field ) {
                    //ready
                    $fieldObj = new FieldInfo();
                    $fieldObj->fieldName = $field[ 'COLUMN_NAME' ];
                    $fieldObj->comment = $field[ 'COLUMN_COMMENT' ];
                    $fieldObj->type = $field[ 'COLUMN_TYPE' ];
                    $fieldObj->pri = $field[ 'COLUMN_KEY' ];
                    $fieldInfo[ 'fieldName' ] = $fieldObj;


                    //build con
                    $var = ( $fieldObj->comment ) ? "   var \${$fieldObj->fieldName}; // {$fieldObj->comment} \n" : "   var \${$fieldObj->fieldName};\n";
                    $tableCon[ 'var' ][] = $var;
                    $constName = generatorConstName( "f_{$fieldObj->fieldName}" );

                    $tableCon[ 'const' ][] = "   const {$constName} = '{$fieldObj->fieldName}'; // {$fieldObj->comment} \n";
                    if ( !empty( $fieldObj->pri ) && $fieldObj->pri === 'PRI' ) {
                        $tableCon[ 'pk' ] = "     \$this->__primaryKey = '{$fieldObj->fieldName}'; // {$fieldObj->comment}   \n";
                    }

                    $tableCon[ 'fields' ][] = "'{$fieldObj->fieldName}'";

                }
                $tabObj->fieldInfos = $fieldInfo;
                //生成schema 类内容
                $tableClassCon = generatorTableClass( $tableCon );

                de( $tableClassCon );
                $fileName = WispConfig::getGeneratorPath() . "/DAO/{$schemaClassName}/{$tableClassName}.php";

                writeFile( $fileName, $tableClassCon );
            }
//     Util::de($tables);
            $schemaClassCon = generatorSchemaClasss( $schemaCon );
            $fileName = WispConfig::getGeneratorPath() . "/DAO/$schemaClassName.php";
            de( $fileName );
            writeFile( $fileName, $schemaClassCon );
        }
    }
}


function generatorSchemaClasss( $schemaCon )
{
//    de($schemaCon[ 'method' ]);
    $schema = $schemaCon[ 'head' ];
    if(isset($schemaCon[ 'method' ])) {
        $methods = implode( "", $schemaCon[ 'method' ] );
        $schema .= $methods;
    }
    $schema .= "}";

    return $schema;

}

function getTableName( $tableName, $cutPrefix = null )
{
//     $tableName = substr($string,)
    $pattern = "/^(?:{$cutPrefix})([\w-_]*)/s";
    preg_match( $pattern, $tableName, $tm );
    if ( !empty( $tm[ 1 ] ) ) {
        $tableName = $tm[ 1 ];
    }
    $tableName = "{$tableName}";

    return $tableName;
}

function writeFile( $fileName, $content )
{
    $dir = dirname( $fileName );
    if ( !is_dir( $dir ) ) mkdir( $dir ); // 如果不存在则创建
    // 在检测b/目录中是否存在c.php文件
//     if (!file_exists($fileName)) file_put_contents('b/c.php', 'd'); // 如
    file_put_contents( $fileName, $content );
}

function generatorVarName( $filedName )
{

}

function generatorConstName( $fieldName )
{
//    $sa = split( '[_]', $fieldName )
    $sa = explode( ')', $fieldName );
    foreach ( $sa as $str ) {
        $s[] = strtoupper( $str );
    }

    return implode( '_', $s );
}

/**
 * 基本模型类名 对应表名
 *
 * @param unknown $schemaName
 *
 * @return string
 */
function generatorName( $schemaName )
{

    $sa = explode( '_', $schemaName );
    $lastStr = count( $sa );
    foreach ( $sa as $key => $str ) {
        //如果最后一个是数字则说明是扩展不做处理
        if ( $key + 1 == $lastStr && is_numeric( $str ) )
            break;

        $s[] = ucwords( $str );
    }

    return implode( '', $s );
}

/**
 * 生成基本表名 处理掉后边的数字
 *
 * @param unknown $schemaName
 *
 * @return string
 */
function generatorBaseTableName( $schemaName )
{

    $sa = explode( '_', $schemaName );
    $lastStr = count( $sa );
    foreach ( $sa as $key => $str ) {
        //如果最后一个是数字则说明是扩展不做处理
        if ( $key + 1 == $lastStr && is_numeric( $str ) )
            break;

        $s[] = $str;
    }

    return implode( '_', $s );
}

function generatorTableName( $tableName )
{
    $tableName = getTableName( $tableName, TABLE_PREFIX );

    return generatorName( $tableName );
}


function generatorTableClass( $tableConArr )
{

    $tableCon = $tableConArr[ 'header' ];

    $varInfo = implode( '', $tableConArr[ 'var' ] );
    $tableCon .= $varInfo;
    $consts = implode( '', $tableConArr[ 'const' ] );
    $tableCon .= $consts;

    $tableCon .= '   static $fields=[' . implode( ', ', $tableConArr[ 'fields' ] ) . "];\n";


    $tableCon .= "   function __construct(\$dbName=null){\n";
    $tableCon .= $tableConArr[ 'schemaName' ];
    $tableCon .= $tableConArr[ 'tableName' ];
    $tableCon .= "     parent::__construct(\$dbName);\n";

    if ( isset( $tableConArr[ 'pk' ] ) )
        $tableCon .= $tableConArr[ 'pk' ];
    $tableCon .= "}\n";

    $tableCon .= "}\n";

//     Util::de( $tableCon);
    return $tableCon;


}


//获取表字段信息

//     $sql ="select * from information_schema.columns where table_name='{$table}'"; //this is very import use this is very easy
//     Util::de($sql);


// foreach ($tables as $key => $table) {
//     $table = current($table);


//     $createInfo = $db->getRows($sql);


//     Util::de($createInfo);
//     $daoInfos[] = preapareDaoInfo(current($createInfo));
//     Util::de($daoInfos);

// //     Util::de($createInfo);
// }

function preapareDaoInfo( $createTableInfo )
{
    $tableStructObj = new tableStruct();
    $tableName = $createTableInfo[ 'Table' ];
    $tableStructObj->tableName = $tableName;
    $fieldInfo = [ ];
    $createTableStr = $createTableInfo[ 'Create Table' ];

//     Util::de($createTableStr);
    // preg_match_all('/(`([^`]*)\s+.*\s+COMMENT\s*\'([^\n]*)\',/s',$createTableStr ,$match );
    $ctsArr = preg_split( '/\n/', $createTableStr );
//     Util::de($ctsArr);
    $count = 0;
    $count = count( $ctsArr );
    foreach ( $ctsArr as $ctLineStr ) {
        $fobj = new tableFieldInfo();
        $ctLineStr = trim( $ctLineStr );

        $count++;
        // 先匹配字段匹配字段
//         $pattern = "/`([^`]+)`\s.*(\s+COMMENT\s+\'(.*)\'),/s";
        $pattern = "/`([^`]+)`\s[\w'\(\)\s-:]+(\s+COMMENT\s+\'(.*)\')?,/s"; //import ！
        preg_match_all( $pattern, $ctLineStr, $fmat );

        Util::de( $ctLineStr );
        //this is for test
        if ( $tableName == 't_adversion' ) {
//             Util::de($ctLineStr);
//             Util::de($fmat);
        }

        if ( !empty( $fmat[ 1 ] ) ) {
            $fobj->fieldName = current( $fmat[ 1 ] );
            $fobj->comment = current( $fmat[ '3' ] );
            $fobj->tableName = $tableName;
            // Util::de( $fobj );
            $fArr[ $fobj->fieldName ] = $fobj;
            continue;
        }
        //尝试匹配主键信息
        $patternPk = '/PRIMARY\s+KEY\s*\(`([^`]+)`\)/s';
        preg_match( $patternPk, $ctLineStr, $fpk );
        // $tableInfo
        if ( !empty( $fpk[ 1 ] ) ) {
//             Util::de($fpk);
            $tableStructObj->primaryKey = $fpk[ 1 ];
            continue;
        }

        //尝试匹配表注释
        $patternTcomment = '/ENGINE.*COMMENT=\'(.*)\'/s';
        preg_match( $patternTcomment, $ctLineStr, $tCom );
        if ( !empty( $tCom[ 1 ] ) ) {
            $tableStructObj->comment = $tCom[ 1 ];
        }
//         Util::de($tCom);
        //尝试匹配表注释
//         Util::de($fpk);
    }
//     Util::de($count);
    $tableStructObj->fieldInfos = $fArr;
    Util::de( $tableStructObj );

    return $tableStructObj;

    // preg_match_all('/[^\n]*\n/s',$createTableStr ,$match );
    // Util::de($match);

    // 获取主键
}

//Todo
class DatabaseInfo
{
    var $schema_name;
}

class TableStruct
{
    var $tableName = null;
    var $comment = null;
    var $primaryKey = null;
    var $fieldInfos = null;
}

class FieldInfo
{
    var $fieldName = null;
    var $comment = null;
    var $tableName = null;
    VAR $type = null;
    var $pri = null;
}

GeneratorDomainDao::run();
