<?php
/* 
 * Quick and (sometimes) dirty functions to alleviaste repetative coding in the
 * test scripts
 * .
 */

function RecordTestResults($pass, $message) {
    global $resultsArray, $testGroup, $thisTest;
    $resultsArray[$testGroup][$thisTest] = array(
        'pass' => $pass,
        'message' => $message,
    );
    if ($pass === true) {
        return true;
    } else {
        return false;
    }
}
function StringifyVariable($variable) {
    $variableType = gettype($variable);
    switch ($variableType) {
        case 'string':
            if (empty($variable)) {
                $variable = 'EMPTY';
            }
            break;
        case 'array':
            $variable = print_r($variable);
            break;
        case 'object':
        case 'resource':
            break;
        case 'NULL':
            $variable = 'NULL';
            break;
        case 'boolean':
            if ($variable === true) {
                $variable = 'TRUE';
            }
            elseif ($variable == true) {
                $variable == 'TRUTHY';
            }
            elseif ($variable === false) {
                $variable = 'FALSE';
            }
            else {
                $variable == 'FALSEY';
            }
            break;
    }
    return $variable;
}
function CaptureDump($variable, $print=false) {
    ob_start();
    if (!$print) {
        var_dump($variable);
    }
    else {
        print_r($variable);
    }
    $dump = ob_get_contents();
    ob_end_clean();
    return $dump;
}

