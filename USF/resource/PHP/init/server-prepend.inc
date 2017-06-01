<?php
/** @note This file is actually located in the PHP Directory */
// Dev Log
    define('USF_DEV_LOGGING', false);
    if (USF_DEV_LOGGING) {
        $devLog = new SplFileObject("c:\php7x64\devlog.txt", 'a');
    }
     DevLogWrite(__FILE__ , __LINE__ , 'BLANK');
     DevLogWrite(__FILE__ , __LINE__ , 'Dev Log Started');
//
    if (array_key_exists('argc', $_SERVER)) {
        /** CLI Mode **/
        define ('USF_PHP_MODE', 'cli');
        DevLogWrite(__FILE__ , __LINE__ , 'CLI Mode');
    }
    else {
        /** Server Mode **/
        define ('USF_PHP_MODE', 'server');
        DevLogWrite(__FILE__ , __LINE__ , 'Server Mode');
        $bufferStarted = ob_start();
        $serverPort = 80;
        if (array_key_exists('SERVER_PORT',$_SERVER)) {
            $serverPort = $_SERVER['SERVER_PORT'];
        }
        switch ($serverPort) {
            case 8080:
            DevLogWrite(__FILE__ , __LINE__ , 'Server Port 8080 Active');
                require_once('C:\Users\Peter\Documents\www\USF\resource\PHP\init\usf-prepend.inc');
                break;
            default:
        }
        while (ob_get_level() != 0) {
            ob_end_clean();
        }
    }
// Dev Log Method
    function DevLogWrite($file='', $line='', $message='BLANK', $dump=false) {
        if (USF_DEV_LOGGING) {
            if ($dump) {
                $message = var_export($message, true);
            }
            if (isset($GLOBALS['devLog'])) {
                if ($message !== 'BLANK') {
                    $GLOBALS['devLog']->fwrite(time() . ", $file, $line, $message" . PHP_EOL);
                }
                else {
                    $GLOBALS['devLog']->fwrite('>>>' . PHP_EOL);
                }
            }
        }
        return true;
    }
    