<?php
    while (ob_get_level() != 0) {
        ob_end_clean();
    }
    $bufferStarted = ob_start();
    require_once('usf-constants.inc');
    require_once(USF_PHP_CLASS_DIRECTORY . 'UsfMethod.class');
    require_once(USF_PHP_CLASS_DIRECTORY . 'JsonData.class');
    require_once(USF_PHP_CLASS_DIRECTORY . 'FileAccess.class');
    require_once(USF_PHP_CLASS_DIRECTORY . 'Logger.class');
    require_once(USF_PHP_CLASS_DIRECTORY . 'XssFilter.class');
    require_once(USF_PHP_CLASS_DIRECTORY . 'RecordServerAccess.class');

// Start the Initialisation Log and other initial values
    $initLog = new Logger('initLog');
    $initLog->SetLogging('debug');
    $accessTime     = $_SERVER['REQUEST_TIME'];
    $fullRequest    = $_SERVER['REQUEST_URI'];
    $serializedData = array(
        'url'     => $fullRequest,
        'server'  => serialize($_SERVER),
        'request' => serialize($_REQUEST),
        'files'   => serialize($_FILES),
    );

//  Ensures that there is a SESSION then start main USF Session Log & other SESSION dependancies.
    /* @note Sessions run as "expected" but NARS requires "session sharing"
     * @TODO Needs more work for NARS i.e. Code for Sharing session */
    list($thisSessionId, $thisSessionStatus) = UsfMethod::GetSessionId();
    $USF_sessionStarted = UsfMethod::StartThisSession($thisSessionId, $thisSessionStatus);
    if ($thisSessionStatus == 'none') {
        $accessComment = 'New Session: ' . session_id();
    }
    $usfLog = UsfMethod::RetrieveObject('Logger', 'usfLog', 'usfLog');
    $initLog->AssociateSession();

// Get any REQUEST instuctions - Do we want to Kill the Session? */
    /* @note The initial request has to be made but, by definition, this script
     * will be reloaded: So, to aviod a redirection loop we have to impliment a
     * "run-once" scenario. */
    $destroySession = false;
    if ((array_key_exists('xsession', $_REQUEST)) && ($_REQUEST['xsession'] === 'true')) {
        $destroySession = true;
        $accessComment = 'Destroy Session [' . session_id() . '] request by query string.';
    }
    if ((array_key_exists('REQUEST_URI', $_SERVER)) && ($_SERVER['REQUEST_URI'] == DESTROY_SESSION_DIRECT_ACCESS)) {
        if ((array_key_exists('redirected', $_REQUEST)) && ($_REQUEST['redirected'] === 'true')) {
            $destroySession = false;
            DestroySession();
            die('Fatal Error Encountered: Unable to destroy the Session.');
            /** @todo The session is not being destroyed - or is being redirected incorrectly */
        }
        $destroySession = true;
        $accessComment = 'Destroy Session [' . session_id() . '] requested by direct access.';
    }
    /* Disables the forced redirect to existing Web App */
    $bypassCrossSite = false;
    if ((array_key_exists('cross', $_REQUEST)) && ($_REQUEST['cross'] == 'true')) {
        $bypassCrossSite = true;
    }
    /* Forces various methods to recalculate data rather than retrive from PHP Session */
    $refreshSessionLookup = false;
    if ((array_key_exists('refresh', $_REQUEST)) && ($_REQUEST['refresh'] == 'true')) {
        $refreshSessionLookup = true;
    }
    define('USF_REFRESH_SESSION_LOOKUP', $refreshSessionLookup);


//  Determine the Web App and set up for redirect if required
    $requestedUsfWebApp = UsfMethod::WebAppFromUrl($fullRequest);
    $requestedWebApp = $requestedUsfWebApp['webApp'];
    $webAppDisplay = 'init';
    $redirectRequired = false;
    /* @note The PHP Session (should) be started and the "usfWebApp" element
     * created by function WebAppFromUrl() but the associated variables will
     * not be commited to Session until ALL the checks have been made. So, on
     * the first run this element is yet to exist. */
    if (array_key_exists('webApp', $_SESSION['usfWebApp'])) {
        $oldWebApp = WebAppFromSession('app');
        $webAppDisplay = WebAppFromSession('display');
        if (($oldWebApp != $requestedWebApp) && !$destroySession && !$bypassCrossSite) {
            $accessComment = 'Forced Redirect [' . $requestedUsfWebApp['display'] . ' to ' . $webAppDisplay . ']';
            $redirectRequired = $oldWebApp;
        }
    }

//  Check for XSS and similar
    $filterXss = new XssFilter($serializedData, USF_REFRESH_SESSION_LOOKUP);
    $validMethod = $filterXss->ValidRequestMethod($_SERVER['REQUEST_METHOD']);
    if ($validMethod['status'] === true) {
        list($validAccess, $xssCheck, $xssComment) = $filterXss->ProcessXss();
    }
    if (!empty($validMethod['result'])) {
        $accessComment = $validMethod['result'];
    }

//  Log Server Activity
    if (!isset($accessComment)) {
        $accessComment = '';
    }
    else {
        $initLog->WriteLine($accessComment, 'SYSTEM');
    }
    $accessLog = new RecordServerAccess($webAppDisplay, $accessTime, $thisSessionId, $accessComment, $serializedData);
    If ($accessLog->GetErrorStatus() === true) {
        $errorPacket = $accessLog->GetErrorPacket();
        FatalErrorEvent($errorPacket);
    }
    $accessLog->PurgeTables($accessTime);
    If ($accessLog->GetErrorStatus() === true) {
        $errorPacket = $accessLog->GetErrorPacket();
        FatalErrorEvent($errorPacket);
    }

// Do we need to Kill XSS?
    if (!$validAccess) {
        $errorPacket = $filterXss->GetLastError();
        if ($errorPacket !== false) {
            $errorPacket['action'] = 'XSS Filter';
            $errorPacket['method'] = $xssCheck;
            FatalErrorEvent($errorPacket);
            die('XSS Violation: ' . $xssCheck);
        }
    }

// Do we have to destroy the Session?
    if ($destroySession) {
        DestroySession();
        die('Fatal Error Encountered: Unable to Redirect to Session Destroy');
    }

// Do we have to redirect the Session?
    if ($redirectRequired !== false) {
        RedirectWebApp($redirectRequired);
        die('Fatal Error Encountered: Forced redirected to ' . $redirectRequired . ' failed.');
    }

// Now we can start the USF Session
    $_SESSION['usfWebApp'] = $requestedUsfWebApp;
    $webApp = &$requestedWebApp;

// Define those Web App Constants we need now
    define('USF_WEB_APP_HOME_WEB', UsfMethod::CleanPath($_SESSION['usfWebApp']['webApp'], true));
    define('USF_WEB_APP_DIRECTORY_PHP', UsfMethod::CleanPath(USF_WEB_ROOT_DIRECTORY_PHP . $webApp));
    define('WEB_APP_CONFIG_DIRECTORY_PHP', UsfMethod::CleanPath(USF_SITE_CONFIG_DIRECTORY_PHP . $webApp));

// Bootstrap the Web App 
    $desiredIncludes = array('site-constants', 'site-methods', 'site-init');
    $availableIncludes = PreloadIncludeCheck($desiredIncludes);
    unset(
        $accessComment,
        $accessLog,
        $accessTime,
        $bypassCrossSite,
        $desiredIncludes,
        $destroySession,
        $errorPacket,
        $filterXss,
        $initLog,
        $oldWebApp,
        $redirectRequired,
        $requestedWebApp,
        $serializedData,
        $thisSessionStatus,
        $validAccess,
        $validMethod,
        $xssCheck,
        $xssComment
    );
    ob_end_clean();
    foreach ($availableIncludes as $includeFile) {
        include_once($includeFile);
    }
