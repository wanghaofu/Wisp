<?php
namespace Wisp\System;

/**
 * notice this is for debug !
 *
 * @author wangtao
 *        
 */
class Slog
{

    static $debug = false;

    const LOG_SKIP = 'skip';

    const LOG_INFO = 'info';

    const LOG_DEBUG = 'debug';

    const LOG_ERROR = 'error';

    static $log = null;

    static $count = 0;

    static $fp = null;

    static $self = null;

    static $traceStartFun = [
        'debug',
        'info',
        'notice',
        'warning',
        'error',
        'critical',
        'alert',
        'emergency'
    ];

    static $msg_pre = '';

    var $strace = false;

    public static function instance()
    {
        if (empty(self::$log))
            self::$log = CoreFActory::instance()->logger();
        
        if (empty(self::$self)) {
            self::$self = new Static();
        }
        return self::$self;
    }

    /**
     * @deacription this is import
     * 
     * @param mixed $messsage
     *            $e $array and other ! 需要启动特定追踪机制
     */
    public static function log($message = null, $operator = self::LOG_INFO, $level = null, $trace = true)
    {
        if (empty(self::$log))
            self::$log = CoreFActory::instance()->logger();
        
        $log = self::$log;
        $modules = 'commission';
        $params = [
            'modules' => $modules,
            'operator' => $operator
        ];
        if ($operator) {
            $file = sprintf("%s_%s.log", $modules, self::LOG_DEBUG);
        }
        
        if (Environment::isProduction() === false) {
            $trace = true;
            self::$debug = true;
        } else {
            $trace = false;
            self::$debug = false;
        }
        
        $message = slog::format($message, null, $trace);
        
        $message = sprintf("%s#%s: %s", self::$msg_pre, $operator, $message);
        
        if (self::$debug == false) {
            switch ($operator) {
                case self::LOG_SKIP:
                case self::LOG_INFO:
                    $log->info($message, $params);
                    break;
                case self::LOG_ERROR:
                    $log->error($message, $params);
                    break;
            }
        }
        if (self::$debug == true) {
            $log->info($message, $params);
            self::__log($message, $file);
        }
    }

    public function setMsgPre($msgPre)
    {
        self::$msg_pre = $msgPre;
        return $this;
    }

    public function debug($message, array $context = array())
    {
        self::log($message, self::LOG_DEBUG);
    }

    public function info($message, array $context = array())
    {
        self::log($message, self::LOG_INFO);
    }

    public function error($message, array $context = array())
    {
        self::log($message, self::LOG_ERROR);
    }

    public function strace()
    {
        $this->strace = true;
    }
    

    public function syslog($message)
    {
        define_syslog_variables();
        openlog("TextLog", LOG_PID, LOG_LOCAL2);
        
        $data = date("Y/m/d H:i:s");
        syslog(LOG_DEBUG,"Messagge: $message");
        
        closelog();
    }
    
    // This is only for debug of commission
    private static function __log($message, $fileName, $path = null)
    {
        if (! is_dir($path)) {
            @mkdir($path, 0777, true);
        }else{
            
        }
        $file = sprintf('%s/%s', $path, $fileName);
        
        if (! self::$fp)
            self::$fp = fopen($file, 'a+');
        
        if (self::$fp) {
            if (fwrite(self::$fp, $message) === FALSE) {}
        }
    }

    public function __construct()
    {
        if (self::$fp)
            fclose(self::$fp);
    }

    public static function format($input, $level = null, $addTrace = true)
    {
        // $format_line = '%1$s [%2$s] %3$s';
        $format_line = '%1$s  %2$s';
        $format_time = '%Y-%m-%d %H:%M:%S';
        $out_message = self::extract_message($input); // message
        if ($addTrace !== FALSE) {
            $out_message .= ' ' . self::get_backtrace_as_string($input); // add trace
        }
        $out_message = sprintf($format_line, strftime($format_time), $out_message);
        return $out_message;
    }

    protected static final function extract_message($input)
    {
        $output_str = '';
        switch (gettype($input)) {
            case 'array':
                $output_str = self::export($input, TRUE);
                break;
            case 'object':
                if (method_exists($input, 'getMessage')) {
                    $output_str = $input->getMessage();
                } else 
                    if (method_exists($input, 'toString')) {
                        $output_str = $input->toString();
                    } else 
                        if (method_exists($input, '__tostring')) {
                            $output_str = (string) $input;
                        } else {
                            $output_str = self::export($input, TRUE);
                        }
                
                if (method_exists($input, 'getFile')) {
                    $file = $input->getFile();
                }
                if (method_exists($input, 'getLine')) {
                    $line = $input->getLine();
                }
                if (isset($file) && isset($line))
                    $output_str = sprintf("%s %s(%d)", $output_str, $file, $line);
                
                break;
            default:
                $output_str = (string) $input;
                break;
        }
        return $output_str;
    }

    /**
     * r 定位错误位置
     * 
     * @param
     *            $input
     * @return string
     */
    protected static final function get_backtrace_as_string($input)
    {
        $bt = NULL;
        if (is_object($input)) {
            if (method_exists($input, 'getTrace')) {
                $bt = $input->getTrace();
            } else 
                if (method_exists($input, 'getBackTrace')) {
                    $bt = $input->getBackTrace();
                }
        }
        if ($bt === NULL) {
            $bt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        }
        $bt_str = '';
        $trace = false;
        foreach ($bt as $frame => $var) {
            $class = isset($var['class']) ? $var['class'] : '';
            $func = isset($var['function']) ? $var['function'] : '';
            
            // 通过函数名来，来确认log起始点,索引加1 表示调用日志输出的位置
            if ($trace == false && in_array($func, self::$traceStartFun) === true) {
                
                $pos = strrpos($class, '\\');
                if ($pos !== false) {
                    $className = substr($class, $pos + 1);
                } else {
                    $className = $class;
                }
                
                if ($className === 'Slog')
                    $trace = true;
            }
            
            if ($trace === false)
                continue;
                
                // 日志写入点的下一条信息就是 日志写入位置
            $func = isset($bt[$frame + 1]['function']) ? $bt[$frame + 1]['function'] : '';
            $class = isset($bt[$frame + 1]['class']) ? $bt[$frame + 1]['class'] : '';
            $file = isset($var['file']) ? $var['file'] : 'built-in function';
            $line = isset($var['line']) ? $var['line'] : '-';
            $text = '(pid:%s) %s::%s(%d) (%d) ';
            self::$count ++;
            $text = sprintf($text, getmypid(), $class, $func, $line, self::$count);
            if (strlen($bt_str) !== 0) {
                $bt_str .= "\n";
            }
            $bt_str .= $text;
            // 输出一条后 跳出 不再进行追踪
            if ($trace = true) {
                break;
            }
        }
        if (empty($bt_str)) {
            return false;
        } else {
            return sprintf("< %s >\n", $bt_str);
        }
    }

    public static function export($expression, $return = FALSE)
    {
        $dump = self::dump($expression);
        if ($return) {
            return $dump;
        }
        print $dump;
    }

    public static function dump($var, $max_depth = 5, $depth = 0)
    {
        if (is_object($var) || is_array($var)) {
            if (is_object($var)) {
                $class_name = get_class($var);
                $var = (array) $var;
            } else {
                $class_name = "array";
            }
            $s = "$class_name(\n";
            if (++ $depth > $max_depth) {
                $s .= str_repeat('    ', $depth) . "?";
            } else {
                $flatten = array();
                foreach ($var as $k => $v) {
                    $flatten[] = str_repeat('    ', $depth) . "$k: " . self::dump($v, $max_depth, $depth);
                }
                $s .= implode(",\n", $flatten);
            }
            $s .= "\n" . str_repeat('    ', $depth - 1) . ")";
            return $s;
        }
        return "$var";
    }
}