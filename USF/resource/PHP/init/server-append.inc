<?php
/** @note This file is actually located in the PHP Directory */
    if (array_key_exists('argc', $_SERVER)) {
        /** CLI Mode **/
    }
    else {
        /** Server Mode **/
        $serverPort = 80;
        if (array_key_exists('SERVER_PORT',$_SERVER)) {
            $serverPort = $_SERVER['SERVER_PORT'];
        }
        switch ($serverPort) {
            case 8080:
                require_once('C:\Users\Peter\Documents\www\USF\resource\PHP\init\usf-append.inc');
                break;
            default:
        }
    }
    if (isset($devLog)) {
        DevLogWrite(__FILE__ , __LINE__ , 'Log  Closed');
        $devLog = null;
    }
