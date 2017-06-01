<?php

/* Automated USF Test Script for Logger.Class
 * 
 * @TODO
 * Develop this for CLI, use arguments to invoke all, selected or suite of tests.
 * Have verbose and non verbose modes -i.e. write a "test result array"
 * Sharing tests could be invoked by multiple instances of a particular suite of
 * (or complete) tests.
 * 
 * 
 * @NOTE The do / while structure is purely to allow code folding in the editor.
 * As a benefit it helpd encapsulate and enforce Scope. At the moment most tests
 * can be run in isolation; at some point (aided by copious use of sapgetti code
 * inherent with the extensive use of Goto) there will be some form of test
 * sequence and management. The objective here is clear visibilty of the tests
 * not "pretty code".
 */
    ob_start();
    define('USF_TESTING_SCRIPT', 'Logger');
    $testOut = "===USF Test Script for Logger Class===" . PHP_EOL;
    $resourcesRequired = array(
        'Server Constants' => 'C:/Users/Peter/Documents/www/Server/resource/PHP/init/usf-constants.inc',
        'Test Script Methods' => 'C:/Users/Peter/Documents/www/Server/resource/PHP/test-scripts/test-methods.inc',
    );
    $usedConstants = array(
        'USF_LOG_DIRECTORY_PHP',
        'USF_PHP_MODE',
        'USF_RL_DEBUG',
        'USF_RL_DEFAULT_LOGFILE',
        'USF_RL_DEFAULT_REPORTING_LEVEL',
        'USF_RL_E_CAUTION',
        'USF_RL_E_ERROR',
        'USF_RL_E_FATAL',
        'USF_RL_E_WARNING',
        'USF_RL_ERROR_BITMASK',
        'USF_RL_NONE',
        'USF_RL_S_INFO',
        'USF_RL_S_NOTICE',
        'USF_RL_S_STATUS',
        'USF_RL_S_SYSTEM',
        'USF_RL_SYSTEM_BITMASK',
        'USF_TESTING_SCRIPT',
    );
    foreach ($resourcesRequired as $resource => $location) {
        $resourceLoaded = @(include_once($location));
        if (!$resourceLoaded) {
            $lastError = error_get_last();
            $resultsArray = array(
                'initialisation' => array(
                    'resources' => array(
                        'pass' => false,
                        'message' => 'Failed to load Resource [' . $resource . ']: '. $lastError['message'],
                    ),
                ),
            );
            goto testsFail;
        }
    }
    $undefined = '';
    foreach ($usedConstants as $thisConstant) {
        if (!defined($thisConstant)) {
            $undefined .= $thisConstant . ', ';
        }
    }
    if (!empty($undefined)) {
        $resultsArray = array(
            'initialisation' => array(
                'constants' => array(
                    'pass' => false,
                    'message' => 'Constants ' . $undefined . ' are not defined.',
                ),
            ),
        );
        goto testsFail;
    }

    $scriptToTest = USF_PHP_CLASS_DIRECTORY . USF_TESTING_SCRIPT .'.class';
    $microTime = explode(' ', microtime());
    $startTime = substr($microTime[0], 2);
    $timeNow = $microTime[1];
    $resultsArray = array(
        'constructor' => array(
            'include' => array(
                'pass' => 'meh',
                'message' => 'Not Tested',
            ),
            'new instance' => array(
                'pass' => 'meh',
                'message' => 'Not Tested',
            ),
            'initial values' => array(
                'pass' => 'meh',
                'message' => 'Not Tested',
            ),
            'bad file' => array(
                'pass' => 'meh',
                'message' => 'Not Tested',
            ),
            'default file' => array(
                'pass' => 'meh',
                'message' => 'Not Tested',
            ),
        ),
        'methods' => array(
            'associate session' => array(
                'pass' => 'meh',
                'message' => 'Not Tested',
            ),
        ),
    );

/** Test Group: constructor - Loading and Instansiation Tests */
    $testGroup = 'constructor';
/** Include File Test: See if the class definition script can be included */
    do {
        $thisTest = 'include';
        $pass = false;
        $message = 'Cannot find script to test: ' . $scriptToTest;
        if (file_exists($scriptToTest)) {
            /**
             * NOTE: This is not fool proof! Essentially this script is being
             * 'run'. It is not going to find a 'logic' error in defined but
             * uncalled methods etc. But it should pick up syntax errors and
             * malformed code.
             */
            @(exec('php -l ' . $scriptToTest, $output, $return));
            if ($return == 0) {
                $includedFiles = get_included_files();
                if (in_array($scriptToTest, $includedFiles)) {
                    $pass = true;
                    $message = 'CAUTION: Script to test (' . $scriptToTest . ') already included';
                }
                else {
                    $shouldBe = 1;
                    $setAt = @(include($scriptToTest));
                    if ($shouldBe === $setAt) {
                        $pass = true;
                        $message = '';
                        unset($shouldBe, $setAt);
                    }
                    else {
                        $lastError = error_get_last();
                        $message = 'Failed to load Test Candidate [' . $scriptToTest . ']: ' . $lastError['message'];
                    }
                }
            }
            else {
                $message = $output;
            }
        }
        if (!RecordTestResults($pass, $message)) {goto testsFail;}
    } while (!$pass);
/** Create New Instance: Can we create a new instance of the Logger class */
    do {
        $thisTest = 'new instance';
        $pass = false;
        $testLog = new Logger('testLog');
        $message = 'Unable to create new instance of Logger Class.';
        /** This is seriously expected NOT to fail, but it could */
        if (is_object($testLog)) {
            $shouldBe = USF_LOG_DIRECTORY_PHP;
            $setAt = $testLog->PropertyValue('logFilePath');
            $message = 'Log File Path is: ' . $setAt . ' It should be: ' . $shouldBe;
            /** The log file path should be assigned to the constant */
            if ($shouldBe == $setAt) {
                $shouldBe = 'resource';
                $setAt = $testLog->PropertyValue('logFilePointer');
                $message = 'No associated Resource (Log File) for Logger Instance.';
                /** Current version of Logger will throw an exception if no resource */
                if ($shouldBe == gettype($setAt)) {
                    $pass = true;
                    $message = '';
                }
            }
        }
        if (!RecordTestResults($pass, $message)) {goto testsFail;}
    } while (!$pass);
/** Create Bad Instance: A new Logger with a bad file name. It should throw an exception */
    do {
        unset($testLog);
        $thisTest = 'bad file';
        $pass = false;
        $message = 'Unexpected continuation of Logger Instance';
        /**
         * Essentially we are testing what happens if a file resource cannot
         * be created; i.e. the class would throw an exception. At some future
         * point this type of response would be handled more gracefully.
         */
        try {
            $testLog = new Logger('testLog&$/');
        } catch (Exception $ex) {
            $pass = true;
            $message = $ex->getMessage();
        }
        if (!RecordTestResults($pass, $message)) {goto testsFail;}
    } while (!$pass);
/** Initial Property Values: Can the newly created object be as we expected */
    do {
        if (!empty(session_id())) {
            session_unset();
            session_destroy();
        }
        unset($testLog);
        $testLog = new Logger('testLog');
        $thisTest = 'initial values';
        $pass = false;
        $shouldBe = USF_RL_DEFAULT_REPORTING_LEVEL;
        $setAt = $testLog->PropertyValue('defaultReporting');
        $message = 'Default Reporting Level is: ' . $setAt . ' It should be: ' . $shouldBe;
        /** The default reporting level should be assigned */
        if ($shouldBe == $setAt) {
            $setAt = $testLog->PropertyValue('reportingLevel');
            $message = 'Reporting Level is: ' . StringifyVariable($setAt) . ' It should be [default value]: ' . $shouldBe;
            /**
             * The actual reporting level should be the default level. We
             * will test the actual validity of it later.
             */
            if ($shouldBe == $setAt) {
                $setAt = $testLog->PropertyValue('logId');
                $message = 'Log ID [type] is: ' . StringifyVariable($setAt) . ' It should be [type] numeric: ' . $startTime;
                /**
                 * Because there is no PHP Session, the Log Id should be
                 * based on the time that this instance of Logger was created
                 * and should therefore be numeric.
                 */
                if (gettype($setAt) === getType($startTime)) {
                    $message = 'Log ID is: ' . StringifyVariable($setAt) . ' It should be (approximately): ' . $startTime;
                    /**
                     * The Log Id should also be (slightly) less that the
                     * time this script started running
                     */
                    if ($setAt > $startTime) {
                        $pass = true;
                        $message = '';
                    }
                }
            }
        }
        if (!RecordTestResults($pass, $message)) {goto testsFail;}
    } while (!$pass);
/** Default Log File: See if we can log to the default log file */
    do {
        unset($testLog);
        $testLog = new Logger();
        $thisTest = 'default file';
        $pass = false;
        $shouldBe = USF_RL_DEFAULT_LOGFILE;
        $setAt = $testLog->PropertyValue('logFileName');
        $message = 'Default Log File Name is: ' . $setAt . ' It should be: ' . $shouldBe;
        /** The default log file name should be assigned */
        if ($shouldBe == $setAt) {
            $pass = true;
            $message = '';
        }
        if (!RecordTestResults($pass, $message)) {goto testsFail;}
    } while (!$pass);
/** File Opening Mode: The log file should opened in a "safe" append mode */
    do {
        unset($testLog);
        $testLog = new Logger();
        $thisTest = 'file opening';
        $pass = false;
        clearstatcache();
        $fileInfo = stream_get_meta_data($testLog->PropertyValue('logFilePointer'));
        $shouldBe = 'cb';
        $setAt = $fileInfo['mode'];
        $message = 'Log file open mode is: ' . $setAt . ' It should be: ' . $shouldBe;
        /** The Log File fopen() Mode should be 'cb' */
        if ($shouldBe == $setAt) {
            $pass = true;
            $message = '';
        }
        if (!RecordTestResults($pass, $message)) {goto testsFail;}
    } while (!$pass);
/** Test Group: methods - Discrete Method Tests */
    $testGroup = 'methods';
/** AssociateSession(): Set the PHP Session Id as the Log Id for "this" log */
    do {
        $thisTest = 'associate session';
        $pass = false;
        $shouldBe = false;
        $setAt = $testLog->AssociateSession();
        $message = 'PHP Session appears to have already started';
        /**
         * If the PHP Session has not started, calling this method should return
         * FALSE. A fail here is more of a scripting issue as any previous PHP
         * Session should have been destroyed by now.
         */
        if ($shouldBe === $setAt) {
            /**
             * We are now going to start the PHP Session. There should not be
             * issues with that...
             */
            $shouldBe = true;
            $placeHolder = '';
            $message = 'Unable to start PHP Session: ' . $placeHolder;
            $setAt = session_start();
            if ($shouldBe === $setAt) {
                /**
                 * With the PHP Session started, calling the method should reset
                 * the Log ID to that of the PHP Session Id.
                 */
                $testLog->AssociateSession();
                $shouldBe = session_id();
                $setAt = $testLog->PropertyValue('logId');
                $message = 'Log ID is: ' . $setAt . ' It should be: ' . $shouldBe;
                if ($shouldBe == $setAt) {
                    /** 
                     * Calling this method a second (or more) time after the PHP
                     * Session has started should always return true.
                     * Unless, the PHP Session ID has changed... somehow...
                     */
                    $shouldBe = true;
                    $setAt = $testLog->AssociateSession();
                    $message = 'Result is: ' . $setAt . ' It should be: ' . $shouldBe;;
                    if ($shouldBe === $setAt) {
                        $pass = true;
                        $message = '';
                    }
                }
            }
            else {
                $lastError = error_get_last();
                $placeHolder = $lastError['message'];
            }
        }
        if (!RecordTestResults($pass, $message)) {goto testsFail;}
    } while (!$pass);
/** GetReportingLevels(): Read back the current reporting levels */
    do {
        $thisTest = 'reporting levels';
        $pass = false;
        $setReportingLevels = $testLog->GetReportingLevels();
        $shouldBe = bindec(USF_RL_DEFAULT_REPORTING_LEVEL);
        $setAt = $setReportingLevels['current'];
        $message = 'Reporting Level is: ' . $setAt . ' It should be: ' . $shouldBe;
        /** Just Checking we are at the default reporting level */
        if ($shouldBe == $setAt) {
            $shouldBe = bindec(USF_RL_DEFAULT_ERROR_LEVEL);
            $setAt = $setReportingLevels['error'];
            $message = 'Error Reporting Level is: ' . $setAt . ' It should be: ' . $shouldBe;
            /** Just Checking we are at the default error reporting level */
            if ($shouldBe == $setAt) {
                $shouldBe = bindec(USF_RL_DEFAULT_SYSTEM_LEVEL);
                $setAt = $setReportingLevels['system'];
                $message = 'System Reporting Level is: ' . $setAt . ' It should be: ' . $shouldBe;
                /** Just Checking we are at the default system reporting level */
                if ($shouldBe == $setAt) {
                    $testLog->PropertyValue('reportingLevel', '10101010', true);
                    $shouldBe = Array('current' => 170, 'error' => 10, 'system' => 160,);
                    $setAt = $testLog->GetReportingLevels();
                    $message = 'Bitmask Test 1 result is: ' . implode(',', array_values($setAt)) . ' It should be: ' . implode(',', array_values($shouldBe));
                    /** Bitmask operation test 1 */
                    if ($shouldBe == $setAt) {
                        $testLog->PropertyValue('reportingLevel', '01010101', true);
                        $shouldBe = Array('current' => 85, 'error' => 5, 'system' => 80,);
                        $setAt = $testLog->GetReportingLevels();
                        $message = 'Bitmask Test 2 result is: ' . implode(',', array_values($setAt)) . ' It should be: ' . implode(',', array_values($shouldBe));
                        /** Bitmask operation test 2 */
                        if ($shouldBe == $setAt) {
                            $pass = true;
                            $message = '';
                        }
                    }
                }
            }
        }
        if (!RecordTestResults($pass, $message)) {goto testsFail;}
    } while (!$pass);
/** ValidateReportingLevel(): Ensure only valid reporting levels are set */
    do {
        $thisTest = 'valid level';
        $pass = false;
        $shouldBe = false;
        $result = $testLog->PrivateMethod('ValidateReportingLevel', 'some text');
        $setAt = $result[0];
//echo "Test Result\n";
//die(var_dump($setAt));
        $message = 'Validation is: ' . StringifyVariable($setAt) . ' It should be: ' . StringifyVariable($shouldBe);
        /** Try a non numeric value */
        if ($shouldBe == $setAt) {
            $pass = true;
            $message = '';
        }
        if (!RecordTestResults($pass, $message)) {goto testsFail;}
    } while (!$pass);

    
    
    /** SetLogging(): Switch Logging On and Off */
    do {
        $thisTest = 'set logging';
        $pass = false;
        $testLog->SetLogging('off');
        $shouldBe = false;
        $setAt = $testLog->WriteLine('Switching Logging Off', 'SYSTEM');
        $message = 'Not Tested';
        /** Check Logging Level Shorcuts */
        
        if (!RecordTestResults($pass, $message)) {goto testsFail;}
    } while (!$pass);

    /**
     * YAY! Concurrent log access
     *
        $concurrentLog1 = $testLog->PrivateMethod('__construct');
        $concurrentLog1->PropertyValue('logId', 'concurrentLog1', true);
        $concurrentLog2 = $testLog->PrivateMethod('__construct');
        $concurrentLog2->PropertyValue('logId', 'concurrentLog2', true);
        $concurrentLog3 = $testLog->PrivateMethod('__construct');
        $concurrentLog3->PropertyValue('logId', 'concurrentLog3', true);
        $concurrentLog4 = $testLog->PrivateMethod('__construct');
        $concurrentLog4->PropertyValue('logId', 'concurrentLog4', true);

     */

/*        $usfLog = new Logger('usfLog');

        $testLogFile = @(fopen("c:\\php7x64\\test", 'cb'));
    $result = $usfLog->PropertyValues('logFileName','test',true);
    $result = $usfLog->PropertyValues('logFilePath','c:\php7x64',true);
    $result = $usfLog->PropertyValues('logFilePointer');
var_dump($result);
    $result = $usfLog->PropertyValues('logFilePointer',$testLogFile,true);
    $result = $usfLog->PropertyValues('logFilePointer');
var_dump($result);
    $result = $usfLog->PropertyValues('reportingLevelMap');
var_dump($result);
    $result = $usfLog->PropertyValues('levelStackLimit');
var_dump($result);
    //$result = $usfLog->WriteLine('Just checking');
    die(var_dump($usfLog));
*/
/** Reporting Level Tests */
    //$testGroup = 'reporting';
    //unset($testLog);
    //$testLog = new Logger('testLog');
    //$testLog->WriteLine('Reporting Level Test');
    /*protected $reportingLevelMap = array(
        USF_RL_NONE      => 'none',
        USF_RL_E_FATAL   => 'FATAL',
        USF_RL_E_ERROR   => 'ERROR',
        USF_RL_E_WARNING => 'WARNING',
        USF_RL_E_CAUTION => 'CAUTION',
        USF_RL_S_SYSTEM  => 'SYSTEM',
        USF_RL_S_STATUS  => 'STATUS',
        USF_RL_S_INFO    => 'INFO',
        USF_RL_S_NOTICE  => 'NOTICE',
        USF_RL_DEBUG     => 'DEBUG',
    );*/
    //$currentState = print_r($testLog);
    //$testLog->WriteLine('Current State:' .PHP_EOL . $currentState);
    /*
        $defaultReporting = $testLog->PropertyValues('defaultReporting');
    $reportingLevel = $testLog->PropertyValues('reportingLevel');
    $maxReportingLevel = ('maxReportingLevel');
    //private $reportingLevelStack = array();
    $levelStackLimit = $testLog->PropertyValues('levelStackLimit');
    */

    
// Set Error Reporting Level
    //$testLog->WriteLine('Set Error Reporting Levels');
    
testsEnd:
    $testOut .= "===Tests Complete===" . PHP_EOL;
    goto testReport;
testsFail:
    $testOut .= '===Testing Aborted===' . PHP_EOL;
    goto testReport;
testReport:
    foreach ($resultsArray as $testGroup=>$tests) {
        $testOut .= 'Test Group::' . $testGroup . PHP_EOL;
        foreach ($tests as $subTest=>$testResult) {
            if ($testResult['pass'] === true) {
                $testResult['pass'] = 'PASS';
            }
            elseif ($testResult['pass'] === false) {
                $testResult['pass'] = 'FAIL';
            }
            else {
                $testResult['pass'] = $testResult['pass'];
            }
            if (is_array($testResult['message'])) {
                $testResult['message'] = trim(implode(PHP_EOL, $testResult['message']));
            }
            $testOut .= "\t" . $subTest . '::' . trim(implode('::', $testResult)) . PHP_EOL;
        }
    }
    echo $testOut;
    ob_end_flush();
    die();
