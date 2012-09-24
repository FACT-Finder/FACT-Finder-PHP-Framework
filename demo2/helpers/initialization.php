<?php
/**
 * This script contains functions for the initial setup of the FACT-Finder Framework
 *
 * @author    Rudolf Batt <rb@omikron.net>, Martin Buettner <martin.buettner@omikron.net>
 * @revision  $Rev: -1 $
 * @update    $LastChangedDate: $
 **/
 
/**
 * Sets up all necessary objects (as Singletons!) to create a search adapter and returns the latter.
 * Note: The search adapter, too, is a Singleton. Thus, an older search adapter will be overwritten
 *       by calling this function.
 * Note: this function has to be called before using the Configuration- and ParamsParser-Singleton
 *       for other purposes.
 *
 * @param	string	$version						A string specifying the type and version of the
 *                              					adapter (e.g. 'xml67' for an XML-based search
 *													adapter and a shop version 6.7)
 * @return	FACTFinder_Abstract_SearchAdapter		The search adapter Singleton
 **/
function getSearchAdapter($version, $log) {
	// TODO: cache configuration somehow, so it must not be loaded every time from harddisk!
	$zendConfig = FF::getSingleton('zend/config/xml', USERDATA_DIR.DS.'local.config.xml', 'production');
	$config = FF::getSingleton('configuration', $zendConfig);
    $curl = new SAI_Curl();
	$encodingHandler = FF::getSingleton('encodingHandler', $config, $log);
	$paramsParser = FF::getSingleton('parametersParser', $config, $encodingHandler, $log);
	$dataProvider = FF::getInstance('http/dataProvider', $curl, $paramsParser->getServerRequestParams(), $config, $log);
	return FF::getSingleton($version.'/searchAdapter', $dataProvider, $paramsParser, $encodingHandler, $log);
}

/**
 * Sets up all necessary objects (as Singletons!) to create a tag cloud adapter and returns the latter.
 * Note: The tag cloud adapter, too, is a Singleton. Thus, an older tag cloud adapter will be overwritten
 *       by calling this function.
 * Note: this function has to be called before using the Configuration- and ParamsParser-Singleton
 *       for other purposes.
 *
 * @param	string	$version						A string specifying the type and version of the
 *                              					adapter (e.g. 'xml67' for an XML-based tag cloud
 *													adapter and a shop version 6.7)
 * @return	FACTFinder_Abstract_SearchAdapter		The tag cloud adapter Singleton
 **/
function getTagCloudAdapter($version, $log) {
	// TODO: cache configuration somehow, so it must not be loaded every time from harddisk!
	$zendConfig = FF::getSingleton('zend/config/xml', USERDATA_DIR.DS.'local.config.xml', 'production');
	$config = FF::getSingleton('configuration', $zendConfig);
    $curl = new SAI_Curl();
	$encodingHandler = FF::getSingleton('encodingHandler', $config, $log);
	$paramsParser = FF::getSingleton('parametersParser', $config, $encodingHandler, $log);
	$dataProvider = FF::getInstance('http/dataProvider', $curl, $paramsParser->getServerRequestParams(), $config, $log);
	return FF::getSingleton($version.'/tagCloudAdapter', $dataProvider, $paramsParser, $encodingHandler, $log);
}