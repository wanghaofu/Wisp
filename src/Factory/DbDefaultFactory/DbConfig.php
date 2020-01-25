<?php
namespace Wisp\Factory\DbDefaultFactory;

/**
 * 基于参数获取扩展db 主db 以及从db的配置
 * 配置解析类 用于解析配置文件 DbfaultDbConfig
 * @author wangtao
 * 该类负责获取数据库连接配置
 */
class DbConfig
{
//<<<<<<< HEAD
//	/**
//	 * * 主数据库配置 **
//	 */
//	static $dbConfigs = [ ];
//	var $dbConfig = [ ];
//	var $key;
//	var $cacheKey;
//	var $split_value;
//	var $dbIdx;
//
//
//	// 绝对路径
//	static $configFilename = '';
//
//	static $ignoreFailSlaveIndex = [ ];
//
//
//	function __construct( $key, $split_value = 'null' )
//	{
//		$this->key = $key;
//		$this->split_value = $split_value;
//		$this->cacheKey = $this->getCacheKey();
//		self:: setDbConfigs();
//	}
//
//
//	/**
//	 * 配置 文件绝对路径  优先级比较高， 设定数据库配置文件加在的绝对路径
//	 *
//	 * @param unknown $file
//	 */
//	static public function setConfigFile( $filename = null )
//	{
//		if (true == isset($filename) &&  file_exists( $filename ) ) {
//			self::$configFilename = $filename;
//		}
//		self:: setDbConfigs();
//	}
//
//	/**
//	 * 加载系统数据库配置文件 ！
//	 *
//	 * @param unknown $dbConfig
//	 */
//	public static function setDbConfigs()
//	{
//		if ( empty( self::$dbConfigs ) ) {
//			//优先获取手动配置的配置文件
//			if ( self::$configFilename ) {
//				self::$dbConfigs = include_once( self::$configFilename );
//			} else {
//				self::$dbConfigs = include_once( WISP_CONFIG_DIR ); //默认加在的文件配置路径
//			}
//		}
//=======
    /**
     * * 主数据库配置 **
     */
    static $dbConfigs = [ ];
    var $dbConfig = [ ];
    var $key;
    var $cacheKey;
    var $split_value;
    var $dbIdx;


    // 绝对路径
    static $configFilename = '';

    static $ignoreFailSlaveIndex = [ ];


    function __construct( $key, $split_value = 'null' )
    {
        $this->key = $key;
        $this->split_value = $split_value;
        $this->cacheKey = $this->getCacheKey();
        self:: setDbConfigs();
    }


    /**
     * 配置 文件绝对路径  优先级比较高， 设定数据库配置文件加在的绝对路径
     *
     * @param unknown $file
     */
    static public function setConfigFile( $filename = null )
    {
        if (true == isset($filename) &&  file_exists( $filename ) ) {
            self::$configFilename = $filename;
        }
        self:: setDbConfigs();
    }

    /**
     * 加载系统数据库配置文件 ！
     *
     * @param unknown $dbConfig
     */
    public static function setDbConfigs()
    {
        if ( empty( self::$dbConfigs ) ) {
            //优先获取手动配置的配置文件
            if ( self::$configFilename ) {
                self::$dbConfigs = include_once( self::$configFilename );
            } else {
                self::$dbConfigs = include_once( WISP_CONFIG_DIR ); //默认加在的文件配置路径
            }
        }
//>>>>>>> feature/easyBuild
//		DbConfig::setDbConfigs(self::$dbConfig); //设定db配置

    }


    public static function getDbConfig( $key, $split_value = 'null' )
    {
        $config = new DbConfig ( $key, $split_value );
        $dbConfig = $config->_getDbConfig( $key, $split_value );

        return $dbConfig;
    }


//<<<<<<< HEAD
//	//获取特定库主键的配置信息  index is group
//	static function getDbConfigByNameAndIndex( $dbKeyName, $index = null, $slave = false, $ignoreSlaveIndex = null )
//	{
////        self:: setDbConfigs();
//		if ( is_null( $index ) ) { //不切分库的情况
//			return self::getDbConfigNoSplit( $dbKeyName, $slave );
//		}
//
//		$dbConfig = self::$dbConfigs[ $dbKeyName ];
//
//		$default = $dbConfig[ 'default' ];
//
//		//逻辑支持 如果选择从连接，并且存在从配置 则返回从库配置
//		if ( true === $slave ) {
//			if ( isset( $dbConfig[ $index ][ 'slave' ] ) && !empty( $dbConfig[ $index ][ 'slave' ] ) ) {
//				$dbConfigIndex = $dbConfig[ $index ][ 'slave' ];
//			}
//
//			//@TODO for slave fail  next redesign for
//			// 连接失败情况下排除该从库配置
//			if ( !is_null( $ignoreSlaveIndex ) && isset( $dbConfigIndex[ $ignoreSlaveIndex ] ) ) {
//				self::$ignoreFailSlaveIndex[] = $ignoreSlaveIndex;
//			}
//			//排除所有失败的连接配置
//			if ( empty( self::$ignoreFailSlaveIndex ) ) {
//				foreach ( self::$ignoreFailSlaveIndex as $ignoreIndex )
//					unset( $dbConfigIndex[ $ignoreIndex ] );
//			}
//
//
//			// 随机分会从库配置进行连接
//			$slaveConfigIndex = array_rand( $dbConfigIndex, 1 );
//			$dbConfigIndex = $dbConfigIndex[ $slaveConfigIndex ];
//			$dbConfigIndex[ 'slaveIndex' ] = $slaveConfigIndex;
//
//		}
//
//		if ( ( empty( $dbConfigIndex ) && $slave ) || false == $slave ) {
//			if ( isset( $dbConfig[ $index ][ 'master' ] ) && !empty( $dbConfig[ $index ][ 'master' ] ) ) {
//				$dbConfigIndex = $dbConfig[ $index ][ 'master' ];
//			}
//		}
//
//		//从返回的是数组
//		if ( $dbConfigIndex ) {
//			$dbConfig = array_merge( $default, $dbConfigIndex ); //用特殊配置覆盖默认配置 如果默认数据没有配置就用默认数据
//
//		}
//
//		return $dbConfig;
//	}
//
//
//	static function getDbConfigNoSplit( $dbKeyName, $user_slave = null )
//	{
//		if ( isset( self::$dbConfigs[ $dbKeyName ] ) ) {
//			$dbConfigs = self::$dbConfigs[ $dbKeyName ];
//		} else {
//			throw new \Exception( "dbConfig {$dbKeyName} is not config" );
//		}
//
//
//		if ( isset( $dbConfigs[ 'default' ] ) ) {
//			$default = $dbConfigs[ 'default' ];
//		} else {
//			$default = null;
//		}
//		if ( $user_slave && isset( $dbConfigs[ 'slave' ] ) ) {
//			$dbConfig = $dbConfigs[ 'slave' ];
//			// 随机分会从库配置进行连接
//			$slaveConfigIndex = array_rand( $dbConfig, 1 );
//			$dbConfig = $dbConfig[ $slaveConfigIndex ];
//		} elseif( true === isset( $dbConfigs[ 'master' ] ) ) {  //随机返回从库配置
//			$dbConfig = $dbConfigs[ 'master' ];
//		}
//
//		if ( $default && is_array( $default ) ) {
//			if ( false === empty( $dbConfig ) ) {
//				$dbConfig = array_merge( $default, $dbConfig );
//			} else {
//				$dbConfig = $default;
//			}
//		}
//
//		return $dbConfig;
//
//
//	}
//
//
//	//以下为私有方法
//	public function _getDbConfig( $key, $split_value = 'null' )
//	{
//		$dbConfig = [ ];
//		$this->dbConfig = self::$dbConfigs[ $key ];
//		$dbConfig = $this->dbConfig;
//		$dsn = $this->getDsn();
//		$database = $this->getDatabase();
//
//		$dbConfig [ 'dsn' ] = $dsn;
//		$dbConfig [ 'database' ] = $database;
//
//		return $dbConfig;
//	}
//
//	private function getDatabase()
//	{
//		$dbIdx = $this->getDbIdx();
//		if ( is_null( $dbIdx ) ) {
//			$database = $this->dbConfig [ 'database' ];
//		} else {
//			$database = $this->dbConfig [ 'database' ] . Split::LINK_TAG . $dbIdx;
//		}
//
//		return $database;
//	}
//
//
//	/**
//	 * @throws Exception
//	 */
//	private function getDsn()
//	{
//		$dsnInfo = $this->dbConfig [ 'dsn' ];
//		if ( is_array( $dsnInfo ) ) {
//			$Idx = $this->getDbIdx();
//			$dsnIdx = $this->getDsnIdx( $Idx );
//
//			return $dsnInfo [ $dsnIdx ];
//		} elseif ( $dsnInfo ) {
//			return $dsnInfo;
//		} else {
//			throw new \Exception ( 'dsn is not config!' );
//		}
//	}
//
//
//	/**
//	 * @param unknown $Idx
//	 *
//	 * @return mixed
//	 */
//	private function getDsnIdx( $Idx )
//	{
//		$dbIdxStr = $this->dbConfig [ 'dbIdx' ];
//		$dbIdxArr = explode( ',', $dbIdxStr );
//		foreach ( $dbIdxArr as $key => $value ) {
//			$dbIdxInfo = explode( ':', $value );
//			if ( $dbIdxInfo [ 0 ] == $Idx ) {
//				$DnsIdx = $dbIdxInfo [ 0 ];
//
//				return $DnsIdx;
//				break;
//			}
//		}
//	}
//
//	private function getCacheKey()
//	{
//		return $this->key . $this->split_value;
//	}
//=======
    //获取特定库主键的配置信息  index is group
    static function getDbConfigByNameAndIndex( $dbKeyName, $index = null, $slave = false, $ignoreSlaveIndex = null )
    {
//        self:: setDbConfigs();
        if ( is_null( $index ) ) { //不切分库的情况
            return self::getDbConfigNoSplit( $dbKeyName, $slave );
        }

        $dbConfig = self::$dbConfigs[ $dbKeyName ];

        $default = $dbConfig[ 'default' ];

        //逻辑支持 如果选择从连接，并且存在从配置 则返回从库配置
        if ( true === $slave ) {
            if ( isset( $dbConfig[ $index ][ 'slave' ] ) && !empty( $dbConfig[ $index ][ 'slave' ] ) ) {
                $dbConfigIndex = $dbConfig[ $index ][ 'slave' ];
            }

            //@TODO for slave fail  next redesign for
            // 连接失败情况下排除该从库配置
            if ( !is_null( $ignoreSlaveIndex ) && isset( $dbConfigIndex[ $ignoreSlaveIndex ] ) ) {
                self::$ignoreFailSlaveIndex[] = $ignoreSlaveIndex;
            }
            //排除所有失败的连接配置
            if ( empty( self::$ignoreFailSlaveIndex ) ) {
                foreach ( self::$ignoreFailSlaveIndex as $ignoreIndex )
                    unset( $dbConfigIndex[ $ignoreIndex ] );
            }


            // 随机分会从库配置进行连接
            $slaveConfigIndex = array_rand( $dbConfigIndex, 1 );
            $dbConfigIndex = $dbConfigIndex[ $slaveConfigIndex ];
            $dbConfigIndex[ 'slaveIndex' ] = $slaveConfigIndex;

        }

        if ( ( empty( $dbConfigIndex ) && $slave ) || false == $slave ) {
            if ( isset( $dbConfig[ $index ][ 'master' ] ) && !empty( $dbConfig[ $index ][ 'master' ] ) ) {
                $dbConfigIndex = $dbConfig[ $index ][ 'master' ];
            }
        }

        //从返回的是数组
        if ( $dbConfigIndex ) {
            $dbConfig = array_merge( $default, $dbConfigIndex ); //用特殊配置覆盖默认配置 如果默认数据没有配置就用默认数据

        }

        return $dbConfig;
    }


    static function getDbConfigNoSplit( $dbKeyName, $user_slave = null )
    {
        if ( isset( self::$dbConfigs[ $dbKeyName ] ) ) {
            $dbConfigs = self::$dbConfigs[ $dbKeyName ];
        } else {
            throw new \Exception( "dbConfig {$dbKeyName} is not config" );
        }


        if ( isset( $dbConfigs[ 'default' ] ) ) {
            $default = $dbConfigs[ 'default' ];
        } else {
            $default = null;
        }
        if ( $user_slave && isset( $dbConfigs[ 'slave' ] ) ) {
            $dbConfig = $dbConfigs[ 'slave' ];
            // 随机分会从库配置进行连接
            $slaveConfigIndex = array_rand( $dbConfig, 1 );
            $dbConfig = $dbConfig[ $slaveConfigIndex ];
        } elseif( true === isset( $dbConfigs[ 'master' ] ) ) {  //随机返回从库配置
            $dbConfig = $dbConfigs[ 'master' ];
        }

        if ( $default && is_array( $default ) ) {
            if ( false === empty( $dbConfig ) ) {
                $dbConfig = array_merge( $default, $dbConfig );
            } else {
                $dbConfig = $default;
            }
        }

        return $dbConfig;


    }


    //以下为私有方法
    public function _getDbConfig( $key, $split_value = 'null' )
    {
        $dbConfig = [ ];
        $this->dbConfig = self::$dbConfigs[ $key ];
        $dbConfig = $this->dbConfig;
        $dsn = $this->getDsn();
        $database = $this->getDatabase();

        $dbConfig [ 'dsn' ] = $dsn;
        $dbConfig [ 'database' ] = $database;

        return $dbConfig;
    }

    private function getDatabase()
    {
        $dbIdx = $this->getDbIdx();
        if ( is_null( $dbIdx ) ) {
            $database = $this->dbConfig [ 'database' ];
        } else {
            $database = $this->dbConfig [ 'database' ] . Split::LINK_TAG . $dbIdx;
        }

        return $database;
    }


    /**
     * @throws Exception
     */
    private function getDsn()
    {
        $dsnInfo = $this->dbConfig [ 'dsn' ];
        if ( is_array( $dsnInfo ) ) {
            $Idx = $this->getDbIdx();
            $dsnIdx = $this->getDsnIdx( $Idx );

            return $dsnInfo [ $dsnIdx ];
        } elseif ( $dsnInfo ) {
            return $dsnInfo;
        } else {
            throw new \Exception ( 'dsn is not config!' );
        }
    }


    /**
     * @param unknown $Idx
     *
     * @return mixed
     */
    private function getDsnIdx( $Idx )
    {
        $dbIdxStr = $this->dbConfig [ 'dbIdx' ];
        $dbIdxArr = explode( ',', $dbIdxStr );
        foreach ( $dbIdxArr as $key => $value ) {
            $dbIdxInfo = explode( ':', $value );
            if ( $dbIdxInfo [ 0 ] == $Idx ) {
                $DnsIdx = $dbIdxInfo [ 0 ];

                return $DnsIdx;
                break;
            }
        }
    }

    private function getCacheKey()
    {
        return $this->key . $this->split_value;
    }
//>>>>>>> feature/easyBuild
}


/**
 * Example
 * 警告 非分库应用dsn不要配置 成 数组 形式
 * @var 基本配置示例！ 分布式数据库配置文件
 * 支持数据库分区， 分布算法自定义， 默认支持分段 可赠一致性hash 分表等
 */
###@@@@ This only for example for other database
// $dbconfig = array ();

// ## 单库配置配置
// $dbConfig ['example'] = array (
// 		'dsn' => 'mysql:host=127.0.0.1;port=3306;',
// 		'user' => 'example',
// 		'password' => 'examplepro',
// 		'database' => 'example',
// 		'charset' => 'utf8',
// );

// ##用户库 活动扩展库 示例配置
// $dbConfig ['user'] = array (
// 		'dsn' => array (
// 				0=>"mysql:host=127.0.0.1;port=3306;", // 非分库应用不要配置 成 数组 形式
// 				1=>"mysql:host=127.0.0.2;port=3306;",

// 		), // 数据库连接字符串
// 		'dbIdx' => "0:0,1:0,2:0,3", // x:x 第一位表示数据库扩展索引 ，第二个表示上边配置的服务器索引 notice 非分库应用不要配置 成 数组 形式
// 		//**这里显示制定库所在的服务信息是为了灵活性，可以自由确定某个扩展所在的服务器，当然也可以根据规则自动制定，每个库所在服务器会是一种规律性分布
// 		'database' => 'example', // 数据库
// 		'user' => "example", // 登陆用户
// 		'password' => "examplepro", // 登陆密码
// 		'charset' => "utf8",

// );


//!! 初始化示例
//dbConfig::setDbConfigs($dbConfig);

