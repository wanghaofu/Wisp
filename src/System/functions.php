<?php
/**
 * Created by PhpStorm.
 * User: tony
 * Date: 16/8/5
 * Time: 17:15
 */

use Wisp\Db\Cascade;

const SYNC_INSERT = 'si';
const SYNC_UPDATE = 'su';
const SYNC_DELETE = 'sd';

const SYNC_LIST = 'slist';
const SYNC_PK= 'spk';


function cascade( $mix = null )
{
    if ( true === isset( $mix ) && is_string( $mix ) ) {
        $mix = new $mix;
    }
    return new  Cascade( $mix );
}

function de( $str, $track = 0, $exit = false )
{
    global $debugnum;
    $debugnum++;
    $debugInfo = debug_backtrace();
    if ( php_sapi_name() !== 'cli' ) {
        $cli = false;
    } else {
        $cli = true;
    }
    if ( $cli === false ) {
        echo "<div style='font-size:14px;background-color:#f1f6f7'>";
        echo "<div style='font-size:16px;background-color:#dfe5e6;color:#001eff;font-weight:bold'>";
        foreach ( $debugInfo as $key => $value ) {
            if ( $key == 0 ) {
                echo "*** <span style='font-size:10px;font-weight:bolder'>{$debugnum}</span> <span style='font-weight:bolder;'> {$value['file']}</span>  <span style='font-size:20;color:red'> {$value['line']} </span>(row) </br>";
            } else {
                if ( $track ) {
                    $file = isset( $value[ 'file' ] ) ? $value[ 'file' ] : serialize( $value );
                    $line = isset( $value[ 'line' ] ) ? $value[ 'line' ] : '';

                    echo "&nbsp;&nbsp;<span style='font-size:12px;'>>> include in file:{$file} line:{$line} row </br></span>";
                } else {
                    break;
                }
            }
        }
        echo "</div>";
        echo '<pre>';
    } else {
        foreach ( $debugInfo as $key => $value ) {
            if ( $key == 0 ) {
                echo "*** {$debugnum} {$value['file']} {$value['line']}(row)  \n";
            } else {
                if ( $track ) {
                    echo " include in file:{$value['file']} line:{$value['line']} row \n";
                } else {
                    break;
                }
            }
        }
    }
    if ( !isset( $str ) ) {
        echo 'the vars in not set!';
    } elseif ( is_numeric( $str ) ) {
        echo $str;
    } elseif ( is_object( $str ) ) {
        print_r( $str );
    } elseif ( is_string( $str ) ) {
        echo $str;
    } elseif ( is_array( $str ) ) {
        print_r( $str );
    } elseif ( is_null( $str ) ) {
        echo 'the vars is null ';
    } elseif ( is_bool( $str ) ) {
        echo $str;
    }
    if ( $cli === false ) {
        echo '</pre>';
        echo "</div>";
    } else {
        echo PHP_EOL;
    }
    if ( $exit ) {
        exit();
    }
    // }}}
}