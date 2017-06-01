<?php
    /**
     * SERVER Wide CONSTANTS
     */
        define('SERVER_SELF_URI_ALIAS', array($_SERVER['SERVER_NAME'], $_SERVER['HTTP_HOST'], 'http://loosepages.co.uk')); //@todo improve on this
        define('USF_SERVER_DIRECTORY_PHP', 'C:/Users/Peter/Documents/www/USF/');
        define('USF_RESOURCE_DIRECTORY_PHP', USF_SERVER_DIRECTORY_PHP . 'resource/');
        define('USF_CODE_DIRECTORY_PHP', USF_RESOURCE_DIRECTORY_PHP . 'PHP/');
        define('USF_SITE_CONFIG_DIRECTORY_PHP', USF_RESOURCE_DIRECTORY_PHP . 'site-init/');
        define('USF_WEB_ROOT_DIRECTORY_PHP', USF_SERVER_DIRECTORY_PHP . 'www/');
        define('USF_ROOT_DIRECTORY_WEB', '/');
        define('USF_TITLE', 'usf');

     /** From Configuraton File **/
     $serverWideConstants = array(
        'USF_MYSQL_HOST_IP'           => '127.0.0.1',
        'PHP_SESSION_NAME'            => ini_get('session.name'),
        'USF_LOG_DIRECTORY_PHP'       => USF_RESOURCE_DIRECTORY_PHP . 'logs/',
        'USF_PHP_INIT_DIRECTORY'      => USF_CODE_DIRECTORY_PHP . 'init/',
        'USF_PHP_CLASS_DIRECTORY'     => USF_CODE_DIRECTORY_PHP . 'classes/',
        'USF_HTML_TEMPLATE_DIRECTORY' => USF_CODE_DIRECTORY_PHP . 'templates/',
        /* USF Session Log */
        'USF_RL_DEFAULT_LOGFILE'       => 'scratch',
        'USF_RL_ERROR_BITMASK'         => sprintf('%08b', 15), // 15
        'USF_RL_SYSTEM_BITMASK'        => sprintf('%08b', 240), // 240
        /* USF Session Log Reporting Levels */
        'USF_RL_NONE'      => 0,   //No Event Logging
        'USF_RL_E_NONE'    => 0,   //No "error" Event Logging
        'USF_RL_S_NONE'    => 0,   //No "system" Event Logging
        'USF_RL_E_FATAL'   => 1,   //Unrecoverable USF errors requiring termination
        'USF_RL_E_ERROR'   => 3,   //Recoverable USF errors
        'USF_RL_E_WARNING' => 7,   //USF errors that may later cause an USF_RL_E_ERROR
        'USF_RL_E_CAUTION' => 15,  //USF cautions - typically default selections
        'USF_RL_E_DEBUG'   => 15,  //All "error" Events,
        'USF_RL_S_SYSTEM'  => 16,  //USF system messages (waypoint markers)
        'USF_RL_S_STATUS'  => 32,  //USF status messages (action results)
        'USF_RL_S_INFO'    => 64,  //USF audit messages
        'USF_RL_S_NOTICE'  => 128, //USF developer "notes"
        'USF_RL_S_DEBUG'   => 240, //All  "system" Events
        'USF_RL_DEBUG'     => 255, //All Events

         'DESTROY_SESSION_DIRECT_ACCESS' => '/destroy.html', // URI to directly destroy current PHP / USF Session.
         'USF_FILELOCK_SLEEP_PERIOD' => 3,  //Number of seconds to "sleep" before attempting to accquire a file lock.
         'USF_FILELOCK_RETRY_COUNT'  => 2,  //Number of times to attempt to accquire a file lock.
     );
     foreach ($serverWideConstants as $key => $value) {
         $thisDefinitionString = 'define($key, "$value");';
         eval($thisDefinitionString);
     }

     /** From Enviroment **/

     /** Dynamic Run Time **/
     define('USF_RL_DEFAULT_ERROR_LEVEL', sprintf("%08d", decbin(USF_RL_E_WARNING)));
     define('USF_RL_DEFAULT_SYSTEM_LEVEL', sprintf("%08d", decbin(USF_RL_S_SYSTEM)));
     define('USF_RL_DEFAULT_REPORTING_LEVEL', (USF_RL_DEFAULT_ERROR_LEVEL | USF_RL_DEFAULT_SYSTEM_LEVEL));
     define('SERVER_REQUEST_UTC', microtime());
