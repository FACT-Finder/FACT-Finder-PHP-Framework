<?php
class JsonCompareAdapter67Test extends PHPUnit_Framework_TestCase
{
	protected static $config;
	protected static $encodingHandler;
	protected static $paramsParser;
	protected static $log;
	
	protected $dataProvider;
	protected $compareAdapter;
	
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
		$this->dataProvider->setFileLocation(RESOURCES_DIR.DS.'responses'.DS.'json67');
        $this->dataProvider->setFileExtension(".json");
		$this->compareAdapter = FF::getInstance('json67/compareAdapter', $this->dataProvider, self::$paramsParser, self::$encodingHandler, self::$log);
	}
	
	public function testComparisonLoading()
	{
		$productIds = array();
		$productIds[] = 123;
		$productIds[] = 456;
		$productIds[] = 789;
		$this->compareAdapter->setProductIds($productIds);
		$comparedRecords = $this->compareAdapter->getComparedRecords();
        
		$this->assertEquals(3, count($comparedRecords), 'wrong number of records delivered');
		$this->assertInstanceOf('FACTFinder_Record', $comparedRecords[0], 'similar product is no record');
		$this->assertNotEmpty($comparedRecords[0], 'first similar record is empty');
        $this->assertEquals('123', $comparedRecords[0]->getId());
        $this->assertEquals('Serious', $comparedRecords[0]->getValue('Hersteller'));
	}
	
	public function testIdsOnlyComparisonLoading()
	{
		$productIds = array();
		$productIds[] = 123;
		$productIds[] = 456;
		$productIds[] = 789;
		$this->compareAdapter->setProductIds($productIds);
		$this->compareAdapter->setIdsOnly(true);
		$comparedRecords = $this->compareAdapter->getComparedRecords();
		
		$this->assertEquals(3, count($comparedRecords), 'wrong number of records delivered');
		$this->assertInstanceOf('FACTFinder_Record', $comparedRecords[0], 'similar product is no record');
		$this->assertNotEmpty($comparedRecords[0], 'first similar record is empty');
        $this->assertEquals('123', $comparedRecords[0]->getId());
	}
	
	public function testAttributesLoading()
	{
		$productIds = array();
		$productIds[] = 123;
		$productIds[] = 456;
		$productIds[] = 789;
		$this->compareAdapter->setProductIds($productIds);
        $comparableAttributes = $this->compareAdapter->getComparableAttributes();
		
        $this->assertEquals(3, count($comparableAttributes));
        $this->assertTrue($comparableAttributes['Farbe']);
        $this->assertTrue($comparableAttributes['Hersteller']);
        $this->assertFalse($comparableAttributes['Modelljahr']);
	}
}