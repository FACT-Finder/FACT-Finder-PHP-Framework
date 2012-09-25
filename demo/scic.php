<?php

define('DS', DIRECTORY_SEPARATOR);

define('DEMO_DIR', dirname(__FILE__));
define('LIB_DIR', dirname(DEMO_DIR).DS.'lib');
define('USERDATA_DIR', DEMO_DIR.DS.'userdata');

require_once LIB_DIR.DS.'FACTFinder'.DS.'Loader.php';

$log = FF::getInstance('log4PhpLogger');
$log->configure(USERDATA_DIR.DS.'log4php.xml');

require_once LIB_DIR.DS.'SAI'.DS.'Curl.php';

// TODO: cache configuration somehow, so it must not be loaded every time from harddisk!
$zendConfig = FF::getSingleton('zend/config/xml', USERDATA_DIR.DS.'local.config.xml', 'production');
$config = FF::getSingleton('configuration', $zendConfig);

$curl = new SAI_Curl();

$encodingHandler = FF::getInstance('encodingHandler', $config, $log);
$paramsParser = FF::getInstance('parametersParser', $config, $encodingHandler, $log);
$dataProvider = FF::getInstance('http/dataProvider', $paramsParser->getServerRequestParams(), $config, $log, $curl);
$scicAdapter = FF::getInstance('http/scicAdapter', $dataProvider, $paramsParser, $encodingHandler, $log);

echo $scicAdapter->doTrackingFromRequest();