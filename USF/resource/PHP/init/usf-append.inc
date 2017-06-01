<?php
/**
 * PHP (included) USF "Auto Append"
 **/
// Serialize and Store active Class Objects
    /**
     * Loaded Classes:
     *  Logger, RecordServerAccess, XssFilter, FileAccess.class, JsonData.class
     */
     if ($sessionStarted) {
         if (!array_key_exists('usf_objects', $_SESSION)) {
             $_SESSION['usf_objects'] = array();
         }
         // Do this for ALL objects that require persistance
         $_SESSION['usf_objects']['Logger'] =  serialize($usfLog);
     }
