<?php
 /**
  * self-explanatory test
  */
class JsonSuggestAdapter66Test extends PHPUnit_Framework_TestCase
{
	protected static $config;
	protected static $encodingHandler;
	protected static $paramsParser;
	protected static $log;
	
	protected $dataProvider;
	protected $suggestAdapter;
	
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
		$this->dataProvider->setFileLocation(RESOURCES_DIR.DS.'responses'.DS.'json66');
        $this->dataProvider->setFileExtension(".json");
		$this->suggestAdapter = FF::getInstance('json66/suggestAdapter', $this->dataProvider, self::$paramsParser, self::$encodingHandler, self::$log);
	}
	
	public function testSuggestionLoading()
	{
		$this->dataProvider->setParam('query', 'bmx');
		
		$suggestions = $this->suggestAdapter->getSuggestions();
		
		$this->assertEquals(3, count($suggestions), 'wrong number of suggest queries delivered');
		$this->assertInstanceOf('FACTFinder_SuggestQuery', $suggestions[0], 'suggestion element is no suggest query');
		$this->assertNotEmpty($suggestions[0], 'first suggest query is empty');
        $this->assertEquals('BMX', $suggestions[0]->getQuery(), 'wrong query delivered for first suggest item');
        $this->assertEquals('category', $suggestions[0]->getType(), 'wrong type delivered for first suggest item');
        $this->assertEquals('productName', $suggestions[2]->getType(), 'wrong type delivered for third suggest item');
	}
}