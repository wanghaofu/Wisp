<?php
/**
 * This is a class group for auto generator the remote method 
 * remote process call
* @author tony
*
**/
class sys{
    static function date(){
        return  date('Y-m-d H:i:s');
    }
}
class RPC_CONFIG
{
    // 	const API_DOMAIN='http://172.16.29.151:80/middleware/';
    static $RPC_CONFIG_FILE='api.ini';
    static  $map=array(
        'int'=>'check::int',
        'char'=>'check::char',
        'len' =>'check::checkLen',
        'timestamp' =>'calbak::timestamp'
    );
    public static function getApiDmain()
    {
        if(defined('_MIDDLE_WARE_API_URL'))
            return _MIDDLE_WARE_API_URL;
            else
                return self::API_DOMAIN;
    }
    public static function getRpcConfigFile()
    {
        return self::$RPC_CONFIG_FILE;
    }
}
class calbak
{
    public static function timestamp($value)
    {
        if(empty($value))
        {
            return date('Y-m-d H:i:s');
        }else{
            return $value;
        }
    }
}
class check
{
    public static function int($var)
    {
        if( is_numeric($var) )
            return true;
            else{
                return false;
            }
    }
    public static function char($var)
    {
        if( is_string($var))
            return true;
            else{
                return false;
            }
    }
    public static function checkLen($var,$len)
    {
        $strLen = strlen($var);
        if($strLen <= $len)
        {
            return true;
        }else {
            return false;
        }
    }
    public static function isNull($value)
    {
        if( $value === 0 || $value==='0' || $value===false || is_array($value) )
        {
            return false; //这些值是非空所以返回false
        }
        if( empty($value) || is_NULL($value))
        {
            return true;
        }else
        {
            return false;
        }
    }
    public static function isString($value)
    {
         
    }
}

/*
 * 存储 key为某个字段的值 值为一个list的 数组
 */
class keyMap
{
    private $keyField;
    private $valueField;
}
/*
 * 键为某个字段的值  值为另外一个字段值 的数组
 * */
class keyValueMap extends keyMap{}
/**存key值为0开始的， 值为某一个字段的数组  即自然下表数组**/
class valueList extends keyMap{}

class AR
{
    private static  $areaSearchStatus = false;
    private static $valueSearchStatus = false;
    private static $tempData;
    public static function map($array,$keyField,$selectFields=null,$posTagField=null)
    {
        return self::reBuildArray($array,$keyField,$selectFields,$posTagField);
    }

    public static function keyValue($array,$keyField,$valueField,$posTagField=null)
    {
        return self::keyMap($array,$keyField,$valueField,$posTagField);
    }
    public static function idxValue($array,$valueField,$posTagField)
    {
        return	self::keyMap($array,'',$valueField);
    }

    private static function getFirstStr($str)
    {
        if(strpos($str,',')!==false)
            $strArr = explode( ',', $str );
            else
                $strArr=array($str);
                foreach($strArr as $key=>$value){
                    if($value==='*') continue; //排除*
                    	
                    if(strpos( $value, '.' )!==false)
                        $str= strstr($value, '.', true);
                        else{
                            $str = $value;
                        }
                        	
                        if(strpos($str,'#')!==false)
                        {
                            $str=strstr($value, '#', true);
                        }
                        break;
                }

                if(empty($str))throw new Exception("The search must has a position tag!");

                return $str;
    }
    /**
     *
     * @param unknown $array
     * @param unknown $keyField  为空表示构建自然indexs述祖
     * @param string $valueField  为空表示返回原始全部数据
     * keyField 'id | meterialSpec.id' //meterialSpec下班的id
     *
     * ValueField
     *
     *  'id, meterialSpec.id'
     *  'meterialSpec.id,meterialSpec.name#newName   #后边边是新的名称 meterialSpec.name#!newName ＃转换新名称并且数组深度上移一位
     *  notice:selectField or keyField musthave one true
     */
    /**
     *
     * @param unknown $searchData  搜索的数组
     *  * @param string $keyField      返回key用的键  key位＃ 表示只取第一条纪录 空表示自然增长
     * @param string $selectFields  value部分 选择的key 只返回二维数组中存在的key值  ＊表示全部 ＃ 表示转名 ＃！表示排序使用这个排序
     * @param string $posTagField  搜索范围定位字段，尽可能是包含所有key信息的最顶级， 可以不添
     * @throws Exception
     * @return NULL|multitype:unknown
     */
    private static function reBuildArray($searchData,$keyField=null,$selectFields=null,$posTagField=null)
    {
        if(empty($searchData)) return array();
        $newArray = array (); //构成数据的保存变量

        //兼容行转换！ select直接被忽略

        if(strpos($keyField,','))
        {
            $tarr = explode(',',$keyField);
            $keyField = $tarr[0];
            $selectFields = $tarr[1];
            	
        }

        if($keyField==='*')$keyField='';

        if( is_string( $posTagField ) )
        {
            	
            $searchAreaKeyField = self::getFirstStr( $posTagField );
            	
        }elseif( empty( $keyField ) || $keyField==='#' ) //当key为空的话
        {
            	
            $searchAreaKeyField = self::getFirstStr($selectFields); //走选择字段的第一个
             
        }else{
            	
            $searchAreaKeyField = self::getFirstStr($keyField); //默认走key
            	
        }

        if( $searchAreaKeyField )
            $searchArea =self::getSearchArea( $searchAreaKeyField, $searchData );

            if(empty( $searchArea )) return array();
            if(!is_array( $searchArea )) return array();
            $num = strlen( $selectFields );

            //处理select 字段信息 这里可以同处理先处理 用svfa保存
            if($num>0)
            {
                if(strpos($selectFields,',')!==false)
                {
                    $svfa = explode(',',$selectFields); //且分多个field
                } else{
                    $svfa=array($selectFields);
                    $idxArray= true;
                }
                //全部加部分处理的情况
                $allIdx = array_search( "*" , $svfa );
                if( $allIdx !== false )
                {
                    unset($svfa[$allIdx]);
                    $allTag = true;  //设定合并模式标签为真
                }
            }else{
                $svfa=false;
            }
            //start build new array struct
            foreach( $searchArea as $key0 => $value0 )
            {
                ####//新的主键值
                if($keyField && $keyField!=='#') //单列index情况考虑  没有传keyfield的情况下考虑
                {
                    $keyValue = self::findValue( $value0, $keyField );
                    if(is_array( $keyValue ))
                        throw new Exception(" Key [ $keyField ]'s value  can't is Array!");
                        elseif(is_null($keyValue))
                        continue;
                }elseif(!is_array($value0) && !in_array($key0,$svfa))
                {
                    continue;
                }else
                {
                    $keyValue = null;
                }
                	
                if(isset($allTag)  && $allTag ===true )
                {
                    $newRow = $value0;
                }
                	
                //应该为空
                if( !empty( $svfa ) )
                {
                    foreach($svfa as $key => $selectField )
                    {
                        if(empty($selectField)) continue;
                        if(strpos($selectField,'#')!==false)
                        {
                            list( $selectField, $selectKeyName ) = explode( '#', $selectField );
                        }else{
                            $selectKeyName = trim( $selectField );
                        }

                        if(strpos( $selectKeyName, '.' )!==false)
                        {
                            $selectKeyName = substr(strrchr( $selectKeyName, '.' ),1);
                        }


                        if( $key0===$selectField)
                            $resValue=$value0;
                            elseif(is_array($value0))
                            $resValue= self::findValue( $value0, $selectField );
                            else
                                $resValue = null;

                                if(strpos($selectKeyName,'!')!==false)  //如果是＃！这个格式说明是要对结果进行重新排序！而非重新命名
                                {
                                    $searchField = substr($selectKeyName,1);
                                    $resValue = self::reBuildArray($resValue,$searchField);  //！！
                                     
                                    $selectKeyName=$selectField;  //key更名
                                }
                                if(!isset( $idxArray ) )
                                {
                                    $newRow[$selectKeyName] = $resValue;
                                }else{
                                    $newRow = $resValue;
                                }
                    }
                     
                }else{
                    $newRow = $value0; //什么都不做全部分会自然索引值
                }
                 
                if(is_null($newRow)) continue;
                 
                if($keyField==="#")  //keyField one row 必须指定
                {
                    $newArray= $newRow;
                    break;
                }elseif(!empty($keyField) && !is_null($newRow))
                $newArray[$keyValue] = $newRow;
                else{             //一般情况下都是数组结构
                    $newArray[] = $newRow;  //自然索引数组
                }
            }
            return $newArray;
    }

    private static function getSearchArea($searchKey=null,$searchData, $searchInfo=null,$deep=null )
    {
        if(!is_array($searchData)){
            return null;
        }
        if( is_null( $deep ) )
        {
            $deep= 0;
            $searchInfo[$deep]['deep']= $deep;  //
            $searchInfo[$deep]['data']= $searchData;
        }
        $tmpArr = array();
        foreach($searchData as $key=>$value) //已经进入了一层
        {
            if(!is_null( $searchKey ) && $key === $searchKey )
            {
                	
                $deep++;
                $searchInfo[$deep]['deep'] = $deep ;
                $searchInfo[$deep]['data'] = $value ; //存在数据的key位置！
                if($deep>=2)
                {
                    return $searchInfo[$deep-2]['data'];  //返回当前层次级向上2个深度的数据
                }elseif($deep<=1){ //只有一层的数组
                    return $searchInfo[$deep-1]['data'];
                }
                break;
                	
            }elseif(is_array($value) && !is_null($searchKey))
            {
                $tmpArr[$key] =$value; //数组先放一边
            }
        }
        if(!empty( $tmpArr )){
            foreach( $tmpArr as $key => $value )
            {
                $deep++;
                $searchInfo[$deep]['deep'] = $deep;
                $searchInfo[$deep]['data'] = $value; //存在数据的key位置！
                //一般走这里
                $result = self::getSearchArea($searchKey,$value,$searchInfo,$deep); //再次进入 就是2层需要
                if(!is_null( $result )) //下一层又找到key
                {
                    return $result;
                    break;
                }
            }
        }
        	
        //没有找到就返回空
        return null;

    }

    //分解搜寻信息进行 并调用fineValue获取数据
    public static function findValue($searchArr,$keyField='')
    {
        if(strpos($keyField,'.')!==false){
            $searchKeyArray = explode('.',$keyField);
            foreach($searchKeyArray as  $key=>$searchField)
            {
                //find key value 深度搜索
                $searchArr = self::_findValue($searchArr,trim($searchField));
            }
            $keyValue = $searchArr;
        }elseif(is_array($searchArr)){
            $keyValue = self::_findValue($searchArr,trim($keyField));
        }else{
            $keyValue = null;
        }
        return $keyValue;
    }


    /**
     *
     * @param unknown $values
     * @param unknown $key
     * @return unknown
     */
    public static function _findValue($values,$searchKey)
    {
        self::$valueSearchStatus = false;
        $searchKey = trim($searchKey);
        if( !is_array( $values ) )return $values;
        if($searchKey==='' ) return $values;
        //一维关联数组如果存在直接返回
        //上边没有存在则深入循环搜索 说明可能在更深层里边
        foreach( $values as $keyo=>$valueo)
        {
            if($keyo === $searchKey ) //停止条件
            {
                $res = $valueo;
                break;
            }elseif(is_array($valueo)){
                $res = self::_findValue($valueo,$searchKey); //开始递归查找
            }
            if(isset($res)) //深度搜索返回 则跳出
            {
                $value=$res;
                break;
            }
        }
        if(!isset($res))
            $value=null;
            else{
                $value= $res;
            }
            return $value;
    }
    public static function ota( $obj )
    {
        if( is_object( $obj ) ) {
            $obj = (array)$obj;
            $obj = self::ota( $obj );
        } elseif( is_array( $obj ) ) {
            foreach( $obj as $key => $value ) {
                $obj[$key] = self::ota($value);
            }
        }
        return $obj;

    }

    //数组转换为对象
    public static function ato( $data )
    {
        if(empty($data)) return null;
        if(is_array ( $data ) )
        {
            foreach ( $data as $key => $val ) {
                if(is_numeric($key) && !isset($ref))
                {
                    $ref= array();
                }elseif(!isset($ref)){
                    $ref = new stdClass();
                }
                if(is_array($ref))
                    $ref[$key] = self::ato( $val  );
                    else
                        $ref->$key = self::ato( $val  );
            }
        }else{
            $ref = $data;
        }
        return $ref;
    }



}

class apiInfo
{
    public $methodInfo; //array('methodName'=>array('paranNameKey',ParamObj);

}
// return Info:
class methodParam
{
    public $paramName;
    public $tag;
    public $rpcPos;
    public $defaultValue;
}

class resultInfo
{
    public $selectField;
    public $keyField;
    public $posTagField;
    public $dataFormat;
}
/**
 *
 *
 * @author tony
 *		docInfo=array(
 *		'apiName1'=>array(
 *					'method'=>array(
 *									paramInfo=array(
 *									'paramName1'=>paramObj1,
 *									'paramName2'=>paramObj2,
 *								)
 *					 'result'=array(
 *									            valueField->valueField=>'fieldName1,fieldName2'
 *												keyField->'fieldName'
 *												posTagField="fieldName"
 *
 *		                )
 *
 *
 */

/** help for build model**/
class FO
{
    public $funDoc;   //方法参数
    public $funName;  //方法名
    public $funParam; //方法参数
    public $funBody;  //方法体
}
/**model template is not user then remove this  only use once then not need**/
class MT{
    const F_PRE="class {className} extend Ac\n";
    const F_PRE_TAG= '{className}';

    const FUN_DOC ="/**\n
* @path {k}/{v} \n
				";
    CONST FUN_HEADER='public function {funName}';
    CONST FUN_HEADER_TAG='{funName}';
    CONST FUN_PARAMS="({paramsList}) \n";
    CONST FUN_BODY="   {funLine}\n";
    CONST FUN_MEM_VAR = "  \$this->set('{memName}', \${valVarName});\n";
    CONST FUN_MEM_NAME='{memName}';
    CONST FUN_VAL_VAR_NAME='{valVarName}';

    static function gen_pre($className)
    {
        return $result = str_replace(self::F_PRE_TAG,$className,self::F_PRE);
    }
    /**ARRAY LIST K=>V**/
    static function gen_fun_doc($arr)
    {

    }
    static function gen_fun_header($funName)
    {
        return $res = str_replace(self::FUN_HEADER_TAG,$funName,self::FUN_HEADER);
    }
    static function gen_fun_mem_var($memName,$valVarName)
    {
        $res = str_replace(self::FUN_MEM_NAME,$memName,self::FUN_MEM_VAR);
        return $res =  str_replace(self::FUN_VAL_VAR_NAME,$valVarName,$res);
    }
}

class BuildModel{
    private $con='';

    private $class='';
    private $function='';

    private $memVar=array();

    CONST  FIELD_CONF_NAME = 'fieldConf';
    CONST  CLASS_MEMBER_SPACE = "###CLASS_MEMBER###";
    static public function exec($docInfo)
    {
        $bc = new BuildModel();
        $bc->buiCls( $docInfo);
    }
    public  function buiCls($docInfo)
    {
        $ignoreInfo = array(docInfoX::API_COMM_FIELD_INFO,docInfoX::API_KEY_NAME);
        foreach($docInfo as $className =>$methods)
        {
            $this->memVar = array();
            $this->con='';
            $this->con="<?php\n";
            $this->con.="class {$className}Dao extends Ac \n{\n";
            $this->con.=self::CLASS_MEMBER_SPACE;
            	
            //make  function
            foreach($methods as $methodName => $methodConf)
            {
                if(in_array( $methodName , $ignoreInfo )) continue;

                $this->buildParamsBody( $className, $methodName, $methodConf );
                 
                $this->buildFunBody( $methodConf );
                $this->con.="}\n";
            }
            	
            if(!empty($this->memVar))
            {
                $mem = '';
                sort($this->memVar);

                foreach($this->memVar as $value)
                {
                    $mem.= "  public \${$value};\n";
                }
                $this->con = str_replace(self::CLASS_MEMBER_SPACE, $mem, $this->con);
            }else {
                $this->con = str_replace(self::CLASS_MEMBER_SPACE, '', $this->con);
            }
            $this->con.="\n}\n";
            // 			de(htmlentities($this->con));
            $this->createClassFile($className, $this->con );
        }
    }

    private function createClassFile($className, $context)
    {
        $path = "../dao/";
        $fileName ="../dao/{$className}Dao.php";
        if (is_writable( $path ))
        {
            $fp = fopen( $fileName, 'w' );
            $stat = fwrite($fp, $context);
            if($stat)
            {
                echo "$fileName write ok <br/>";
            }
            fclose( $fp );
        }else{
            throw new Exception ("dao path can't write file !");
        }

        $modelPath = "../models/";
        $modelFileName ="../models/$className.php";
        if(is_writable( $modelPath ) )
        {
            if(!file_exists($modelFileName))
            {
                $model = "<?php\n";
                $model.="class {$className} extends {$className}Dao\n";
                $model.="{\n";
                $model.="}\n";

                $fp = fopen( $modelFileName, 'w' );
                $stat= fwrite($fp,$model);
                if($stat)
                {
                    echo " $modelFileName  write ok<br/>";
                }
                fclose( $fp );


            }
        }

    }
    /** generate the fundoc info**/
    /**
     *
     * @param unknown $className
     * @param unknown $methodConf
     */
    private function buildDoc($className,$path,$pathParamList=null, $methodParamInfo=null)
    {
        if(isset( $methodConf[docInfoX::REMOTE_METHOD_NAME] ))
        {
            $path =$methodConf[docInfoX::REMOTE_METHOD_NAME];
        }
        $apiName = strtolower($className);
        $this->con.= "\n/**\n";
        $this->con.= "* Config For Data Access Protocol of Restful Server \n";

        if(isset($path))
            $path= "/{$apiName}/{$path}";
            else{
                $path.= "/{$apiName}";
            }
            $path = preg_replace('/\/{2,}/', '/', trim($path));
            $this->con.="* @path {$path}\n";

            if(isset( $methodParamInfo ) && is_array( $methodParamInfo))
            {
                $parStr = "* @".self::FIELD_CONF_NAME." ";
                $maxNum =count($methodParamInfo);
                $i=0;
                foreach( $methodParamInfo as $fieldName => $mpo )
                {
                    $i++;
                    if(isset($mpo->tag) && $mpo->tag==1)
                        $parStr.= "{$fieldName}.*";
                        else{
                            $parStr.= "{$fieldName}";
                        }

                        if($maxNum != $i)$parStr.=" ";
                        if(!in_array($fieldName,$this->memVar))$this->memVar[] = $fieldName;
                }
                $this->con.= $parStr."\n" ;
            }
            if($pathParamList  || $methodParamInfo)
            {
                $this->con.= "* Method param \n";
            }

            if(!is_null($pathParamList) && is_array($pathParamList))
            {
                	
                foreach($pathParamList as $key=>$value)
                {
                    $this->con.= "* @param \${$value}\n";
                    if(!in_array($value,$this->memVar))$this->memVar[] = $value;
                }

            }

            if(!is_null($methodParamInfo))
            {
                $this->con.= "* @param \$dataArr\n";
            }

            $this->con.= "*/\n";      //end the doc


    }
    /**
     *
     * @param unknown $className
     * @param unknown $methodName
     * @param unknown $methodConf
     */
    private function buildParamsBody($className, $methodName, $methodConf )
    {
        if(isset($methodConf[docInfoX::REMOTE_METHOD_NAME]))
        {
            $pathTemplate =$methodConf[docInfoX::REMOTE_METHOD_NAME];
        }else{
            $pathTemplate = null;
        }
        $methodParamInfo = null;
        $paramsList = null;

        $parCon = null;
        $medBody="\n{\n";

        if(isset( $methodConf[docInfoX::PARAM_KEY] )) $methodParamInfo = $methodConf[docInfoX::PARAM_KEY];

        if(!empty($pathTemplate))
        {
            $pattern = '/\{(.*?)\}/';
            // 			$replacement = '{\$$1}';
            preg_match_all($pattern, $pathTemplate, $matches);
            // 			$path = preg_replace($pattern, $replacement, $pathTemplate);
            if(isset($matches[1])) $paramsList = $matches[1];
        }
        	
        if(!empty($paramsList))
        {
            $parCon= "(";
            foreach($paramsList as $key=>$paramsName )
            {
                if(empty( $paramsName ))continue;
                if($key===0){
                    $parCon.=" \${$paramsName} ";
                }else{
                    $parCon.=", \${$paramsName} ";
                }
                	
                // 					$medBody.=" \$this->{$paramsName} = \${$paramsName};\n";
                $medBody.= MT::gen_fun_mem_var($paramsName, $paramsName);
            }

            if(isset($methodParamInfo) && $paramsList)
                $parCon.= ', $dataArr = array() ';
                elseif(isset( $methodParamInfo ))
                $parCon.= ' $dataArr = array() ';

                $parCon .= ")";
        }else{
            if(!empty($methodParamInfo) )
                $parCon ='( $dataArr = array() )';
                else
                    $parCon ='()';

        }
        if(isset($methodParamInfo))
        {
            $pnum = count($methodParamInfo);
        }else{
            $pnum = 0;
        }
        if($pnum==1)
        {
            $tk = current(array_keys($methodParamInfo));
            $medBody.="  \$this->set( '{$tk}' , \$dataArr ); \n";
        }elseif( $pnum >1 )
        {
            $medBody.="  \$this->set( \$dataArr ); \n";
        }
        // mix
        $path = $pathTemplate;
        //call build the header
        $this->buildDoc($className, $path , $paramsList, $methodParamInfo);
        	
        //header
        $this->con.= MT::gen_fun_header( $methodName ); ;
        	
        $this->con.= $parCon;
        $this->con.= $medBody;



    }
    private function buildFunBody( $methodConf )
    {
        if(isset($methodConf[docInfoX::PARAM_KEY]))  $methodParamInfo = $methodConf[docInfoX::PARAM_KEY];

        $this->con.="  \$medName = __FUNCTION__;\n";
        $this->con.="  return parent::\$medName();\n";

    }

}
//model access data store and server call check point
class docInfoHelp
{
    private $className;
    private $methodName;

    private $path;
    private $paramsInfo;

    private static $cacheApiInfo;  //cache api params Info

    public function __construct($className, $funName){
        $this->className = $className;
        $this->methodName = $funName;

        if(class_exists($className))
        {
            $method = new ReflectionMethod($className,$funName);
            $doc = $method->getDocComment();
            $this->generatePathInfo($doc);
            $this->generateMethodParamInfo($doc);
        }
    }

    private function generatePathInfo($doc)
    {
        $pattern = '/@path\s*([^\n]+)/';
        preg_match($pattern, $doc, $matches );
        if(isset($matches[1]))
        {
            $path = $matches[1];
            $path = preg_replace('/^\/*[^\/]+/', '', trim($path));
            $this->path = $path;
            self::$cacheApiInfo[$this->className][$this->methodName]['path'] = $path; //后期建立缓存机制
        }

    }
    private function generateMethodParamInfo($doc)
    {
        $pattern = "/@".BuildModel::FIELD_CONF_NAME."\s*([^\n]+)/";
        preg_match($pattern, $doc, $matches );
        if(isset($matches[1]))
        {
            $paramsInfo = $matches[1];
            $paramsArr = preg_split('/([\s+]|,)/',$paramsInfo) ;
            foreach($paramsArr as $fieldName)
            {
                if( empty( $fieldName ) ) continue;

                $fo = new methodParam();

                if(strpos($fieldName,'.')===false)
                {
                    $fo->paramName = $fieldName;
                    $fo->tag= 0;
                    $paramsArrObj[$fieldName] = $fo;
                }else
                {
                    $fieldNameArr= explode('.',$fieldName);
                    foreach($fieldNameArr as $k=>$v)
                    {
                        switch($k)
                        {
                            case '0':
                                $fo->paramName = $v;
                                $fName = $v;
                                break;
                            case '1':
                                if($v === '*') $fo->tag = '1';
                                break;
                        }
                    }
                    $paramsArrObj[$fName] = $fo;
                }
            }
            $this->paramsInfo =$paramsArrObj;
        }

    }

    public function getParamsInfo($key)
    {
        return $this->paramsInfo[$key];
    }

    public function getPathInfo()
    {
        return $this->path;

    }
    public function writeCache( )
    {

    }


    /**
     * * 解析格式
     * @path /advertiser/add
     * @fieldProtoType fullName.* url.* name.* licenseNo.* comments contactName.* contactPosition.* mobile.* email.*
     * @param string $keyName
     */
    public function getMethodParamInfo($keyName='')
    {
        	
    }

}

class docInfoX{
    private $docInfo;
    public static $docInfoCache;
    const RPC_METHOD_VAR_NAME ='METHOD';
    const RPC_METHOD_PARAMETER_NAME = 'METHOD_PARAM';
    const QUERY_FIELD_NAME = 'QUERY';
    const POST_FIELD_NAME = 'POST';
    const API_KEY_NAME = 'KEY';

    const API_COMM_FIELD_INFO='__COMM_FIELD';

    const PARAM_KEY='_P';
    const RESULT_KEY_NAME='_RE'; //结果处理的参数
    const REMOTE_METHOD_NAME='_RM';  //远程接口明 键名

    const RPC_POS_QUERY = 0;  //alias of get
    const RPC_POS_GET = 0;
    const RPC_POS_POST = 1;
    const RPC_POS_DEFAULT = 1;


    //接口定义
    private $apiName;
    private $methodName;

    //初始化文件
    function __construct($apiName=null,$methodName=null){

        $this->init();  //初始化 api结构

        if(!empty($apiName))
            $this->apiName = $apiName;
            if(!empty($methodName))
                $this->methodName = $methodName;

    } //defaultInfo="requestPage:1,perPageCount:10"
    public  function init()
    {
        if( !self::$docInfoCache )
        {
            $docInfo = parse_ini_file( RPC_CONFIG::getRpcConfigFile(), true);
            $this->buildDocInfoArray($docInfo); //手动配置api
            $docInfo = parse_ini_file( 'baseApi.ini', true); //自动api
            $this->buildDocInfoArray($docInfo);
            self::$docInfoCache=$this->docInfo;
        }else{
            $this->docInfo = self::$docInfoCache;
        }

    }
    public function generateModel()
    {
        BuildModel::exec($this->docInfo);
    }

    public function setApiName( $name )
    {
        if(empty($name)) return ;
        $this->apiName = $name;
    }

    public function setMethodName( $name )
    {
        if( empty( $name ) ) return ;
        $this->methodName= $name;
    }

    /** 这里合并2个位置的配置！** is ok**/
    public function getFiledInfo($fieldName)
    {
        $tf = new field();  //临时字段对象
        $apiName = $this->apiName;
        $methodName = $this->methodName;

        //@todo deal what for !! wating
        $apiHelp = new docInfoHelp($apiName, $methodName);
        // 		$fieldName =$apiHelp->getParamsInfo();
        // 		de($fieldName);
        if(!isset($this->docInfo[$apiName])) return null;

        //may be insert the info ?

        $apiInfo =  $this->docInfo[$apiName];
        if(isset($apiInfo[$methodName]) ) $methodInfo = $apiInfo[$methodName];
        if(isset($apiInfo [self::API_COMM_FIELD_INFO]))
            $commandFieldInfo = $this->docInfo[$apiName][self::API_COMM_FIELD_INFO];

            if(isset(  $commandFieldInfo[$fieldName] ))
            {
                $fo = $commandFieldInfo[$fieldName];
                $tf = $fo;
            }
            //覆盖公共信息  //其他信息可能访问不到！
            if(isset($methodInfo[self::PARAM_KEY][$fieldName] ))
            {
                $mp = $methodInfo[self::PARAM_KEY][$fieldName];
                $tf->tag = $mp->tag;
                if($mp->rpcPos!==NULL) $tf->rpcPos = $mp->rpcPos;
            }
            //默认走post
            if(check::isNull($tf->rpcPos ))
            {
                $tf->rpcPos = self::RPC_POS_POST;
            }
            //     	de($this->medthodParamsInfo);
            if(is_null($tf->tag))
            {
                $tf->tag = null;
            }
            return $tf;
    }

    public function getMethodParamsInfo()
    {
        if(isset($this->docInfo[$this->apiName][$this->methodName][self::PARAM_KEY]))
            return $this->docInfo[$this->apiName][$this->methodName][self::PARAM_KEY];
            else
                return null;
    }

    private function buildFiledInfo($fieldName)
    {
        $tf = new field();  //临时字段对象
        if(isset(  $this->fieldInfoArr[$fieldName] )) //!!
        {
            $fo =  $this->fieldInfoArr[$fieldName];
            $tf = $fo;
        }
        //覆盖公共信息  //其他信息可能访问不到！
        if(isset( $this->medthodParamsInfo[$fieldName] )) //!!
        {
            $mp =  $this->medthodParamsInfo[$fieldName];
            $tf->tag = $mp->tag;
            if($mp->rpcPos!==NULL) $tf->rpcPos = $mp->rpcPos;
        }
        //默认走post
        if(self::isNull($tf->rpcPos ))
        {
            $tf->rpcPos = self::RPC_POS_POST;
        }
        //     	de($this->medthodParamsInfo);
        if(is_null($tf->tag))
        {
            $tf->tag = 0;
        }
        return $tf;
    }


    private function buildDocInfoArray($docInfo)
    {
        foreach($docInfo as $apiName=>$apiInfo)
        {
            $this->buildApiInfo($apiName,$apiInfo);
            //查找远程映射 以及返回结果过滤信息
            $this->buildApiCommFieldInfo($apiName,$apiInfo);

            $this->buildApiKey($apiName,$apiInfo);
            $this->buildRpcMethod($apiName,$apiInfo);

            //获取映射信息
            // 			$this->docInfo[$api]
        }
    }

    private function buildApiKey( $apiName, $apiInfo )
    {
        if( isset($apiName) && isset($apiInfo[self::API_KEY_NAME]) )
            $this->docInfo[$apiName][self::API_KEY_NAME] = $apiInfo[self::API_KEY_NAME];
    }
    public function getApiKey()
    {
        if(isset( $this->docInfo[$this->apiName][self::API_KEY_NAME]) )
            return $this->docInfo[$this->apiName][self::API_KEY_NAME];
            else
                return null;
    }
    private function buildApiCommFieldInfo($apiName,$apiInfo)
    {
        $this->paraseRpcPostField( $apiName, $apiInfo );
        $this->parasRpcQueryField( $apiName, $apiInfo );
    }
     
     
    /**
     * 重要方法需要调整  构造方法和返回结果信息
     * methodName 对象初始化时已经初始化了，一切配置依赖于本地名称！ 和远程api无关 这里完成映射关系转换，如果没有配置则返回进来的参数
     * @param string $rpcMethodInfo
     * @return  apiName
     */
    public function buildRpcMethod($apiName,$apiInfo)
    {

        if(isset( $apiInfo[ self::RPC_METHOD_VAR_NAME ] ) )
        {
            $rpcMethodInfo = $apiInfo[self::RPC_METHOD_VAR_NAME];
        }
        //没有配置就断掉了
        if(empty($rpcMethodInfo)) return null;
        $arr = preg_split('/[\n;]/',$rpcMethodInfo); //解析出函数数组信息
        //     	$arr= explode(";", $rpcMethodInfo );  //解析出函数映射或者 和返回处理定义
        if(count($arr) > 0 )
        {
            foreach ( $arr as  $methodInfoStr )
            {
                $this->_rpcMethod($apiName,$methodInfoStr);
            }
        }
    }

    private function _rpcMethod($apiName,$methodInfoStr)
    {
         
        $methodInfoStr = trim($methodInfoStr);
        if(strlen($methodInfoStr)<=0)return false;
         
        $methodArr = preg_split('/[\s]+/',$methodInfoStr);  //切分参数以及配置信息
        $pnum=count($methodArr);
        if($pnum<=0) return false;
         
        $resulto = new resultInfo();
         
        for($i=0;$i<$pnum;$i++)
        {
            switch($i)
            {
                case 0:  //远程映射方法名
                    $methodi= $methodArr[$i];
                    $methodi=trim($methodi);
                    if($methodi)
                    {
                        $lmethodName = $this->_rpcMethodName( $apiName, $methodi );
                    }
                    break;
                case 1: //select field 信息
                    $resulto->keyField = trim($methodArr[$i]);
                    break;
                case 2:  //key 字段信息
                    $resulto->selectField = trim($methodArr[$i]);
                    break;
                case 3:   //定位key信息
                    $resulto->posTagField = trim($methodArr[$i]);
                    break;
                case 4:
                    $resulto->dataFormat = trim($methodArr[$i]);
                     
            }
        }
        $this->docInfo[$apiName][$lmethodName][self::RESULT_KEY_NAME] =$resulto; //构造参数信息
         
    }

    public function getMethodResConf($lmethodName=null)
    {
        $apiName =$this->apiName;
        $lmethodName = $this->methodName;

        if(isset($this->docInfo[$apiName][$lmethodName][self::RESULT_KEY_NAME]))
            return $this->docInfo[$apiName][$lmethodName][self::RESULT_KEY_NAME];
            else{
                return null;
            }
             
    }
    //获取远程方法名
    public function getRpcMethod()
    {
        /** new class parase  **/
        $apiHelp = new docInfoHelp($this->apiName, $this->methodName);
        $path = $apiHelp->getPathInfo();
        if(!empty( $path ))
        {
            return $path;
        }
        $lmethodName = $this->methodName;
        if(isset($this->docInfo[$this->apiName][$lmethodName][self::REMOTE_METHOD_NAME] ))
        {
            $rmethodName = $this->docInfo[$this->apiName][$lmethodName][self::REMOTE_METHOD_NAME];
            $apiName = strtolower($this->apiName);
            return $rmethodName;
        }else{
            //     		return $lmethodName;  //这个可以传默认是
            return null;  //新规则远端方法名为空
        }
    }

    public function getDefault($fieldName)
    {
        //先从函数配置的地方找
        if(isset($this->docInfo[$this->apiName][$this->methodName][self::PARAM_KEY][$fieldName]))
        {
            $mp = $this->docInfo[$this->apiName][$this->methodName][self::PARAM_KEY][$fieldName];
            if(!is_null($mp->defaultValue))
            {
                return $mp->defaultValue;
            }
        }
         
        //     		de($this->docInfo[$this->apiName]);
        //从公共地方找默认值
        if(!isset($this->docInfo[$this->apiName][self::API_COMM_FIELD_INFO][$fieldName]))
            return null;
            else
            {
                $fo = $this->docInfo[$this->apiName][self::API_COMM_FIELD_INFO][$fieldName];
                if(isset($fo->defaultValue))
                {
                    return $fo->defaultValue;
                }
            }
    }
    private function _rpcMethodName($apiName,$methodNameInfoStr)
    {
        if( strpos($methodNameInfoStr, ":" ) !== false )
        {
            $methodInfoArr = explode( ":", trim( $methodNameInfoStr) );

            $lmethodName = trim($methodInfoArr[0]);
            $rmethodName = trim($methodInfoArr[1]);

            if( $lmethodName && $rmethodName)
            {
                $rpcMethodName = $rmethodName;
            }else {
                $rpcMethodName = null;
            }
        }else{
            $lmethodName = $methodNameInfoStr;
            $rpcMethodName = null;
        }
        $this->docInfo[$apiName][$lmethodName][self::REMOTE_METHOD_NAME] = $rpcMethodName;  //构造参数信息
        return $lmethodName;
    }
    /**
     * 在这里构造所有的方法信息了！ 一个方法的
     * 提出调用方法必须的那些字段 这个初始化参数调用，负责初始化函数参数信息
     * @param unknown $rpcMethodName  远程方法限定的必传参数 别名必须找到对应的远程方法之后才能在这里调用
     * @return boolean|Ambigous <number, multitype:>
     */
    private function buildApiInfo($apiName,$apiInfo)
    {
        //获取配置的方法参数信息
        if(isset( $apiInfo[self::RPC_METHOD_PARAMETER_NAME]))
        {
            $methodParamInfos =$apiInfo[self::RPC_METHOD_PARAMETER_NAME];
        }
         
        if(!isset($methodParamInfos)) return null;
         
         
        $arr = preg_split('/[\n;]/',$methodParamInfos); //解析出函数数组信息
        //     	$arr= explode(";", $methodParamInfos ); //字段信息配置
        if(count($arr)>0)
        {
            foreach($arr as $methodParamInfoStr ) //解析出方法 参数的信息
            {
                $methodParamInfoStr = trim($methodParamInfoStr);
                if(empty($methodParamInfoStr)) continue;
                if(strpos($methodParamInfoStr,':'))
                {
                    $methodParamInfo= explode(":",trim($methodParamInfoStr));
                    $methodName =$methodParamInfo[0];  //save method name
                    $tMethodParaStr = $methodParamInfo[1];  //save the paramstr

                    //设置参数部分信息
                    if( $methodName && $tMethodParaStr )  //如果是匹配的方法名则进行解析参数解析
                    {
                        if(!check::isNull($tMethodParaStr)){
                            $methodParamsArr = preg_split('/[,\s+]/',$tMethodParaStr); //解析出函数数组信息
                            foreach( $methodParamsArr as  $param)
                            {
                                if(trim($param))
                                {
                                    $this->_setMethodParamInfo($apiName,$methodName,$param);
                                }
                            }
                        }
                    }
                    //设置result部分信息
                }
            }
        }
    }
    /**
     * 最终实现实现方法
     * @param unknown $apiName
     * @param unknown $methodName
     * @param unknown $param
     */
    private function _setMethodParamInfo($apiName,$methodName,$param)
    {
        $param = trim($param);
        if(strpos($param,'.') !== false )
        {
            $tArr = explode('.',$param);
            // 			$tArr = preg_split('/[\s,]+/',$param);
            $num=count($tArr);
            for($idx=0;$idx<$num;$idx++)
            {
                switch($idx)
                {
                    case 0:
                        $paramName = trim($tArr[0]); //参数名
                        break;
                    case 1:
                        $tag = trim($tArr[1]);
                        if($tag=="*") $tag =  1;  //是否是必填字段 0 可以不填 不提交
                        break;
                    case 2:
                        $defaultValue = trim($tArr[2]);
                        if($defaultValue=="*")  //默认值 如果为*号表示没有默认值
                        {
                            $defaultValue= null;
                        }
                        break;
                    case 3:
                        $rpcPos = trim($tArr[3]); //0表示query   1 post  //数据位置
                        if($rpcPos =='*') $rpcPos =self::RPC_POS_POST;
                        break;
                }
            }
        }else
        {
            $paramName= $param;
            $defaultValue = null;
            $rpcPos = self::RPC_POS_POST;  //默认post
            $tag=0; //0表示非必填信息
        }
        if(!isset($tag))$tag = null;
        if(!isset($rpcPos))$rpcPos= null;
        $po = new methodParam();
        $po->paramName=$paramName;
        $po->tag = $tag;
        $po->rpcPos= $rpcPos;
        if(isset( $defaultValue))	$po->defaultValue = $defaultValue;   //默认值需要新的构造逻辑

        //     	$this->medthodParamsInfo[$paramName]= $po;
        $this->docInfo[$apiName][$methodName][self::PARAM_KEY][$paramName] = $po;  //构造参数信息
    }
    private function paraseRpcPostField($apiName,$apiInfo)
    {
        $this->buildRpcParams($apiName,$apiInfo, self::POST_FIELD_NAME );
    }

    private function parasRpcQueryField($apiName,$apiInfo)
    {
        $this->buildRpcParams($apiName,$apiInfo, self::QUERY_FIELD_NAME );
    }
    /**
     *
     * @param unknown $dataPos  //参数默认值配置信息
     *  ;格式<rpcField>:<本地获取方法>:<本地获取字段名>:<数据类型效验.长度>:<本地获取的数据转换回调>
     *  queryFiled="date:get:datex:int.10:data,requestPage::int.5,perPageCount:page:int.10,campaign:campaignId:int.10,Ac:str.128"  //参数配置
     */
    private function buildRpcParams($apiName,$apiInfo, $dataOutPos )
    {
        if(empty($dataOutPos)) return false;
        if(isset( $apiInfo[$dataOutPos] ))
            $paramStr = $apiInfo[$dataOutPos]; //参数信息
            if( empty ($paramStr) ) return false;
             
            //优先匹配都好分割
            if(strpos($paramStr,',')!==false){
                $fieldInfoArr= explode(",",$paramStr ); //字段信息配置
            }elseif(strpos($paramStr,"\n")!==false ){
                $fieldInfoArr= explode("\n",$paramStr ); //字段信息配置
            }
             
            if(!isset($fieldInfoArr) ) return false;
            if(count($fieldInfoArr) == 0 ) return false;
            foreach($fieldInfoArr as $key=>$fieldStr)
            {
                $fieldStr = trim($fieldStr);
                $arr=array();
                if(empty($fieldStr)) continue;
                //$fieldArr = explode(":",$fieldStr);
                $fieldArr = preg_split('/[\s]+/',$fieldStr);  //单个字段的配置数组信息
                //list($fieldName,$pos,$localField,$typeStr,$callBack) = $fieldArr;
                $count= count($fieldArr);
                $f = new field();
                for($i=0;$i<$count;$i++)
                {
                    switch($i)
                    {
                        case 0:
                            $f->fieldName = $fieldArr[0];
                            break;
                        case 1:
                            if( $fieldArr[1] !='*')
                                $f->pos = $fieldArr[1];
                                else
                                    $f->pos=null;
                                    break;
                        case 2:
                            if($fieldArr[2] !='*')
                                $f->localField= $fieldArr[2];
                                else
                                    $f->localField=null;
                                    break;
                        case 3:
                            if( $fieldArr[3] !="*")
                            {
                                $typeStr= $fieldArr[3];
                            }else {
                                $typeStr= null;
                            }
                            if( $typeStr && strpos($typeStr,'.') !== false )
                            {
                                list($type,$typeLen ) = explode('.',$typeStr);  //类型验证信息  通过回调转移到系统yii支持的方法中，或自定义的方法中，通过映射， callBack应该可以省略掉
                                $f->typeLen= $typeLen;
                                $f->type= $type;
                            }else{
                                $f->type= $typeStr;
                            }
                            break;
                        case 4:
                            $f->callBack = $fieldArr[4];
                            if($f->callBack=='*') $f->callBack=null;
                            break;
                        case 5:
                            $f->defaultValue = trim($fieldArr[$i]);
                            break;
                    }
                }
                if($dataOutPos == self::QUERY_FIELD_NAME)  //根据情景切换不同的叫法
                    $f->rpcPos= self::RPC_POS_QUERY;

                    if($dataOutPos == self::POST_FIELD_NAME)
                        $f->rpcPos = self::RPC_POS_POST;

                        //     		$this->fieldInfoArr[$f->fieldName] = $f;
                        $this->docInfo[$apiName][self::API_COMM_FIELD_INFO][$f->fieldName] = $f;
            }

    }

}

class readyData{
    public $apiName;
    public $methodName;

    private $data;  //设置的变量
    private $conf;
    private $out;
    function __construct($apiName,$methodName,$data)
    {
        $this->apiName = $apiName;
        $this->methodName=$methodName;
        $this->conf= new  docInfox($apiName,$methodName);
        $this->data= $data;
        $this->out= new out();  //返回结果保存对象
    }
    /**
     * 关联调用方法 参数信息 以及参数类型及验证信息 默认信息 进行整合输出
     *
     * 填充数据
     * 字长效验
     * 以及其他检查
     *
     * **/
    public function buildOutData()
    {
        //构造用户设定的数据 按照什么构造就怎么输出
        $hasSetKeyArr = $this->_buildData(); //构建同时返回已经设置了的key序列
        $medthodParamsInfo = $this->conf->getMethodParamsInfo();
        $this->generatePath(); //构造pathinfo
        //根据配置信息进行参数匹配和修正 构造get post
        if($medthodParamsInfo )
        {
            //获取方法配置的参数信息
            $outFieldArr = $medthodParamsInfo;
            //如果返回已配置信息则匹配是否有没有设置的字段
            if(is_array($hasSetKeyArr))
            {
                $noSetParamKey = array_diff( array_keys( $outFieldArr ), $hasSetKeyArr);
            }else
            {
                $noSetParamKey = $outFieldArr;
            }
            if( is_array( $noSetParamKey ) ) $this->autoBuild( $noSetParamKey );
        }

    }
    /**
     * 实现用户级别最高 自动化支持的 输出自动设定
     * @param unknown $fo
     * @param unknown $fieldName
     * @return boolean
     */
    private function getValue( $fo='',$fieldName )
    {
        //对自动获取的值进行键名转换 自动化处理的时候进行转换
        if( $fo->localField && $fo->localField!='*' )
        {
            $fieldNameArr = array( $fieldName, $fo->localField );
        }else{
            $fieldNameArr = array( $fieldName );
        }
        foreach( $fieldNameArr as $tFieldName )
        {
            if($fo->pos=='post' && !check::isNull($value = $this->data->getPost($tFieldName) )) break;
            if($fo->pos=='get' &&  !check::isNull($value = $this->data->getGet($tFieldName))) break;
            if(!check::isNull( $value = $this->data->getData( $tFieldName )))break;
            // 					de($tFieldName);
            // 					de($value);
            //有配置的优先取 从view直接取数据
            if ($fo->pos=='post' &&  !check::isNull($value = in::getPost($tFieldName) ) )  break;
            if( $fo->pos=='get' &&  !check::isNull($value = in::getGet($tFieldName) ) ) break;

            //无配置 无设定从view直接取
            if( !check::isNull($value = in::getPost($tFieldName) ) )  break;
            if( !check::isNull($value = in::getGet($tFieldName) ) ) break;
            if( !check::isNull($value = $this->conf->getDefault($tFieldName)) ) break;  //获取默认值
        }
        if(check::isNull($value) )
        {
            return NULL;
        } else{
            return $value;
        }
    }


    //处理用户手动设定的data数据
    private function _buildData()
    {
        $tStoreKey=array();
        $methodParamsInfo = $this->conf->getMethodParamsInfo();
        if( !empty($methodParamsInfo) )
            $confParamArr = array_keys($methodParamsInfo);

            $gets = $this->data->getGets();
            if( $gets )
            {
                foreach($gets as $key=>$value)
                {
                    if(out::hasGetKey($key)) continue ;
                    if(isset($confParamArr) && !in_array($key,$confParamArr ))
                    {
                        continue;
                    }
                    $fo = $this->conf->getFiledInfo($key);
                    $fo->rpcPos=self::RPC_POS_QUERY;
                    $this->_setOut($key,$value,$fo);
                    $tStoreKey[]=$key;
                }
            }
            	
            $posts = $this->data->getPosts();
            if( $posts )
            {
                foreach($posts as $key=>$value)
                {
                    if(out::hasPostKey($key)) continue ;
                    if(isset($confParamArr) && !in_array($key,$confParamArr ))
                    {
                        continue;
                    }
                    $fo = $this->conf->getFiledInfo( $key );
                    $fo->rpcPos = self::RPC_POS_POST;
                    $this->_setOut( $key, $value , $fo );
                    $tStoreKey[]=$key;
                }
            }
            	
            $data = $this->data->getDatas();
            if( $data )
            {
                foreach($data as $key=>$value)
                {
                    if(isset($confParamArr) && !in_array($key,$confParamArr ))
                    {
                        continue;
                    }
                    $fo = $this->conf->getFiledInfo($key);
                    	
                    $this->_setOut( $key, $value , $fo );
                    $tStoreKey[]=$key;
                    	
                }
            }
            return $tStoreKey;
    }
    /**
     * 配置文件 该部分只有在有设定的情况下才会进行，仅对用户没有设定的参数进行主要从前端自动匹配
     * @param unknown $outFieldArr
     */
    private function autoBuild($outFieldArr)
    {
        foreach( $outFieldArr as $fieldName )
        {
            $this->_setOut( $fieldName );
        }

    }
    //自动补充
    private function _setOut($key,$value=NULL,$fo='')
    {
        if(empty($fo))
        {
            $fo = $this->conf->getFiledInfo( $key );
        }
        if(empty($fo)) $fo= new field();
        //获取数据  如果是空则获取数据  //补充部分在设置部分是不传值的 用户部分则认定是有值的
        if(check::isNull( $value ))
        {
            $value = $this->getValue( $fo, $key );
            if(isset($value) && is_string($value))
                $value = trim($value);
        }
        //如果没有传如 也取不到数据 如果必须有数据则 抛出异常否则就填空  为0 表示默认填空  如果为1 则表示必填不能为空 为2表示没有设置不提交这个字段
        // 		var_dump($fo);

        if($value==='null' || $value==='NULL')
        {
            $value = NULL;
        }

        if(is_null($fo->tag) && is_null($value) && !is_array($value))
        {
            $value = "";
        }elseif($fo->tag==='1' && check::isNull($value) )
        {
            throw New Exception("$this->methodName  $key not set Value,please check.");
        }elseif($fo->tag==='0' && check::isNull($value)){
            return ;
        }
        //数据效验 // 类型及长度
        if( $fo->type && $value )
        {
            if( !$this->callBack( $fo->type, $value )) {
                throw new Exception("$key's type is error,type must is  {$fo->type}, please check.");
            }
        }
        if($fo->typeLen && $value )
        {
            $isOk = $this->callBack( 'len', $value , $fo->typeLen ) ;
            if( empty( $isOk ) )
                throw new Exception("$key's value [$value] len is error,max len is  {$fo->typeLen}, please check.");
        }
        //对数据进行特定处理
        if($fo->callBack)
        {
            $value = $this->callBack( $fo->callBack, $value );
        }
        //方法特定位置优先级别 先设定到有方法参数配置的的地方，公共设定将失效
        if($fo->rpcPos == docInfoX::RPC_POS_QUERY  )
        {
            if(out::hasGetKey($key)) return ;
            out::setQuery($key,$value);
        }
        	
        if( $fo->rpcPos == docInfoX::RPC_POS_POST )
        {
            if(out::hasPostKey($key)) return ;  //model已经设定就不再处理
            out::setPost($key,$value);  //给post设置值
        }
    }
    private function callBack( $callBackInfo, $value,$valuet=''  )
    {
        //现在map映射里边找对应的处理实现，如果没有就是设定本身
        if( RPC_CONFIG::$map[$callBackInfo] )
            $callBackFun = RPC_CONFIG::$map[$callBackInfo];
            else{
                $callBackFun = $callBackInfo;
            }
            if( $valuet )
                return  call_user_func( $callBackFun, $value,$valuet );
                else
                    return  call_user_func( $callBackFun, $value );
    }
    /**build path**/
    public function generatePath()
    {
        //如果已经设定则直接退出
        $path = out::getPath();
        if($path) return;

        $pathTemplate = $this->conf->getRpcMethod();
        if(is_null($pathTemplate))
        {
            out::setPath('/');
            return ;
        }

        //正则批配出两个变量名 找出然后再正则 替换
        $pattern = '/\{(.*?)\}/';
        // 		$replacement = '{\$$1}';
        preg_match_all($pattern, $pathTemplate, $matches);

        if(isset($matches[1])) $paramsList = $matches[1];
        if(is_array($paramsList))
        {
            foreach($paramsList as $key=>$paramsName )
            {
                $fo= new field();
                $findStr = $matches[0][$key];
                $value= $this->getValue($fo,$paramsName);
                if(is_null($value)) $value='';
                $pathTemplate = str_replace($findStr,$value, $pathTemplate);
            }
        }
        $path = $pathTemplate ;

        out::setPath($path); //生成最终路径

    }


}

class debug{
    public $reqId;
    public $class;
    public $method;
    public $query;
    public $post;
    public $result;
    public $jsonData;

    //出现问题的文件和行
    public $file;
    public $line;
    public $ExceptionMessage;
}
class field
{
    public $fieldName;
    public $pos;   //来源位置
    public $rpcPos;  //输出给远端的参数位置
    public $localField; //view表单位置
    public $type;   //数据类型
    public $typeLen;  //数据长度
    public $callBack;  //回调类型

    public $defaultValue;   //默认值

    public $tag;  //??
}

class iString
{
    //从开头搜索关键字
    public static function keySearch($str,$search)
    {
        $len = strlen($search);

        if( substr($str,0,$len-1) == $search )
            return true;
            else
                return false;
    }
}

//负责从前端接受来的数据处理初级处理 传给协议分析
class in
{
    private static $get;
    private static $post;
    private static $IN=array();
    public static function INIT()
    {
        self::setGet();
        self::setPost();
        self::$IN=self::parse_incoming();
    }
    public static function setGet()
    {
        self::$get = $_GET;
    }
    public static function setPost()
    {
        self::$post = $_POST;
    }
    public static function getGet($key)
    {
        if(!isset(self::$get[$key]))
            return null;
             
            return self::$get[$key];
    }

    public static function getPost($key)
    {
        if(isset(self::$post[$key]))
            self::$post[$key];
            else{
                return null;
            }
    }
    //深度搜索
    public static function __getPost($key)
    {
         
    }

    private static function parse_incoming( )
    {
        global $_GET;
        global $_POST;
        global $HTTP_CLIENT_IP;
        global $REQUEST_METHOD;
        global $REMOTE_ADDR;
        global $HTTP_PROXY_USER;
        global $HTTP_X_FORWARDED_FOR;
        $return = array( );
        if ( is_array( $_GET ) )
        {
            foreach( $_GET as $key=>$value ){
                if ( is_array( $_GET[$key] ) )
                {
                    foreach( $_GET[$key] as $key2=>$value2 ){
                        $key2 = self::clean_key( $key2 );
                        if ( !$key2 ) continue;
                        $return[$key][] = self::clean_value( $value2 );
                    }
                } else {
                    $return[$key] = self::clean_value( $value );
                }
            }
        }
        if ( is_array( $_POST ) )
        {
            foreach( $_POST as $key=>$value ){
                if ( is_array( $_POST[$key] ) )
                {
                    foreach( $_POST[$key] as $key2=>$value2 ){
                        $key2 = self::clean_key( $key2 );
                        if ( !$key2 ) continue;
                        $return[$key][self::clean_key( $key2 )] = self::clean_value( $value2 );
                    }
                } else {
                    $return[$key] = self::clean_value( $value );
                }
            }
        }
        $addrs = array( );
        foreach ( array_reverse( explode( ",", $HTTP_X_FORWARDED_FOR ) ) as $x_f )
        {
            $x_f = trim( $x_f );
            if ( preg_match( "/^\\d{1,3}\\.\\d{1,3}\\.\\d{1,3}\\.\\d{1,3}\$/", $x_f ) )
            {
                $addrs[] = $x_f;
            }
        }
        $addrs[] = $_SERVER['REMOTE_ADDR'];
        $addrs[] = $HTTP_PROXY_USER;
        $addrs[] = $REMOTE_ADDR;
        $return['IP_ADDRESS'] = self::select_var( $addrs );
        $return['IP_ADDRESS'] = preg_replace( "/^([0-9]{1,3})\\.([0-9]{1,3})\\.([0-9]{1,3})\\.([0-9]{1,3})/", "\\1.\\2.\\3.\\4", $return['IP_ADDRESS'] );
        $return['request_method'] = $_SERVER['REQUEST_METHOD'] != "" ? strtolower( $_SERVER['REQUEST_METHOD'] ) : strtolower( $REQUEST_METHOD );
        if ( isset($return['op']))
        {
            $data = explode( ";", $return['op'] );
            foreach ( $data as $key => $var )
            {
                $data1 = explode( "::", $var );
                $return["{$data1[0]}"] = $data1[1];
            }
        }
        return $return;
    }

    private static function select_var( $array )
    {
        if ( !is_array( $array ) )
        {
            return -1;
        }
        ksort( $array );
        $chosen = -1;
        foreach ( $array as $k => $v )
        {
            if ( isset( $v ) )
            {
                $chosen = $v;
                break;
            }
        }
        return $chosen;
    }

    private static function clean_key( $key )
    {
        if (empty($key))
        {
            return  $key;
        }
        $key = preg_replace( "/\\.\\./", "", $key );
        $key = preg_replace( "/\\_\\_(.+?)\\_\\_/", "", $key );
        $key = preg_replace( "/^([\\w\\.\\-\\_]+)\$/", "\$1", $key );
        return $key;
    }

    private static function clean_value( $val )
    {
        if ( empty($val) )
        {
            return $val;
        }
        if ( get_magic_quotes_gpc( ) )
        {
            $val = stripslashes( $val );
        }
        return $val;
    }


    private static function escape($string, $esc_type = 'html')
    {
        switch ($esc_type) {
            case 'html':
                return htmlspecialchars($string, ENT_QUOTES);
            case 'htmlall':
                return htmlentities($string, ENT_QUOTES);
            case 'url':
                return urlencode($string);
            case 'quotes':
                // escape unescaped single quotes
                return preg_replace("%(?<!\\\\)'%", "\\'", $string);
            case 'hex':
                // escape every character into hex
                $return = '';
                for ($x=0; $x < strlen($string); $x++) {
                    $return .= '%' . bin2hex($string[$x]);
                }
                return $return;
            case 'hexentity':
                $return = '';
                for ($x=0; $x < strlen($string); $x++) {
                    $return .= '&#x' . bin2hex($string[$x]) . ';';
                }
                return $return;
            case 'javascript':
                // escape quotes and backslashes and newlines
                return strtr($string, array('\\'=>'\\\\',"'"=>"\\'",'"'=>'\\"',"\r"=>'\\r',"\n"=>'\\n'));

            default:
                return $string;
        }
    }

}

//接口方法
class get
{
    private $query;
    public function clean()
    {
        $this->query=array();
    }
    public function __set($key,$value)
    {
        $this->set($key,$value);
    }

    public function set($key,$value)
    {
        $isArr = is_array($key);
        if( $isArr && $this->query)
        {
            $this->query= array_merge($this->query,$key);
        }elseif($isArr)
        {
            $this->query = $key;
        }else{
            $this->query[$key]=$value;
        }
    }


    public function __get($name)
    {
        return $this->query[$name];
    }

    public function getGets()
    {
        return $this->getGet();
    }

    public function getGet($key='')
    {
        if( $key && isset($this->query[$key]))
            return $this->query[$key];
            elseif($key)
            return null;
            else
                return $this->query;
    }

}
//接口方法
class post
{
    private $post;
    public function clean()
    {
        $this->post=array();
    }
    public function __set($key,$value)
    {
        $this->set($key,$value);
         
    }
    public function __get($name)
    {
        if(isset($this->post[$name]))
            return $this->post[$name];
    }
    public function set($key,$value)
    {
        if(is_array($key) && self::$post)
        {
            $this->post= array_merge($this->post,$key);
        }elseif(is_array($key)){
            $this->post =$key;
        }else{
            $this->post[$key]=$value;
        }
    }

    public function getPosts()
    {
        return $this->getPost();
    }
    public function getPost($key='')
    {
        if( $key && isset($this->post[$key]))
            return $this->post[$key];
            elseif($key)
            {
                return null;
            }else{
                return $this->post;
            }
    }
}

class _data
{
    private $_data=array();
    public function clean()
    {
        $this->_data= array();
    }
    //最小化配置情况下的路径
    public function __set($key,$value)
    {
        $this->set($key,$value);
    }
    public function hasSet($key)
    {
        if(isset($this->_data[$key]))
            return true;
            else{
                return false;
            }
    }
    public function set($key,$value)
    {
        if(empty($key)) return false;
        if(is_object($key))
        {
            $key = AR::ota($key);
        }
        $isArr = is_array($key);
        if( $isArr && $this->_data )
        {
            $this->_data = array_merge($this->_data,$key);
        }elseif($isArr)
        {
            $this->_data = $key;
        }else {
            $this->_data[$key]=$value;
        }
    }
    public static function setData($name,$value)
    {
        $this->_data[$name]=$value;
    }

    public function getData($key)
    {
        if($key && isset($this->_data[$key]))
        {
            return	$this->_data[$key];
        }elseif($key)
        {
            return null;
        }else
            return $this->_data;

    }
    public function getDatas()
    {
        return $this->_data;

    }


}
/**
 * 数据格式分析
 **/
class data{

    public $_data;
    public $_get;
    public $_post;
    function __construct()
    {
        $this->_data= new _data();
        $this->_get = new get();
        $this->_post = new post();
    }

    public function getDatas()
    {
        return $this->_data->getDatas();
    }

    public function getData($key)
    {
        return $this->_data->getData($key);
    }

    public function getGet($key)
    {
        return $this->_get->getGet($key);
    }

    public function getGets()
    {
        return $this->_get->getGets();
    }
    public function getPost($key)
    {
        return $this->_post->getPost($key);
    }
    public function getPosts()
    {
        return $this->_post->getPosts();
    }
    //清空
    public function clean()
    {
        $this->_data->clean();
        $this->_get->clean();
        $this->_post->clean();

    }
    public static function map($array,$keyField,$selectFields=null,$posTagField=null)
    {
        return AR::map($array,$keyField,$selectFields,$posTagField);
    }

}
//输出给rpc的最终数据 从这里设定的数据是处理完成后的
class  out
{
    private static $query;  //get部分的数据
    private static $post;
    private static $path;
    private static $header;

    //     private $_query;
    //     private $_post;
    //     private $_path; //path 部分数据
    //    	private $_header; //头部数据 暂时没有空着
    public static function setPost($key,$value='')
    {
        if(is_array($key) && self::$post)
        {
            self::$post= array_merge(self::$post,$key);
        }elseif(is_array($key)){
            self::$post =$key;
        }else{
            self::$post[$key]=$value;
        }
    }
    //头部不清理！
    public static function setHeader($key,$value='')
    {
        if(is_array($key) && self::$header)
        {
            self::$header= array_merge(self::$header,$key);
        }elseif(is_array($key)){
            self::$header =$key;
        }else{
            self::$header[$key]=$value;
        }
    }

    public static function getHeader()
    {
        return self::$header;
    }

    public static function hasPostKey($key)
    {
        if(isset(self::$post[$key]))
            return true;
            else
                return false;
    }
    public static function hasGetKey($key)
    {
        if(isset(self::$query[$key]))
            return true;
            else
                return false;
    }
    /**
     * set path data this is different post and query the path string is generate by readyData
     */
    public static function setPath($path)
    {
        self::$path = $path;
    }
    public static function getPath()
    {
        return self::$path;
    }

    //默认设置方法
    public static function setGet($key,$value)
    {
        self::setQuery($key,$value);
    }

    //config query
    public static function setQuery($key,$value)
    {
        $isArr = is_array($key);
        if( $isArr && self::$query)
        {
            self::$query= array_merge(self::$query,$key);
        }elseif($isArr)
        {
            self::$query = $key;
        }else{
            self::$query[$key]=$value;
        }
    }
    //generate query all
    public function generateQuery()
    {
        $get = self::getGet();
        if(empty($get))
        {
            return false;
        }
        $query ='';
        foreach($get as $key=>$value)
        {
            if(!empty($key) )
            {
                if(empty($query))
                    $query = "$key=$value";
                    else
                        $query .= "&$key=$value";
            }
        }
        if(isset($query))
            return $query;
            else{
                return null;
            }
    }

    public function clean()
    {
        self::$query = array();
        self::$post = array();
        self::$path = null;
    }

    public static function allClean()
    {
        self::$query = array();
        self::$post = array();
    }
     
    public  function getQuery()
    {
        $query = self::generateQuery();
        return $query;
    }

    public  function getGet()
    {
        return self::$query;
    }

    public  function getPost()
    {
        return self::$post;
    }


}

if (!class_exists('CModel',true)) {
    class CModel{}
}


class serviceData
{
    private $serviceData;
    public function __get($name)
    {

    }
}
//rpc::CFormModel
class _Restfulclient extends CModel
{
    private $__domain;

    private  $__q;  //查询变量
    private  $__p;  //post变量
    private $__data; //用户数据对象 保存单个接口的所有数据

    public $__out;   //临时数据

    //out
    public $__query;
    public $__url;
    public $__post;
    public $__path;

   	protected $__control;  //对应模拟远端对象类
   	protected $__method;   //方法 对应远端方法
    public  $__isPost=false;
    protected $__outDebugInfo=array();

    const DATA_FORMAT=false; //默认obj
    private $__dataFormat = false;

    private $__debug=false; //对象debug

    private static $__self;
    private static  $_debug = false; //全部debug
    private static  $__print=false;

    public $__isBinary=false;

    protected $__RDO; //处理结果对象
    private $__MCO; //模型定义对象
    private $__ModelName;
     
    protected   $__conf;
    private $__format='json';
    public static function __init(){}

    function __construct($name = null )
    {
        $this->__data= new data();
        $this->__q= new get();
        $this->__p = new post();
        $this->__out= new out();  //初始化输出数据
        $this->__MCO  = new resultInfo(); //数据过滤

        $domain= RPC_CONFIG::getApiDmain();

        $this->setDomain($domain);
        if(empty($name))
        {
            $name = get_called_class();
        }
        $this->__ModelName = $name;
        //远程控制名
        $apiName = $this->__getRemoteApiName($name);
        $this->setControl($apiName);
        $this->__conf = new docInfox($apiName);

    }
    public function getModelName()
    {
        return $this->__ModelName;
    }
    /**
     *
     * @param unknown $name
     * @return string
     */
    private function __getRemoteApiName($name)
    {
        $apiName = strtolower($name);
        return $apiName;
    }


    public static function debug($print= false)
    {
        if($print===0 ) //只显示不记录日志
        {
            self::$_debug = false;
            self::$__print = true;
        }elseif($print== true){ //写日志并且显示
            self::$_debug = true;
            self::$__print = true;
        }else { //只记录日志 不显示
            self::$_debug = true;
        }
         
    }

    public function getDebug()
    {
        return $this->__outDebugInfo[$this->__method];
    }
    public function getDebugs()
    {
        return $this->__outDebugInfo;
    }

    public function getUrl()
    {
        $url = $this->getDomain();
        $path = $this->getPath();
        $path = preg_replace('/\/+/','/',$path);
        $query= $this->getQuery();
        if(!empty($path))
        {
            $url .= ''.$path;
        }
        if(!empty($query))
        {
            $url .= '?'.$query;
        }
        $url = preg_replace('/\/+$/','',$url);
         
        $this->__url=$url;
         
         
         
        return $url;
    }
    //静态方法 对外接口 enterance one
    public static function __callStatic($name,$arguments='')
    {
        return self::_init($name,$arguments);
    }

    private static function _init($name,$arguments='')
    {
        if(!isset(self::$__self[$name]))
        {
            $rpc = new Ac($name);
            self::$__self[$name] = $rpc;
        }
        return self::$__self[$name];
    }
    //清除用户手动设定的数据
    public function clean()
    {
        $this->__data->clean();
    }

    /**处理自身对象的**/
    private  function _clean()
    {
        $this->__out->clean();
        out::allClean();

    }
    private function getApiName()
    {
        return $this->getControl();
    }
    private function getMemVal($className)
    {
        if(false === class_exists($className)) return;
        $class = new ReflectionClass($className);
        $prop = $class->getProperties();
        if(empty($prop)) return;
        foreach($prop as $key => $propObj)
        {
            if($propObj->class == $className)
            {
                if($this->__data->_data->hasSet($propObj->name))continue; //加入已经设定了则跳过

                $pv = $propObj->getValue($this);
                if(isset($pv))
                {
                    $this->set($propObj->name, $pv );
                }
            }
        }
    }
    //类层面的实现 // 注意: $name 区分大小写
    public  function __call($name,$arguments)
    {
        //     	$data = $this->getMemVal($this->__ModelName.'Dao');
        //     	$this->set($data);
        //     	$data = $this->getMemVal($this->__ModelName);
        //     	$this->set($data);
        return $this->__callServer($name,$arguments);
    }
    public function __callServer($name='',$arguments='')
    {
        $this->__data->_get =  $this->__q;
        $this->__data->_post = $this->__p;
        $this->paseParam($arguments); //转化参数
         
        $debug = new debug();
        $apiName = $this->getControl();
        $lmethodName = $name;
        $modelName = $this->getModelName();
        //构造一个配置对象 //一个局部依赖
        $this->__conf->setMethodName($lmethodName);

        //auto readyData for out!
        try{
            $autoDeal = new readyData($modelName ,$lmethodName,$this->__data);
            $rpcName = $this->__conf->getRpcMethod();
            $autoDeal->buildOutData();
            $this->clean(); //清理数据
        }catch(Exception $e)
        {
            $debug->ExceptionMessage = $e->getMessage();
        }
        if(isset( $rpcName )) $this->__setMethod($rpcName);
        $this->setPost();
         
        //call remote may be transter data from here。
        $result = $this->callRpc();
        //deal result and debug recorder
        if( $this->__dataFormat )
            $this->__MCO->dataFormat = $this->__dataFormat;
            else
                $this->__MCO->dataFormat = self::DATA_FORMAT; //构造默认值
                try{
                    //     		if(self::$_debug || $this->__debug || self::$__print)
                        //     		{
                    $time = $this->microtimeFormat('H:i:s:x', $this->microtimeFloat());
                    $debug->class = $this->__control;
                    $debug->lmethod = $lmethodName;
                    $debug->rmethod= $this->__method;
                    $debug->reqId= $time; //date('YmdHis',microtime());
                    $debug->query = $this->__url;
                    $debug->post = $this->__post;
                    $this->__outDebugInfo[$lmethodName] = $debug;
                    $debug->jsonData = $result;
                    $_hasDebug =true;
                    //     		}
                    $deal = new dealResult($result,$this->__MCO,$modelName,$lmethodName,$this->__format);

                    $this->resetSelect();
                    $this->__RDO = $deal;
                    $result = $deal->get();
                    if(isset($debug) && $result)
                    {
                        $debug->result = $result;
                    }
                    $this->__dataFormat = self::DATA_FORMAT; //处理完后修改会默认值
                     
                        }catch(Exception $e)
                        {
                            sysLog::log($debug,self::$__print);

                            throw new Exception(  $e->getMessage()  ,$e->getCode());
                        }

                        if(isset($_hasDebug) )
                        {
                            sysLog::log($debug,self::$__print);
                        }
                        $this->setObjMember($result);
                        return $result;
                }
                public function __setResultFormat($format='txt')
                {
                    $this->__format = $format;
                }
                private function setObjMember($result=null)
                {
                     
                    if($result && is_array($result))
                    {
                        $result= current($result);
                        if(!is_array($result))return;
                        foreach($result as $k=>$v)
                        {
                            $this->$k = $v;
                        }
                    }
                }


                public function isFile($status=true)
                {
                    $this->__isBinary= $status;
                }
                private function __setModelAttrib($result)
                {
                    if($result)
                    {
                        $this->attributes= $result;
                    }
                }
                public function setDebug()
                {
                    $this->__debug= true;
                }
                function microtimeFloat()
                {
                    list($usec, $sec) = explode(" ", microtime());
                    return ((float)$usec + (float)$sec);
                }

                function microtimeFormat($tag, $time)
                {
                    if(strpos($time,'.')!==false)
                    {
                        list($usec, $sec) = explode(".", $time);
                    }else{
                        $sec = $time;
                        $usec= 0;
                    }

                    $date = date( $tag, $usec );
                    return str_replace('x', $sec, $date);
                }
                private function callRpc()
                {
                    $rc = new rpcCurl($this);
                    $result = $rc->exec();
                    $this->_clean(); //调用完成后自动清理输出参数
                    return $result;
                }

                private  function  setControl($control)
                {
                    $this->__control = $control;
                }

                private function getControl()
                {
                    return $this->__control;
                }

                //多位数组格式set($keyArr); //这样可以直接进行数据传入！
                public function set($key,$value='')
                {
                    $this->__data->_data->set($key,$value);
                    return $this;
                }

                public function select($keyField='',$selectField='',$posField=null)
                {
                    //     	if(!empty($this->__MCO->keyField) || !empty($this->__MCO->selectField)) return $this;
                     
                    $this->__MCO->keyField = $keyField;
                    $this->__MCO->selectField = $selectField;
                    $this->__MCO->posTagField = $posField;
                    return $this;
                }

                protected function resetSelect()
                {
                    $this->__MCO->keyField = null;
                    $this->__MCO->selectField = null;
                    $this->__MCO->posTagField = null;
                }

                protected  function paseParam($arguments=null)
                {
                    if(empty($arguments)) return;
                    if(is_array($arguments))
                    {
                        $paramNum = count($arguments);
                        foreach($arguments as $key=>$value)
                        {
                            if(is_object($value)) $value= AR::ota($value);

                            if(is_array($value))
                                $this->set($value);
                                else{
                                    // 	    			$this->set('id',$value);
                                }
                        }
                    }
                }

                public function obj()
                {
                     
                    $this->__dataFormat = true;
                    return $this;
                }

                //多位数组格式setQ($keyArr); //这样可以直接进行数据传入！
                public function setQ($key,$value="")
                {
                    $this->__data->_get->set($key,$value);
                }
                //多位数组格式setP($keyArr); //这样可以直接进行数据传入！
                public function setP($key,$value='')
                {
                    $this->__data->_post->set($key,$value);
                }

                public function __set($key,$value)
                {
                    $this->set($key,$value); //对象保存数据
                }
                 
                public function __get($key)
                {
                    if(!$this->__RDO) return false;
                    $result = $this->__RDO->getR($key);

                    if(is_null($result))
                        $result = $this->__data->get($key);
                        return $result;
                }

                 
                public function __setMethod($methodName)
                {
                    $this->__method= $methodName;
                }

                public function getMethod()
                {
                    return $this->__method;
                }

                private function setDomain($domain)
                {
                    $this->__domain= $domain;
                }

                /**path has generate ok by readydata**/
                public function getPath()
                {
                    $path = $this->__out->getPath();
                    if($path)
                        $this->__path="/{$this->__control}/{$path}";
                        else
                            $this->__path = "/$this->__control";
                             
                            return $this->__path;
                }
                public function getDomain()
                {
                    return $this->__domain;
                }
                public function getQuery()
                {
                    $this->__query = $this->__out->getQuery();
                    return $this->__query;
                }
                //设置post部分 针对出口
                private function setPost()
                {
                    $this->__post = $this->__out->getPost();
                    if(!empty($this->__post) )
                    {
                        $this->isPost();
                    }else{
                        $this->isPost(false);
                    }
                }
                public function getPost()
                {
                    return $this->__post;
                }
                private function isPost( $status=true )
                {
                    $this->__isPost = $status;
                }
                public function setHeader($key,$value='')
                {
                    $this->__out->setHeader($key,$value);
                }
                public function getHeader()
                {
                    return $this->__out->getHeader();
                }

                ####yii must remaining methods###########
                /**
                *
                * (non-PHPdoc) //必须在这里实现！ why
                * @see CFormModel::attributeNames()
                */

                public function attributeNames()
                {
                    return array();
                }
}

class bs{
    // 	static public  $DOMAINS = array(
    //         'madhouse.mp.com'=>array(
    //             'systemId'=>'ATD',
    //             'businessId'=>'MAHAD'
    //         ),
    //         'airwaveone.mp.com'=>array(
    //             'systemId'=>'ATD',
    //             'businessId'=>'AWOAD'
    //         ),
    //         'admin.ui.madhouse.cn'=>array(
    //             'systemId'=>'ATD',
    //             'businessId'=>'MAHAD'
    //         ),
    //         'admin.ui.airwaveone.net'=>array(
    //             'systemId'=>'ATD',
    //             'businessId'=>'AWOAD'
    //         ),
    //     );
    //for test domains
    static private $domains= array(
        'a.net',
    );

    const DEFAULT_DOMAIN = 'madhouse.mp.com';
    static public  function getbs()
    {
        $domains = self::$DOMAINS;
        $domain = self::getDomain();
        $bs = $domains[$domain];

        // 		return  $bs;
    }
    static public function getDomain()
    {
        $idomain = $_SERVER['HTTP_HOST'];
        $idomain = str_replace('www.', '', $idomain);
        if( in_array( $idomain, self::$domains ) ) //如果是配置的说明是测试域名自动转化到远端默认域名
        {
            $domain = self::DEFAULT_DOMAIN;
        }else{
            $domain = $idomain;  //没有配置则就按照取到的值传过去
        }

        return $domain;
    }
}
/**
 * yyi 框架接口层
 * @author tony
 *
 */
class Ac extends _Restfulclient{
    public $attributes;
    private static $models;
    //yyi model 自动验证  临时的实现
    protected $__key;
    public function __construct($name=null)
    {
        parent::__construct($name);
        $this->__key = $this->__conf->getApiKey();
        $domain = bs::getDomain();
        $this->setCommandHeader( $domain );
    }

    public function setCommandHeader($url='',$userId='1')
    {
        $this->setHeader('url',$url);
        $this->setHeader('userId',$userId);
    }

    public function rules()
    {
        return array();
    }

    public function preDealData()
    {

    }
    public function attributeLabels()
    {
        return array();
    }

    public function setAttributes($values,$safeOnly = true)
    {
        foreach($values as $key=>$value) {
            $this->attributes[$key]=$value;
        }
        $this->set($values);
    }

    public function getAttributes($names = NULL)
    {

        if(!is_null($names) && isset($this->attributes[$names]))
            return $this->attributes[$names];
            elseif(isset($this->attributes))
            return $this->attributes;
            else
                return null;
    }
    public static function model($className=null)
    {

        if(is_null($className))
            $className=get_called_class();

            if(isset(self::$models[$className])){
                $model = self::$models[$className];
                return $model ;
            }else{
                $model=self::$models[$className]= new $className(null);
                return $model;
            }
    }
    //后数组筛选
    public function filter($keyField,$selectFields=null,$array=null,$posTagField=null)
    {
        if(is_null($array)) $array = $this->getResult();
        return AR::map($array,$keyField,$selectFields,$posTagField);
    }



    //yii db enter
    public function reParam($params)
    {
    }
    public function findAll($post,$fields=array()) {
        $this->initc();
        return $this->select('#','data')->get($post);

    }
    private function initC()
    {
    }
    public function findList($post=null) {
        $this->initc();
        return $this->get($post);

    }
    public function getResult()
    {
        if(!$this->__RDO) return false;
        return  $result = $this->__RDO->getR();
    }

    public function getC($key='')
    {
        return $this->__RDO->getC($key);
    }

}

class rpcCurl
{
    private $rpc;  //rpc类型成员
    private  $isPost=false;
    private $url;
    private $ch;
    function __construct( $rpc)
    {
        $this->rpc=$rpc;
        $this->ch = curl_init();
    }
    public function exec()
    {
        // 		$path= $this->rpc->__out->getPath();
        // 		if(defined('_MIDDLE_WARE_API_URL') && rpc::$_debug===false)
            // 		{
            // 			$c = $this->rpc->__control;
            // 			$m = $this->rpc->__method;
            // 			return json_encode(MID::$c($this->rpc->getPost())->$m($this->rpc->getQuery()));
            // 		}
    $headerArr=array();
    $url = $this->rpc->getUrl();
    $this->url = $url;

    curl_setopt($this->ch, CURLOPT_URL,$url);
    curl_setopt($this->ch, CURLOPT_RETURNTRANSFER,1);
    if(0) curl_setopt($this->ch, CURLOPT_HEADER, 1);
    $postData =  $this->rpc->getPost();
    if($this->rpc->__isBinary)
    {
        curl_setopt($this->ch,CURLOPT_BINARYTRANSFER,true);
        curl_setopt( $this->ch,CURLOPT_POST, 1 );
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $postData);
    }
    elseif($this->rpc->__isPost )
    {
        // 			$da =AR::ato($postData);
        $jsonData =json_encode( $postData ) ;
        $headerArr = array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData ));
        	
        curl_setopt( $this->ch, CURLOPT_POST, 1 );
        curl_setopt( $this->ch, CURLOPT_POSTFIELDS, $jsonData );
    }else{ //默认全部post logic maybe modify
        $jsonData =json_encode( $postData ) ;
        $headerArr = array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData ));

        curl_setopt( $this->ch, CURLOPT_POST, 1 );
        curl_setopt( $this->ch, CURLOPT_POSTFIELDS, $jsonData );
    }

    $this->setHeader($headerArr);
    $output = curl_exec($this->ch);
    // 		de($output);
    if(0) $status=curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
    curl_close($this->ch);
    return $output;
    }
    public function setHeader($headerArr=array())
    {
        $theader = $this->rpc->getHeader();
        //循环设置头信息
        if(isset($theader) && is_array($theader))
        {
            foreach($theader as $k => $rs)
            {
                $header[] = "{$k}: {$rs}";
            }
        }
        if(!empty($headerArr))$header = array_merge($header,$headerArr);
        if($header)
        {
            curl_setopt($this->ch, CURLOPT_HTTPHEADER, $header);
        }
    }
}
class sysLog{
    private static $log_idx=0;
    //local.info /path
    public static function log(debug $debugInfoObj,$print=false)
    {

        $debugInfo = self::traceDebug();
        $debugInfoObj->file = $debugInfo['file'];
        $debugInfoObj->line = $debugInfo['line'];

        if($print === 0 || $print==true )
        {
            echo '<pre>';
            echo 'RPC ['.self::$log_idx.'] ';
            print_r($debugInfoObj);
            echo '</pre>';
        }
        if( $print !== 0  )
        {
            $basePath=dirname(dirname(__FILE__));
            if(strtoupper(substr(PHP_OS, 0, 3))==='WIN' )
            {
                $date=date("m-d-Y");
                $fp = fopen($basePath."/runtime/{$date}_rpc.log",'a');
                fwrite($fp,json_encode( $debugInfoObj ));
                fclose($fp);
            }else{
                $level=LOG_INFO;
                openlog("RPC", LOG_PID | LOG_PERROR,LOG_LOCAL3);
                syslog($level, "REQUEST".'['.self::$log_idx .']: '.json_encode( $debugInfoObj ));
                closelog();
                self::$log_idx++;
            }
        }

    }
    private static function traceDebug()
    {
        $debugInfo =  debug_backtrace();
        if(strpos($debugInfo[2]['file'],'Ac.php')===FALSE)
        {
            return $debugInfo[2];
        }elseif(strpos($debugInfo[3]['file'],'Ac.php')===FALSE)
        {
            return $debugInfo[3];
        }elseif(strpos($debugInfo[4]['file'],'Ac.php')===FALSE)
        {
            return $debugInfo[4];
        }
        else{
            return $debugInfo[5];
        }
    }
}
if (!function_exists('getallheaders'))
{
    function getallheaders()
    {
        foreach ($_SERVER as $name => $value)
        {
            if (substr($name, 0, 5) == 'HTTP_')
            {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }
}



class dealResult
{
    private $jsonData;
    private $result ;
    private $status; //结果状态

    private $fields=false; //字符串过滤用

    private $dataFormat;


    private $method_key='';
    const DATA_KEY='data';
    const ERROR_KEY='error';
    const CODE_KEY='code';

    private  $SERVER_STATUS;

    private $resConf;
    private $conf;
    //保存返回结果
    private $__R;  //数据部分
    private $__C;  //控制部分


    const RES_HEADER = 'responseHeaderDto';
    const RES_RESULT = 'results';
    //返回数据处理依赖的方法
    private $modelName;
    private $format='json';

    function __construct($result,$resConf,$modelName,$lmethodName,$format="json")
    {
        $this->format = $format;
        $this->modelName = $modelName;
        $apiName = strtolower($modelName);
        $this->jsonData = $result;
        if(!check::isNull($resConf))
            $this->resConf = $resConf;

            $this->conf= new docInfox($apiName,$lmethodName);

            $this->buildResInfo();
            $this->unCode();
            if($this->status===false) return;
            if( $this->resConf )
            {
                $this->filter();
            }
            $this->rebuildData();
            unset($this->conf); //注销掉conf 方便调试
    }
    public function setResConf($resConf)
    {
        $this->resConf = $resConf;
    }
    public function setData($result)
    {
        $this->jsonData= $result;
    }
    public function setMethodName($methodName)
    {
        $this->modelName = $methodName;
    }

    private function rebuildData()
    {
        if( $this->resConf->dataFormat )
        {
            $this->resData = $this->ato($this->resData);
        }
    }
    //数组转换为对象
    private function ato( $data )
    {
        if(empty($data)) return $data;
        if(is_array ( $data ) )
        {
            foreach ( $data as $key => $val ) {
                if(is_numeric($key) && !isset($ref))
                {
                    $ref= array();
                }elseif(!isset($ref)){
                    $ref = new stdClass();
                    // 					$ref = new $this->modelName();
                }
                if(is_array($ref))
                    $ref[$key] = $this->ato( $val  );
                    else
                        $ref->$key = $this->ato( $val  );
            }
        }else{
            $ref = $data;
        }
        return $ref;
    }
    private function buildResInfo()
    {
        $confRes = $this->conf->getMethodResConf();
        if(check::isNull($confRes))
            $confRes = new resultInfo();

            if( is_null($this->resConf->selectField) && !is_null($confRes->selectField ) )
                $this->resConf->selectField = $confRes->selectField;

                if( is_null($this->resConf->keyField) &&  !is_null( $confRes->keyField ) )
                    $this->resConf->keyField = $confRes->keyField;
                    if( is_null($this->resConf->posTagField) && !is_null($confRes->posTagField) )
                        $this->resConf->posTagField = $confRes->posTagField;

                        if(is_null($this->resConf->keyField) && is_null($this->resConf->selectField))
                        {
                            $this->resConf->keyField = '#';
                            $this->resConf->selectField = self::RES_RESULT;
                        }

    }

    private function unCode()
    {
        if($this->jsonData === false )
        {
            throw new Exception ("Remote Service is shoutDown, Please concat admin!");
        }
        $this->resData = json_decode($this->jsonData,true);
        if( !isset( $this->resData ) )
        {
            // 			throw new Exception("Call remote server Error !");
            $this->resData = $this->jsonData;
            $this->status = false;
        }else{
            $this->_buildControl();
            if(isset($this->resData[self::RES_RESULT]))
                $this->__R = $this->resData[self::RES_RESULT];
                else{
                    $this->__R = $this->resData;
                }
        }
    }
    public function getR($key = null)
    {
        if( !isset( $this->__R ) ) return false;
        if(empty($key))
            return $this->__R;
            else
                $res = AR::map($this->__R,'#',$key);
                if(isset($res) && is_array($res))
                    return $res;
                    else
                        return $res;
    }
    public function getC($key='')
    {
        if(!check::isNull($key ) )
            $c = $this->__C[$key];
            else
                $c= $this->__C;
                return $c;
    }

    /**
     *  从数据中提取控制信息如果有的话 提取玩之后注销结果中的控制信息
     */
    private function _buildControl()
    {
        // 		$controlArr=array('total','page','size','code','error');
        if(is_array($this->resData[self::RES_HEADER]))
        {
            foreach($this->resData[self::RES_HEADER] as $key=>$value)
            {
                $this->__C[$key] = $value;
            }
        }
    }

    public function getCtr($key=null)
    {
        if( isset( $this->__C[$key] ) )
            return  $this->__C[$key];
            else{
                return $this->__C;
            }
    }

    private function filter()
    {
        if($this->resConf->selectField || $this->resConf->keyField )
        {
            $result = AR::map($this->resData,$this->resConf->keyField,$this->resConf->selectField,$this->resConf->posTagField);
        }

        if(isset($result) )
        {
            $this->resData = $result;
        }
    }
    public function get( )
    {
        $this->statusCheck();
        return $this->resData;
    }
    private function statusCheck( )
    {
        $error = $this->getCtr();

        if($this->status === false && $this->format=='json' ) //远端服务器错误
        {
            $errorCode = 40004;
        }elseif(isset($error['errorCode'])){
            $errorCode = $error['errorCode'];
        }elseif($this->format!='json'){
            $errorCode = 20000;
        }
        // 		$errorCode = 40004;
        $ekv = dealError::getErrorConf();  //get error conf
        $errorLang = $ekv['english'];

        //
        // 		de($error,1);
        // 		[currentPageNum] =>
        // 		[errorCode] => 20000
        // 		[errorMsg] => ok
        // 		[pageSize] =>
        // 		[responseCode] => 0
        // 		[resultsSize] => 0
        // 		[totalPageNum] =>

        if($errorCode != 40004 )
            $errorMessage = $error['errorMsg'];
            else{
                $errorMessage = $errorLang[$errorCode];
            }

            if($errorCode!= 20000 && $errorMessage )
            {
                $message = sprintf("Code:%s Message:%s",$errorCode,$errorMessage); //有错误类型错误
            }elseif( $errorCode!= 20000  && isset($errorLang[$errorCode])){
                $message = sprintf("Code:%s Message:%s",$errorCode,$errorLang[$errorCode]); //有错误类型错误
            }elseif($errorCode!= 20000 && !isset($errorLang[$errorCode])){ //无错误类型 错误
                $message = sprintf("Code:%s Message:%s",$errorCode,"Other error!");
            }

            if(!isset($message) ) //如果没有错误信息则返回
            {
                return true;
            }else{
                throw new Exception( $message ,$errorCode);
            }

            // 		if(!empty($_GET['inajax']) && $_GET['inajax']==1)
                // 		{
                // 			dealErrorView::errorAjaxCodeMessage($message);
                // 		}
                // 		else
                    // 		{
                    // 			dealErrorView::innerHtmlExceptionView($message);
                    // 		}
                    }

}
//THIS CLASS IS NOT USE
class dealErrorView
{
    // 	static function innerHtmlExceptionView($message)
    // 	{
    // 		header('Content-Type: text/html; charset=utf-8');
    // 		ob_end_clean();
    // 		echo $message;
    // 		exit;
    // 	}
    // 	static function ajaxCalExceptionView($message)
    // 	{
    // 		header('Content-Type: text/html; charset=utf-8');
    // 		ob_end_clean();
    // 		// 应用于ajax数据的返回
    // 		header("Expires: -1");
    // 		header("Cache-Control: no-store, private, post-check=0, pre-check=0, max-age=0", FALSE);
    // 		header("Pragma: no-cache");
    // 		echo json_encode(array('status'=>0, 'message'=>$message));
    // 		$this->errorLog();
    // 		exit;
    // 	}

    }
    class dealError
    {
        const ERROR_LANG_FILE='components/error_lang.txt';
        const ERROR_LANG_CACHE = 'dao/lang_cache.cache';

        function __construct()
        {
        }
        static public  function getErrorConf()
        {
            $eo = new dealError();
            return $eo->_getErrorConf();
        }
        public function _getErrorConf()
        {
            // 		$basePath =  $_SERVER['DOCUMENT_ROOT'];
            $basePath = dirname(dirname(__FILE__));
            $errorLangFile = "$basePath/".self::ERROR_LANG_FILE;
            $langCacheFile = "$basePath/".self::ERROR_LANG_CACHE;
            //check cache
            if(file_exists($langCacheFile))
            {
                $fcp = fopen($langCacheFile,'r');
                $langCache = fread($fcp, filesize($langCacheFile) );
                fclose($fcp);
            }

            if(file_exists($errorLangFile))
            {
                $fp = fopen($errorLangFile,'r');
                $fstat = fstat($fp);
            }else{
                throw new Exception($errorLangFile .' is not exists!');
            }
            if(isset($langCache))
            {
                $la =  unserialize($langCache);
                if(isset($la['mtime']) && $la['mtime'] == $fstat['mtime'])
                    return $la;
            }
            //缓存过期或者不存在则更新
            $ec = fread($fp, filesize($errorLangFile) );
            fclose($fp);

            $ca = preg_match_all('/(\[([^\n]+)\]|(\d{5})\s([^\n]+))/u',$ec,$matchs);
            $language= $matchs[2];
            $code= $matchs[3];
            $conArr =$matchs[4];
            foreach( $language as $k=>$v)
            {
                if(!empty($v))
                {
                    $v = trim($v);
                    $langName = $v;
                    continue;
                }
                	
                $langArr[$langName][$code[$k]] = $conArr[$k];
                	
            }
            $langArr['mtime'] = $fstat['mtime'];
            $langCache = serialize($langArr);

            $fp = fopen($langCacheFile,'w');
            $langCache = fwrite($fp, $langCache);
            fclose($fp);

            return $langArr;

        }
    }



