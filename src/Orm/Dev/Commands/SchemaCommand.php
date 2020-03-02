<?php namespace Wisp\Orm\Dev\Commands;


/**
 * 构建数据库 这里是目前主要的地方
 */

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\Comparator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use King\Core\CoreFactory;
use Wisp\Orm\Dev\SchemaTool;

use Wisp\Factory\DbDefaultFactory\DbFactory;
use Wisp\Factory\DbDefaultFactory\DbSharding;
use Wisp\Factory\DbDefaultFactory\DbConfig;


class SchemaCommand extends Command
{
    const MODE = 'mode';
    const TARGET = 'target';

    const MODE_CREATE = 'create';
    const MODE_MIGRATE = 'migrate';
    const MODE_DROP = 'drop';
//TRUNCATE TABLE
//    const MODE_TRUNCATE = 'truncate';
    const MODE_CREATE_DB = 'create_db';
    const MODE_DROP_DB = 'drop_db';
    const MODE_MIGRATE_FROM = 'migrate_from';

    const OPTION_EXEC = 'exec';

    const OPTION_TABLE = 'table';

    const OPTION_DB = 'db';

    protected function configure()
    {
        parent::configure();
        $this
            ->setName( 'schema' )
            ->setDescription( 'generate/run schema sql ' )
            ->addArgument( self::MODE, InputArgument::OPTIONAL, 'drop|create|migrate|migrate_from|create_db|drop_db', self::MODE_CREATE )
            ->addArgument( self::TARGET, InputArgument::OPTIONAL, '<target>', '' )
            ->addOption( self::OPTION_EXEC, null, null, '直接执行' )
            ->addOption( self::OPTION_TABLE, null, InputOption::VALUE_REQUIRED, '表名' )
//            ->addOption( self::OPTION_DB, null, InputOption::VALUE_REQUIRED, '库名', 'passport' ); //添加db名称选项
//        $this ->addOption( self::OPTION_DB, null, InputOption::VALUE_REQUIRED, '库名',  $dbKeyName ); //添加db名称选项
           ->addOption(self::OPTION_DB,null,InputOption::VALUE_REQUIRED);
    }


    protected function execute( InputInterface $input, OutputInterface $output )
    {
//        $pdo = CoreFactory::instance()->pdo(); //这里返回的是连接对象！pdo连接对象
        /**
         *  core 能够取到连接配置
         *
         *   但是之前的连接是单的 而新的连接是跨库的 所以 后边的连接要wisp自行构建
         *
         *
         */
        $dbKeyName = $input->getOption( self::OPTION_DB );
        CoreFactory::instance();
//集群名称

        $dbGroupExtArr = DbSharding::getDbIdxArr( $dbKeyName );
        $groupNum = count( $dbGroupExtArr );
        if ( is_array( $dbGroupExtArr ) && $groupNum > 1 ) {  //大于一个的库要切分
            foreach ( $dbGroupExtArr as $groupId ) {
                $this->_execute( $input, $output, $dbKeyName, $groupId );
            }
        } else {
//            $db = DbFactory::ConnDb( 'passport');
//            $pdo = $db->connect();
            $this->_execute( $input, $output, $dbKeyName );
        }

    }


    protected
    function _execute( InputInterface $input, OutputInterface $output, $dbKeyName, $groupId = null )
    {


//        $pdo = CoreFactory::instance()->pdo(); //这里返回的是连接对象！pdo连接对象
        /**
         *  core 能够取到连接配置
         *
         *   但是之前的连接是单的 而新的连接是跨库的 所以 后边的连接要wisp自行构建
         *
         *
         */

        $schema = $this->getSchema( $input, $dbKeyName );

        $db = DbFactory::ConnDb( $dbKeyName, $groupId );

        if ( $input->getArgument( self::MODE ) == self::MODE_CREATE_DB ) {
            $pdo = $db->connectOnlyPdo();
        } else {
            $pdo = $db->connect();
        }

        $dbConfig = DbConfig::getDbConfigByNameAndIndex( $dbKeyName, $groupId );
        $dbName = $dbConfig[ 'database' ];

        if ( $groupId !== null ) {
            $dbName = $dbName . '_' . $groupId; //获取db名称
        }

//        $pdo->query("create database if not exists $dbName"); //数据库不存在则创建

        $connection = DriverManager::getConnection( [
            'pdo'    => $pdo,
            'dbname' => $dbName,  //获取db名称
        ] );

        echo "----
         dbName: {$dbName}\n\n";
        /**
         * @link http://doctrine-orm.readthedocs.org/en/latest/cookbook/mysql-enums.html
         */
        $connection->getDatabasePlatform()->registerDoctrineTypeMapping( 'enum', 'string' );

//
//        $schema = $connection->getSchemaManager()->createSchema();
//        var_dump($schema->toSql($connection->getDatabasePlatform()));
//        return;

        $platform = $connection->getDatabasePlatform();
        switch ( $input->getArgument( self::MODE ) ) {
            case self::MODE_CREATE_DB:
                $sqls = [ "create database if not exists $dbName" ];
                break;
            case self::MODE_DROP_DB;
                $sqls = [ "drop database if exists $dbName" ];
                break;
            case self::MODE_DROP:
//                $sqls = $this->getDropSql($schema, $platform);
                $sqls = $schema->toDropSql( $platform );
                break;
            case self::MODE_CREATE:
                $sqls = $schema->toSql( $platform );
                break;
            case self::MODE_MIGRATE:
                $comparator = new Comparator();
                $schemaDiff = $comparator->compare( $connection->getSchemaManager()->createSchema(), $schema );

                $sqls = $schemaDiff->toSaveSql( $platform );
                break;
            case self::MODE_MIGRATE_FROM:
                $comparator = new Comparator();
                $fromSchema = SchemaTool::createSchemaFromDir( $input->getArgument( self::TARGET ) );
                if ( empty( $fromSchema->getTables() ) ) {
                    throw new \Exception( 'empty target schema' );
                }
                $schemaDiff = $comparator->compare( $fromSchema, $schema );

                $sqls = $schemaDiff->toSaveSql( $platform );
                break;
            default:
                throw new \Exception( 'unimplemented' );
        }

        if ( $input->getOption( self::OPTION_EXEC ) ) {
//            $pdo = OrmDevFactory::instance()->pdo();

            foreach ( $sqls as $sql ) {
                echo "> running sql:", PHP_EOL, $sql, PHP_EOL;
                try {
                    $pdo->query( $sql );
                } catch ( PDOException $e ) {
                    de( $e );
                }
            }
            echo '> done', PHP_EOL;
        } else {
            echo implode( ";\n----\n", $sqls ), PHP_EOL;
        }

    }


    function checkDatabaseExist( $pdo, $dbName )
    {


    }

    protected function getSchema( InputInterface $input, $dbKeyName = '' )
    {


        $file = $input->getOption( self::OPTION_TABLE );
        $dir= dirname(dirname(dirname(dirname(__DIR__)))) .'/schema';
        if ( empty( $file ) ) {

//            $dir = __DIR__ . '../../../../schema';

            $dir = $dbKeyName ? $dir . '/' . $dbKeyName : $dir;

            $schema = SchemaTool::createSchemaFromDir( $dir );
        } else {

            if ( $dbKeyName ) {
                $format = "{$dir}/{$dbKeyName}%s.yml";
            } else {
                $format = "{$dir}/schema/%s.yml";
            }

            $file = sprintf( $format,  $file );
            if ( !is_file( $file ) ) {
                throw new \InvalidArgumentException( 'cannot find: ' . $file );
            }
            $schema = SchemaTool::createTableFromFile( $file );
        }
        return $schema;
    }
}
