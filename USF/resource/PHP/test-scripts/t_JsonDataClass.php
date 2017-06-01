<?php
/* Automated USF Test Script for UsfMethod.Class
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
    define('USF_TESTING_SCRIPT', 'JsonData');
    define('USF_TEST_LOCATION', dirname(__FILE__) . DIRECTORY_SEPARATOR);
    $testOut = '===USF Test Script for ' . USF_TESTING_SCRIPT . ' Class===' . PHP_EOL;
    $resourcesRequired = array(
        'Server Constants' => 'C:/Users/Peter/Documents/www/Server/resource/PHP/init/usf-constants.inc',
        'Test Script Methods' => 'C:/Users/Peter/Documents/www/Server/resource/PHP/test-scripts/test-methods.inc',
        'USF Common Methods' => 'C:/Users/Peter/Documents/www/Server/resource/PHP/classes/UsfMethod.class',
        'USF File Access' => 'C:/Users/Peter/Documents/www/Server/resource/PHP/classes/FileAccess.class',
    );
    $usedConstants = array(
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

    $usfMethods = UsfMethod::GetInstance();
    
    $scriptToTest = USF_PHP_CLASS_DIRECTORY . USF_TESTING_SCRIPT .'.class';
    $microTime = explode(' ', microtime());
    $startTime = $microTime[1] . '.00000000';
    $timeNow = $microTime[1];
    $resultsArray = array(
        'instantiation' => array(
            'include' => array(
                'pass' => 'meh',
                'message' => 'Not Tested',
            ),
            'create instance' => array(
                'pass' => 'meh',
                'message' => 'Not Tested',
            ),
        ),
        'methods' => array(),
        'functional' => array(),
    );

/** Test Group: constructor - Loading and Instansiation Tests */
    $testGroup = 'instantiation';
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
/** Create New Instance: Can we create a new instance of this class */
    do {
        $thisTest = 'create instance';
        $pass = false;
        eval("\$loadedClass = " . USF_TESTING_SCRIPT . "::GetInstance();");
        $message = 'Unable to create new instance of ' . USF_TESTING_SCRIPT . ' Class.';
        /** This is seriously expected NOT to fail, but it could */
        if (is_object($loadedClass)) {
            $pass = true;
            $message = '';
            unset($loadedClass);
        }
        if (!RecordTestResults($pass, $message)) {goto testsFail;}
    } while (!$pass);
/** Get the Class Methods and create a testing list */
    $methodList = get_class_methods(USF_TESTING_SCRIPT);
    foreach ($methodList as $method) {
        $words = preg_split('/(?=[A-Z])/',$method);
        $testGroup = trim(implode(' ', $words));
        $resultsArray['methods'][$testGroup] = array(
            'pass' => 'meh',
            'message' => 'Not Tested',
        );
    }
/** Test Group: methods - Test the Class Methods */
    $testGroup = 'methods';
/** Get Instance: Do we create a Singlton? */
    do {
        $thisTest = 'Get Instance';
        $pass = false;
        $shouldBe = true;
        $setAt = false;
        eval("\$loadedClass1 = " . USF_TESTING_SCRIPT . "::GetInstance();");
        $dump1 = CaptureDump($loadedClass1);
        eval("\$loadedClass2 = " . USF_TESTING_SCRIPT . "::GetInstance();");
        $dump2 = CaptureDump($loadedClass2);
        if ($dump1 === $dump2) {
            $setAt = true;
        }
        $message = 'Object Instance is not a Singleton.';
        if ($setAt == $shouldBe) {
            $pass = true;
            $message = '';
            unset($shouldBe, $setAt, $loadedClass1, $loadedClass2, $dump1, $dump2);
        }
        if (!RecordTestResults($pass, $message)) {goto testsFail;}
    } while (!$pass);
/** Test Group: functional - Can we actually use the Class */
    $testGroup = 'functional';
/** Read USF Config: Read and decode the USF Configuration File */
   do {
        $thisTest = 'Read USF Config';
        $pass = false;
        $shouldBe = true;
        list($result1, $setAt) = JsonData::ReadJsonFile();
        $message = '(Read File) Expected: ' . StringifyVariable($shouldBe) . ' Received: ' . StringifyVariable($setAt) . ' Info:  ' . StringifyVariable($result1);
        if($shouldBe == $setAt) {
            $shouldBe = true;
            list($result2, $setAt) = JsonData::ConvertJson($result1);
            $message = '(Convert Data) Expected: ' . StringifyVariable($shouldBe) . ' Received: ' . StringifyVariable($setAt) . ' Info:  ' . StringifyVariable($result2);
            if ($shouldBe == $setAt) {
                $shouldBe = 'USF Default Configuration file';
                $setAt = $result2['comment'];
                $message = '(Data) Expected: ' . StringifyVariable($shouldBe) . ' Received: ' . StringifyVariable($setAt);
                if ($shouldBe == $setAt) {
                    $pass = true;
                    $message = '';
                    unset($shouldBe, $setAt, $result1, $result2);
                }
            }
        }
        if (!RecordTestResults($pass, $message)) {goto testsFail;}
    } while (!$pass);
/** Read Web App Config: Read and decode "a" Web App Configuration File */
    do {
        // Simulate a Web App REQUEST / SESSION (from usf-prepend.inc)
        session_start();
        $_SESSION['usfWebApp'] = array();
        $requestedUsfWebApp = UsfMethod::WebAppFromUrl('http://www.example.com/test/index.html');
        $requestedWebApp = $requestedUsfWebApp['webApp'];
die(var_dump($requestedWebApp));
        $thisTest = 'Read Web App Config';
        $pass = false;
        $shouldBe = true;
        list($result1, $setAt) = JsonData::ReadJsonFile();
        $message = '(Read File) Expected: ' . StringifyVariable($shouldBe) . ' Received: ' . StringifyVariable($setAt) . ' Info:  ' . StringifyVariable($result1);
        if($shouldBe == $setAt) {
            $shouldBe = true;
            list($result2, $setAt) = JsonData::ConvertJson($result1);
            $message = '(Convert Data) Expected: ' . StringifyVariable($shouldBe) . ' Received: ' . StringifyVariable($setAt) . ' Info:  ' . StringifyVariable($result2);
            if ($shouldBe == $setAt) {
                $shouldBe = 'USF Default Configuration file';
                $setAt = $result2['comment'];
                $message = '(Data) Expected: ' . StringifyVariable($shouldBe) . ' Received: ' . StringifyVariable($setAt);
                if ($shouldBe == $setAt) {
                    $pass = true;
                    $message = '';
                    unset($shouldBe, $setAt, $result1, $result2);
                }
            }
        }
        if (!RecordTestResults($pass, $message)) {goto testsFail;}
    } while (!$pass);
/** Read Specific File: Read and decode a Specified JSON File */
   do {
        $thisTest = 'Read Specific File';
        $pass = false;
        $shouldBe = true;
        list($result1, $setAt) = JsonData::ReadJsonFile('json-test-data.json', USF_TEST_LOCATION);
        $message = '(Read File 1) Expected: ' . StringifyVariable($shouldBe) . ' Received: ' . StringifyVariable($setAt) . ' Info:  ' . $result1;
        if($shouldBe == $setAt) {
            list($result1, $setAt) = JsonData::ReadJsonFile('json-test-data', USF_TEST_LOCATION);
            $message = '(Read File 2) Expected: ' . StringifyVariable($shouldBe) . ' Received: ' . StringifyVariable($setAt) . ' Info:  ' . $result1;
            if($shouldBe == $setAt) {
                $shouldBe = true;
                list($result2, $setAt) = JsonData::ConvertJson($result1);
                $message = '(Convert Data) Expected: ' . StringifyVariable($shouldBe) . ' Received: ' . StringifyVariable($setAt) . ' Info:  ' . $result2;
                if ($shouldBe == $setAt) {
                    $shouldBe = 'USF Test Data for JsonData Test Script';
                    $setAt = $result2['comment'];
                    $message = '(Data) Expected: ' . StringifyVariable($shouldBe) . ' Received: ' . StringifyVariable($setAt);
                    if ($shouldBe == $setAt) {
                        $pass = true;
                        $message = '';
                        unset($shouldBe, $setAt, $result1, $result2);
                    }
                }
            }
        }
        if (!RecordTestResults($pass, $message)) {goto testsFail;}
    } while (!$pass);
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
