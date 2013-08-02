<?php
class JsonSimilarRecordsAdapter67Test extends PHPUnit_Framework_TestCase
{
	protected static $config;
	protected static $encodingHandler;
	protected static $paramsParser;
	protected static $log;
	
	protected $dataProvider;
	protected $similarRecordsAdapter;
	
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
		$this->similarRecordsAdapter = FF::getInstance('json67/similarRecordsAdapter', $this->dataProvider, self::$paramsParser, self::$encodingHandler, self::$log);
	}
	
	public function testSimilarRecordLoading()
	{
		$this->similarRecordsAdapter->setProductId('123');
		$similarRecords = $this->similarRecordsAdapter->getSimilarRecords();
		
		$this->assertEquals(10, count($similarRecords), 'wrong number of similar records delivered');
		$this->assertInstanceOf('FACTFinder_Record', $similarRecords[0], 'similar product is no record');
		$this->assertNotEmpty($similarRecords[0], 'first similar record is empty');
        $this->assertEquals('277986', $similarRecords[0]->getId());
        $this->assertEquals('..Fahrräder..', $similarRecords[0]->getValue('category1'));
	}
	
	public function testSimilarIdLoading()
	{
		$this->similarRecordsAdapter->setProductId('123');
		$this->similarRecordsAdapter->setIdsOnly(true);
		$similarRecords = $this->similarRecordsAdapter->getSimilarRecords();
		
		$this->assertEquals(10, count($similarRecords), 'wrong number of similar records delivered');
		$this->assertInstanceOf('FACTFinder_Record', $similarRecords[0], 'similar product is no record');
		$this->assertNotEmpty($similarRecords[0], 'first similar record is empty');
        $this->assertEquals('278034', $similarRecords[0]->getId());
	}
	
	public function testSimilarRecordAfterIdLoading()
	{
		$this->similarRecordsAdapter->setProductId('123');
		$this->similarRecordsAdapter->setIdsOnly(true);
		$similarIds = $this->similarRecordsAdapter->getSimilarRecords();
		$this->similarRecordsAdapter->setIdsOnly(false);
		$similarRecords = $this->similarRecordsAdapter->getSimilarRecords();
		
		$this->assertEquals(10, count($similarIds), 'wrong number of similar records delivered');
		$this->assertInstanceOf('FACTFinder_Record', $similarIds[0], 'similar product is no record');
		$this->assertNotEmpty($similarIds[0], 'first similar record is empty');
        $this->assertEquals('278034', $similarIds[0]->getId());
		
		$this->assertInstanceOf('FACTFinder_Record', $similarRecords[0], 'similar product is no record');
		$this->assertNotEmpty($similarRecords[0], 'first similar record is empty');
        $this->assertEquals('277986', $similarRecords[0]->getId());
        $this->assertEquals('..Fahrräder..', $similarRecords[0]->getValue('category1'), 'first similar record does not contain all fields');
	}
	
	public function testMaxRecordCount()
	{
		$this->similarRecordsAdapter->setProductId('123');
		$this->similarRecordsAdapter->setIdsOnly(true);
		$this->similarRecordsAdapter->setMaxRecordCount(3);
		$similarRecords = $this->similarRecordsAdapter->getSimilarRecords();
		
		$this->assertEquals(3, count($similarRecords), 'wrong number of similar records delivered');
		$this->assertInstanceOf('FACTFinder_Record', $similarRecords[0], 'similar product is no record');
		$this->assertNotEmpty($similarRecords[0], 'first similar record is empty');
	}
	
	public function testSimilarAttributesLoading()
	{
		$this->similarRecordsAdapter->setProductId('123');
		$similarAttributes = $this->similarRecordsAdapter->getSimilarAttributes();
		
		$this->assertEquals(3, count($similarAttributes), 'wrong number of similar attributes delivered');
        $this->assertEquals('..Fahrräder..', $similarAttributes['category1'], 'wrong attribute value delivered');
	}
}