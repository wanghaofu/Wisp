<?php
/**
 * example 
 * @author tony
 *
 *  Log::debug('this is an error example',Log::INFO);
 *
 */
class Log{
  
    
    const EMERGENCY = 0;
    const ALERT = 1;
    const CRITICAL = 2;
    const ERROR = 3;
    const WARNING = 4;
    const NOTICE = 5;
    const INFO = 6;
    const DEBUG = 7;
    

    /* {{{ debug函数
     * @param   string  $debugData      debug内容
     * @param   int     $debugLevel     debug级别
     *
     * @return  null
     */
    public  function debug($debugData,$debugLevel) {
        if ($debugLevel>=$GLOBALS['debugLevel'] && !empty($debugData)) {
            $lvDesc=_getDebugDesc($debugLevel);
            /* {{{ data prefix
             */
            $dataPrefix='';
            
            //@todo rewrite
            /**
            if (!empty($GLOBALS['operation'])) {
                $dataPrefix.="[{$GLOBALS['operation']}]";
            }
            if (!empty($GLOBALS['serviceName'])) {
                $dataPrefix.="[{$GLOBALS['serviceName']}]";
            }
            if (!empty($GLOBALS['selector'])) {
                $dataPrefix.="[{$GLOBALS['selector']}]";
            }
            if (!empty($GLOBALS['rowKey'])) {
                $dataPrefix.="[{$GLOBALS['rowKey']}]";
            }
            if (!empty($dataPrefix)) {
                $dataPrefix.=' ';
            }
            **/
    
            $dataSuffix=' ';
            if (!empty($GLOBALS['moduleName'])) {
                $dataSuffix.="[{$GLOBALS['moduleName']}]";
            }
            $dataSuffix.="["._getErrDesc($GLOBALS['errCode'])."({$GLOBALS['errCode']})]";
            if (!empty($GLOBALS['serviceSign'])) {
                $dataSuffix.="[{$GLOBALS['serviceSign']}]";
            }
            $dataSuffix.="["._procSpeed()."][$lvDesc]";
    
            $debugData=$dataPrefix.$debugData.$dataSuffix;
            /* }}} */
            if ($GLOBALS['debugOutput']) {
                printf("[%s]%s<br />\n",date('Y-m-d H:i:s'),$debugData);
            }
            _saveSysLog($debugData,'_debug');
        }
    }
    /* }}} */
    
    
    /* {{{ 获取debug级别
     * @param string $lvStr debug字符串
     */
   private function _getDebugLevel($lvStr='') {
        if (is_numeric($lvStr)) {   //是数字直接返回数字
            return (int)$lvStr;
        } elseif ($lv=constant('_DLV_'.strtoupper($lvStr))) {
            return $lv;
        } else {
            return _DLV_NONE;
        }
    }
    /* }}} */
    
    /* {{{ 日志注册器
     * 将日志配置存入$GLOBALS['sysLog']中,可按照需要增加键指,默认_debug不能占用
     */
    function _syslogRegister($logSettingStr,$logRKey=null,$logTag=null) {
        $logRKey=empty($logRKey)?'_debug':$logRKey; //下划线开头,避免冲突
        $logTag=empty($logTag)?$GLOBALS['logTag']:$logTag;
        $GLOBALS['sysLog'][$logRKey]['tag']=$logTag;
        list($facStr,$priStr)=explode('.',$logSettingStr);
        if ($facility=constant('_FACILITY_'.strtoupper($facStr))) {
            $GLOBALS['sysLog'][$logRKey]['facility']=$facility;
        } else {
            $GLOBALS['sysLog'][$logRKey]['facility']=_FACILITY_DEFAULT;
        }
        if ($priority=constant('_PRIORITY_'.strtoupper($priStr))) {
            $GLOBALS['sysLog'][$logRKey]['priority']=$priority;
        } else {
            $GLOBALS['sysLog'][$logRKey]['priority']=_PRIORITY_DEFAULT;
        }
        return true;
    }
    /* }}} */
    
    /* {{{ debug描述
     * @param $lv int debug级别
     */
    function _getDebugDesc($lv) {
        switch($lv) {
            case _DLV_INFO:
                $ret='INFO';
                break;
            case _DLV_NOTICE:
                $ret='NOTICE';
                break;
            case _DLV_WARNING:
                $ret='WARNING';
                break;
            case _DLV_ERROR:
                $ret='ERROR';
                break;
            case _DLV_CRIT:
                $ret='CRIT';
                break;
            case _DLV_ALERT:
                $ret='ALERT';
                break;
            case _DLV_EMERG:
                $ret='EMERG';
                break;
            case _DLV_NONE:
            default:
                $ret='NONE';
                break;
        }
        return $ret;
    }
    /* }}} */
    
    /* {{{ 记录系统日志
     * @param   string  $data       log内容
     * @param   string  $tag        log标识
     * @param   int     $facility   log分类(与/etc/syslog.conf对应)
     * @param   int     $priority   log级别(与/etc/syslog.conf对应)
     *
     * @return  null
     */
   private  function _saveSysLog($data,$logRKey) {
        do {
            if (empty($data)) {
                _debug("[".__FUNCTION__."][no_data]",_DLV_CRIT);
                break;
            }
            if (false==($logSetting=$GLOBALS['sysLog'][$logRKey])) {
                _debug("[".__FUNCTION__."][logsetting_invalid]",_DLV_CRIT);
                break;
            }
            if (empty($logSetting['tag']) || empty($logSetting['facility']) || empty($logSetting['priority'])) {
                _debug("[".__FUNCTION__."][logsetting_miss]",_DLV_CRIT);
                break;
            }
            openlog($logSetting['tag'],LOG_PID,$logSetting['facility']);
            syslog($logSetting['priority'],$data);
            closelog();
        } while(false);
    }
    /* }}} */
  
    
    /* {{{ 错误描述
     */
   static function _getErrDesc($errCode=false) {
        switch($errCode) {
            case _ERROR_OK:
                $ret='OK';
                break;
            case _ERROR_NOKEY:
                $ret='NoKey';
                break;
            case _ERROR_CONFLICT:
                $ret='Conflict';
                break;
            case _ERROR_DATA:
                $ret='Data';
                break;
            case _ERROR_NOPROPERTY:
                $ret='NoProperty';
                break;
            case _ERROR_NOTEXISTS:
                $ret='NotExists';
                break;
            case _ERROR_FAILED:
                $ret='Failed';
                break;
            case _ERROR_ILLEGALSELECTOR:
                $ret='IllegalSelector';
                break;
            case _ERROR_BADREQUEST:
                $ret='BadRequest';
                break;
            case _ERROR_SIGN:
                $ret='Sign';
                break;
            case _ERROR_TIME:
                $ret='Time';
                break;
            default:
                $ret='Unknown';
                break;
        }
        return $ret;
    }
    /* }}} */
    
    /* {{{ 错误处理函数
     */
    function _checkError($errCode=false) {
        $ret=false;
        switch($errCode) {
            case _ERROR_OK:
                $ret=true;
                break;
            case _ERROR_NOKEY:
            case _ERROR_CONFLICT:
            case _ERROR_DATA:
            case _ERROR_NOPROPERTY:
            case _ERROR_NOTEXISTS:
            case _ERROR_FAILED:
            case _ERROR_ILLEGALSELECTOR:
            case _ERROR_BADREQUEST:
            case _ERROR_SIGN:
            default:
                break;
        }
        _debug("[".__FUNCTION__."][".self::_getErrDesc($errCode)."]",_DLV_NOTICE);
        return $ret;
    }
    /* }}} */
    
}