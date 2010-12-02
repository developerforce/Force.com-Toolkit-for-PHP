<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Define path to application directory
defined('APP_PATH')
    || define('APP_PATH', realpath(dirname(__FILE__)));
defined('LIB_PATH')
    || define('LIB_PATH', realpath(dirname(__FILE__) . '/Lib'));
    
define('SOAP_BASEDIR', realpath(dirname(__FILE__) . '/..'));

set_include_path(implode(PATH_SEPARATOR, array(
    LIB_PATH,
    SOAP_BASEDIR,
    get_include_path(),
)));

function __autoload($className) {
    $fullclasspath = "";
    // get separated directories
    $pathchunks=explode("_",$className);

    //re-build path without last item
    for($i=0; $i<(count($pathchunks)-1); $i++) {
	    $fullclasspath .= $pathchunks[$i].'/';
    }

//    require_once realpath(APP_PATH . '/' . $fullclasspath . $className . '.php');
    require_once realpath(APP_PATH . '/' . $fullclasspath . '/' . substr($className, (strrpos($className, '_') + 1)) . '.php');
}

$type = (int)$_GET['type'];
$target = $_GET['target'];
$testFactory = new Lib_Test_TestFactory(SOAP_BASEDIR, APP_PATH . '/test.log');
$testFactory->run($type, $target);

