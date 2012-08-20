<?php
/**
 * FACT-Finder PHP Framework
 *
 * @category  Test
 * @package   FACTFinder\Xml67
 * @copyright Copyright (c) 2012 Omikron Data Quality GmbH (www.omikron.net)
 */

 /**
  * self-explanatory test
  */
class SearchAdapter67Test extends PHPUnit_Framework_TestCase
{
	protected static $config;
	protected static $encodingHandler;
	protected static $paramsParser;
	protected static $log;
	
	protected $dataProvider;
	protected $searchAdapater;
	
	public static function setUpBeforeClass()
	{
		$zendConfig = FF::getSingleton('zend/config/xml', RESOURCES_DIR.DS.'config.xml', 'production');
		self::$config = FF::getSingleton('configuration', $zendConfig);
		
        self::$log = FF::getInstance('log4PhpLogger');
		self::$log->configure(RESOURCES_DIR.DS.'log4php.xml');
		
		self::$encodingHandler = FF::getInstance('encodingHandler', self::$config);
		self::$paramsParser = FF::getInstance('parametersParser', self::$config, self::$encodingHandler);
	}

	public function setUp()
	{
		$this->dataProvider = FF::getInstance('http/dummyProvider', self::$paramsParser->getServerRequestParams(), self::$config);
		$this->dataProvider->setFileLocation(RESOURCES_DIR.DS.'responses'.DS.'xml67');
		$this->searchAdapater = FF::getInstance('xml67/searchAdapter', $this->dataProvider, self::$paramsParser, self::$encodingHandler);
	}
	
	public function testProductsPerPageOptionsLoading()
	{
		$this->dataProvider->setParam('query', 'foobar');
		
		$pppo = $this->searchAdapater->getProductsPerPageOptions();
		
		$this->assertNotEmpty($pppo, 'products per page options should be loaded');
		$this->assertEquals(6, $pppo->getSelectedOption()->getValue());
		$this->assertEquals(24, $pppo->getDefaultOption()->getValue());
	}
}