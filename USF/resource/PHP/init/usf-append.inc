<?php
/**
 * PHP (included) USF "Auto Append"
 **/
// Serialize and Store active Class Objects
    /** Loaded Classes (names of) can be (are) found in array $storedClasses */
    if (($USF_sessionStarted == true) && (isset($storedClasses))) {
        if (!array_key_exists('stored_objects', $_SESSION)) {
            $_SESSION['stored_objects'] = array();
        }
        foreach ($storedClasses as $thisClass) {
           $_SESSION['stored_objects'][$thisClass] = serialize($$thisClass);
        }
    }
die(var_dump($_SESSION));
