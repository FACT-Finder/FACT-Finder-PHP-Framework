<?php
 /**
  * self-explanatory test
  */
class JsonImportAdapter68Test extends PHPUnit_Framework_TestCase
{
	protected static $config;
	protected static $encodingHandler;
	protected static $paramsParser;
	protected static $log;
	
	protected $dataProvider;
	protected $importAdapter;
	
	public static function setUpBeforeClass()
	{
		$zendConfig = FF::getSingleton('zend/config/xml', RESOURCES_DIR.DS.'config.xml', 'production');
		self::$config = FF::getSingleton('configuration', $zendConfig);
		
		self::$log = FF::getInstance('log4PhpLogger');
		self::$log->configure(RESOURCES_DIR.DS.'log4php.xml');
		
		self::$encodingHandler = FF::getInstance('encodingHandler', self::$config, self::$log);
		self::$paramsParser = FF::getInstance('parametersParser', self::$config, self::$encodingHandler, self::$log);
	}

	public function setUp()
	{
		$this->dataProvider = FF::getInstance('http/dummyProvider', self::$paramsParser->getServerRequestParams(), self::$config, self::$log);
        $this->dataProvider->setFileLocation(RESOURCES_DIR.DS.'responses'.DS.'json68');
        $this->dataProvider->setFileExtension(".json");
		$this->importAdapter = FF::getInstance('json68/importAdapter', $this->dataProvider, self::$paramsParser, self::$encodingHandler, self::$log);
	}
	
	public function testDataImport()
	{
        $this->markTestIncomplete('Test incomplete: missing local response file.');
		$this->importAdapter->triggerDataImport();
	}
	
	public function testSuggestImport()
	{
        $this->markTestIncomplete('Test incomplete: missing local response file.');
		$this->importAdapter->triggerSuggestImport();
	}
	
	public function testRecommendationImport()
	{
        $this->markTestIncomplete('Test incomplete: missing local response file.');
		$this->importAdapter->triggerRecommendationImport();
	}
}