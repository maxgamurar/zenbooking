<?php

// check session
if (session_id() === "")
    session_start();

define('WEB_ROOT', substr($_SERVER['SCRIPT_NAME'], 0, strpos($_SERVER['SCRIPT_NAME'], 'public/index.php')));

$path = ROOT . DS . 'config' . DS . 'config.php';

require_once ($path);

// Autoload classes 
spl_autoload_register(function($className) {

    $rootPath  = ROOT . DS;
    $valid     = false;

    //try to load a class based on router path
    //
    // check library
    $valid     = file_exists($classFile = $rootPath . 'library' . DS . $className . '.class.php');

    // if we cannot find any, then find library/core directory
    if (!$valid) {
        $valid     = file_exists($classFile = $rootPath . 'library' . DS . 'core' . DS . $className . '.class.php');
    }
    if (!$valid) {// find library/mvc directory
        $valid     = file_exists($classFile = $rootPath . 'library' . DS . 'mvc' . DS . $className . '.class.php');
    }
    if (!$valid) { // find application/controllers directory
        $valid     = file_exists($classFile = $rootPath . 'application' . DS . 'controllers' . DS . $className . '.php');
    }
    if (!$valid) { // find application/models directory
        $valid     = file_exists($classFile = $rootPath . 'application' . DS . 'models' . DS . $className . '.php');
    }

    // if we have valid file - include it
    if ($valid) {
        require_once($classFile);
    }
});

//load helpers
MyHelpers::removeMagicQuotes();
MyHelpers::unregisterGlobals();
$APPRouter = new Router($_route);

// dispatch the output
$APPRouter->dispatch();

// close session to speed up the concurrent connections
session_write_close();
