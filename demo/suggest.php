<?php

error_reporting(0);

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
$suggestAdapter = FF::getInstance('http/suggestAdapter', $dataProvider, $paramsParser, $encodingHandler, $log);

try {
	echo $suggestAdapter->getSuggestions();
} catch (Exception $e) {
	if (!headers_sent()) {
		// close connection to browser if that is possible
		header("Content-Length: 0");
		header("Connection: close");
		flush();
	
		/* if you want, you can log errors here. this will not cause the user to wait for the request * /
		// log error
		$logfile = "suggest.error.log";
		$f = fopen($logfile, 'a');
		fwrite($f, date(DATE_RFC822).': error ['.$e->getMessage().'] for search request ['.$dataProvider->getAuthenticationUrl()."]\n");
		fclose($f);
		// */
	}
}