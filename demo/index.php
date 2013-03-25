<?php
/**
 * initscript for the FACT-Finder PHP Framework demo using .phtml template files
 *
 * @author    Rudolf Batt <rb@omikron.net>
 * @revision  $Rev: -1 $
 * @update    $LastChangedDate: $
 **/

// configure application
error_reporting(E_ALL );

define('DS', DIRECTORY_SEPARATOR);

define('DEMO_DIR', dirname(__FILE__));
define('I18N_DIR', DEMO_DIR.DS.'i18n');
define('LIB_DIR', dirname(DEMO_DIR).DS.'lib');
define('TEMPLATE_DIR', DEMO_DIR.DS.'templates');
define('USERDATA_DIR', DEMO_DIR.DS.'userdata');

// init
require_once I18N_DIR.DS.'class.i18n.inc.php';
require_once LIB_DIR.DS.'FACTFinder'.DS.'Loader.php';
require_once USERDATA_DIR.DS.'HtmlGenerator.php';

// TODO: Fetch this through the FF-Loader
//require_once LIB_DIR.DS.'FACTFinder'.DS.'Http'.DS.'ParallelDataProvider.php';

// construct application
$log = FF::getInstance('log4PhpLogger');
$log->configure(USERDATA_DIR.DS.'log4php.xml');
FF::setLogger($log);
// if required a second logger with a different configuration can be created to handle logging outside of the library differently

// TODO: cache configuration somehow, so it must not be loaded every time from harddisk!
$zendConfig = FF::getSingleton('zend/config/xml', USERDATA_DIR.DS.'local.config.xml', 'production');
$config = FF::getSingleton('configuration', $zendConfig);

$encodingHandler = FF::getInstance('encodingHandler', $config, $log);
$paramsParser = FF::getInstance('parametersParser', $config, $encodingHandler, $log);
$dataProvider_search = FACTFinder_Http_ParallelDataProvider::getDataProvider($paramsParser->getServerRequestParams(), $config, $log);
//$dataProvider_search =  FF::getInstance('http/dummyProvider', $paramsParser->getServerRequestParams(), $config);
//$dataProvider_search->setFileLocation(USERDATA_DIR.DS.'responses'.DS.'xml67');
$dataProvider_tagCloud = FACTFinder_Http_ParallelDataProvider::getDataProvider($paramsParser->getServerRequestParams(), $config, $log);

if ($paramsParser->getRequestParam('productsPerPage', 12) > 60) { $dataProvider_search->setParam('productsPerPage', 12); }

$searchAdapter = FF::getInstance('xml69/searchAdapter', $dataProvider_search, $paramsParser, $encodingHandler, $log);
$tagCloudAdapter = FF::getInstance('xml68/tagCloudAdapter', $dataProvider_tagCloud, $paramsParser, $encodingHandler, $log);

// run / show view
$htmlGenerator = new HtmlGenerator($searchAdapter, $tagCloudAdapter, $paramsParser, $config, $log, TEMPLATE_DIR);
$output = $htmlGenerator->getHtmlCode();
echo $output;