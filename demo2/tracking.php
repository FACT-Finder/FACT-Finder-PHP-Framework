<?php

/**
 * Script for AJAX requests to notify the Shopping Cart Information Collector
 *
 * @author    Rudolf Batt <rb@omikron.net>, Martin Buettner <martin.buettner@omikron.net>
 * @revision  $Rev: -1 $
 * @update    $LastChangedDate: $
 **/

require_once 'init.php';

$log = FF::getInstance('log4PhpLogger');
$log->configure(USERDATA_DIR.DS.'log4php.xml');

// TODO: cache configuration somehow, so it must not be loaded every time from harddisk!
$zendConfig = FF::getSingleton('zend/config/xml', USERDATA_DIR.DS.'local.config.xml', 'production');
$config = FF::getSingleton('configuration', $zendConfig);

$curl = FF::getInstance('curl');

$encodingHandler = FF::getInstance('encodingHandler', $config, $log);
$paramsParser = FF::getInstance('parametersParser', $config, $encodingHandler, $log);
$dataProvider = FF::getInstance('http/dataProvider', $paramsParser->getServerRequestParams(), $config, $log, $curl);
//$trackingAdapter = FF::getInstance('http/scicAdapter', $dataProvider, $paramsParser, $encodingHandler, $log);
$trackingAdapter = FF::getInstance('xml69/trackingAdapter', $dataProvider, $paramsParser, $encodingHandler, $log);

echo $trackingAdapter->doTrackingFromRequest();
