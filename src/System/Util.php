<?php
namespace Wisp\System;

final class Util
{

    public static /* string */
        function export(/* string */ $expression,
                     /* bool */ $return = FALSE)
    {
        $dump = self::dump($expression);
        if ($return) {
            return $dump;
        }
        print $dump;
    }

    public static /* string */
        function dump($var, $max_depth = 5, $depth = 0)
    {
        if (is_object($var) || is_array($var)) {
            // $class_name;
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

    static function de($str, $track = 0, $exit = false)
    {
        global $debugnum;
        $debugnum ++;
        $debugInfo = debug_backtrace();
        if (php_sapi_name() !== 'cli') {
            $cli = false;
        } else {
            $cli = true;
        }
        if ($cli === false) {
            echo "<div style='font-size:14px;background-color:#f1f6f7'>";
            echo "<div style='font-size:16px;background-color:dfe5e6;color:#001eff;font-weight:bold'>";
            foreach ($debugInfo as $key => $value) {
                if ($key == 0) {
                    echo "*** <span style='font-size:10px'>{$debugnum}</span><span style='font-weight:normal'> {$value['file']}</span>  <span style='font-size:20;color:red'> {$value['line']} </span>(row) </br>";
                } else {
                    if ($track) {
                        echo "&nbsp;&nbsp;<span style='font-size:12px;'>>> include in file:{$value['file']} line:{$value['line']} row </br></span>";
                    } else {
                        break;
                    }
                }
            }
            echo "</div>";
            echo '<pre>';
        } else {
            foreach ($debugInfo as $key => $value) {
                if ($key == 0) {
                    echo "*** {$debugnum} {$value['file']} {$value['line']}(row)  \n";
                } else {
                    if ($track) {
                        echo " include in file:{$value['file']} line:{$value['line']} row \n";
                    } else {
                        break;
                    }
                }
            }
        }
        if (! isset($str)) {
            echo 'the vars in not set!';
        } elseif (is_numeric($str)) {
            echo $str;
        } elseif (is_object($str)) {
            print_r($str);
        } elseif (is_string($str)) {
            echo $str;
        } elseif (is_array($str)) {
            print_r($str);
        } elseif (is_null($str)) {
            echo 'the vars is null ';
        } elseif (is_bool($str)) {
            echo $str;
        }
        if ($cli === false) {
            echo '</pre>';
            echo "</div>";
        }
        if ($exit) {
            exit();
        }
        // }}}
    }
}
