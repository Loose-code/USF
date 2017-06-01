<?php
/* Automated USF Test Script for JsonData.Class - Singleton
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
    define('USF_TESTING_SCRIPT', 'UsfMethod');
    $testOut = '===USF Test Script for ' . USF_TESTING_SCRIPT . ' Class===' . PHP_EOL;
    $resourcesRequired = array(
        'Server Constants' => 'C:/Users/Peter/Documents/www/Server/resource/PHP/init/usf-constants.inc',
        'Test Script Methods' => 'C:/Users/Peter/Documents/www/Server/resource/PHP/test-scripts/test-methods.inc',
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
        $usfMethod1 = UsfMethod::GetInstance();
        $dump1 = CaptureDump($usfMethod1);
        $usfMethod2 = UsfMethod::GetInstance();
        $dump2 = CaptureDump($usfMethod2);
        if ($dump1 === $dump2) {
            $setAt = true;
        }
        $message = 'Object Instance is not a Singleton.';
        if ($setAt == $shouldBe) {
            $pass = true;
            $message = '';
            unset($shouldBe, $setAt, $usfMethod1, $usfMethod2, $dump1, $dump2);
        }
        if (!RecordTestResults($pass, $message)) {goto testsFail;}
    } while (!$pass);
/** Clean Path: Check we can regularise Directory Paths */
    do {
        $thisTest = 'Clean Path';
        $pass = false;
        $testData = array(
            array('c:\/outer/inner//sub', 'c:\outer\inner\sub\\', null),
            array('c:\/outer/inner//sub', 'c:\outer\inner\sub\\', false),
            array('c:\/outer/inner//sub', 'c://outer/inner/sub/', true),
            array('.//outer\/inner\\sub', '.\outer\inner\sub\\', null),
            array('..//outer\/inner\\sub', '..\outer\inner\sub\\', false),
            array('..\outer\inner\sub', '../outer/inner/sub/', true),
        );
        $count = 0;
        foreach ($testData as $thisTestData) {
            $count++;
            $shouldBe = $thisTestData[1];
            if (is_null($thisTestData[2])) {
                $setAt = UsfMethod::CleanPath($thisTestData[0]);
            }
            else {
                $setAt = UsfMethod::CleanPath($thisTestData[0], $thisTestData[2]);
            }
            $message = 'Directory Path (' . $count . ') not regularised: ' . $setAt . ' It should be: ' .$shouldBe;
            if ($shouldBe == $setAt) {
               $pass = true;
               $message = '';
               unset($shouldBe, $setAt);
            }
            else {
                break;
            }
        }
        if (!RecordTestResults($pass, $message)) {goto testsFail;}
    } while (!$pass);
/** Unique Array: Check we can modify an array for unique values */
    do {
        $thisTest = 'Unique Array';
        $pass = false;
        $testData = array(
            /** @note This test of UsfMethod::UniqueArray() relies on the
             *  behaviour of PHP Core array_unique() i.e:
             * "...keys are preserved. array_unique() sorts the values treated
             *  as string at first, then will keep the first key encountered
             *  for every value, and ignore all following keys. It does not
             *  mean that the key of the first related value from the unsorted
             *  array will be kept..."
             * So. keep this in mind when creating test and result data. ALSO,
             *  UsfMethod::UniqueArray() will "re-index" indexed arrays.
             */
            array(
                'arrayIn' => array(
                    'toplevel1' => array('this', 'that', 'other', 'this'),
                    'topLevel2' => array('something', 'something', 'else'),
                    'toplevel3' => array(0,1,2,3,4,0,1,2,3,4,5),
                    'topLevel4' => array(array(
                        'more' => 'less', 'less' => 'more', 57, 75, 'more' => 'less')
                        ),
                    array(99,98,50,'ten',50,'ten',),
                ),
                'arrayOut' => array(
                    'toplevel1' => array('this', 'that', 'other',),
                    'topLevel2' => array('something', 'else',),
                    'toplevel3' => array(0,1,2,3,4,5,),
                    'topLevel4' => array(
                        array(
                            'more' => 'less',
                            'less' => 'more',
                            57,
                            75,
                        )
                    ),
                    array(99,98,50,'ten',),
                ),
            ),
        );
       $count = 0;
        foreach ($testData as $thisTestArray) {
            $count++;
            $shouldBe = $thisTestArray['arrayOut'];
            $setAt = UsfMethod::UniqueArray($thisTestArray['arrayIn']);
            $message = 'Test Array (' . $count . ') not Unique: ' . CaptureDump($setAt, true) . ' It should be: ' . CaptureDump($shouldBe, true);
            if ($shouldBe == $setAt) {
               $pass = true;
               $message = '';
               unset($shouldBe, $setAt);
            }
            else {
                break;
            }
        }
        if (!RecordTestResults($pass, $message)) {goto testsFail;}
    } while (!$pass);
 /** Start This Session: See if we get given the "correct" PHP Session Id and Status */
    do {
        $thisTest = 'Start This Session';
        $testSessionId = 'startThisSession' . $timeNow;
        $pass = false;
        if (session_id()) {
            session_unset();
            session_destroy();
        }
        $setAt1 = UsfMethod::StartThisSession($testSessionId, 'dormant');
        $setAt2 = $testSessionId;
        $shouldBe1 = true;
        $shouldBe2 = session_id();
        $message = '(Dormant) PHP Session Start is: ' . StringifyVariable($setAt1) . ' and ' . $setAt2 . '" They should be: ' . StringifyVariable($shouldBe1) . ' and ' . $shouldBe2;
        if (($shouldBe1 == $setAt1) && ($shouldBe2 == $setAt2)) {
            session_unset();
            session_destroy();
            $setAt1 = UsfMethod::StartThisSession($testSessionId, 'none');
            $setAt2 = $testSessionId;
            $shouldBe1 = true;
            $shouldBe2 = session_id();
            $message = '(None) PHP Session is: ' . StringifyVariable($setAt1) . ' and ' . $setAt2 . ' They should be: ' . StringifyVariable($shouldBe1) . ' and not ' . $shouldBe2;
            if (($shouldBe1 == $setAt1) && ($shouldBe2 != $setAt2)) {
                $setAt1 = UsfMethod::StartThisSession($testSessionId, 'current');
                $setAt2 = $testSessionId;
                $shouldBe1 = true;
                $shouldBe2 = session_id();
                $message = '(Current) PHP Session is: ' . StringifyVariable($setAt1) . ' and ' . $setAt2 . ' They should be: ' . StringifyVariable($shouldBe1) . ' and not ' . $shouldBe2;
                if (($shouldBe1 == $setAt1) && ($shouldBe2 != $setAt2)) {
                    $pass = true;
                    $message = '';
                    unset($testSessionId, $setAt1 ,$setAt2, $shouldBe1, $shouldBe2);
                }
            }
        }
        if (!RecordTestResults($pass, $message)) {goto testsFail;}
    } while (!$pass);
/** Get Session Id: See if we get the "correct" PHOP Session Id and Status */
    do {
        $thisTest = 'Get Session Id';
        $testSessionId = 'getSessionId' . $timeNow;
        $pass = false;
        if (session_id()) {
            session_unset();
            session_destroy();
        }
        $shouldBe1 = '';
        $shouldBe2 = 'none';
        list ($setAt1, $setAt2) = UsfMethod::GetSessionId();
        $message = '(No) PHP Session is ' . StringifyVariable($setAt1) . ' and ' . $setAt2 . ' They should be: ' . StringifyVariable($shouldBe1) . ' and ' . $shouldBe2;
        if (($shouldBe1 == $setAt1) && ($shouldBe2 == $setAt2)) {
            $_COOKIE[PHP_SESSION_NAME] = $testSessionId;
            $shouldBe1 = $testSessionId;
            $shouldBe2 = 'dormant';
            list ($setAt1, $setAt2) = UsfMethod::GetSessionId();
            $message = '(Dormant) PHP Session is: ' . $setAt1 . ' and ' . $setAt2 . '" They should be: ' . $shouldBe1 . ' and ' . $shouldBe2;
            if (($shouldBe1 == $setAt1) && ($shouldBe2 == $setAt2)) {
                unset($_COOKIE);
                session_start();
                $shouldBe1 = session_id();
                $shouldBe2 = 'current';
                list ($setAt1, $setAt2) = UsfMethod::GetSessionId();
                $message = '(Active) PHP Session is: ' . $setAt1 . ' and ' . $setAt2 . '" They should be: ' . $shouldBe1 . ' and ' . $shouldBe2;
                if (($shouldBe1 == $setAt1) && ($shouldBe2 == $setAt2)) {
                    $pass = true;
                    $message = '';
                    unset($testSessionId, $shouldBe1, $shouldBe2, $setAt1, $setAt2);
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
