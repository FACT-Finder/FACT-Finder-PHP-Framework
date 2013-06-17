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
		$this->similarRecordsAdapter = FF::getInstance('json67/similarRecordsAdapter', $this->dataProvider, self::$paramsParser, self::$encodingHandler, self::$log);
	}
	
	public function testSimilarRecordLoading()
	{
		$this->similarRecordsAdapter->setProductId('123');
		$similarRecords = $this->similarRecordsAdapter->getSimilarRecords();
		
		$this->assertGreaterThan(0, count($similarRecords), 'no similar records delivered');
		$this->assertInstanceOf('FACTFinder_Record', $similarRecords[0], 'similar product is no record');
		$this->assertNotEmpty($similarRecords[0], 'first similar record is empty');
	}
	
	public function testSimilarIdLoading()
	{
		$this->similarRecordsAdapter->setProductId('123');
		$this->similarRecordsAdapter->setIdsOnly(true);
		$similarIds = $this->similarRecordsAdapter->getSimilarRecords();
		
		$this->assertGreaterThan(0, count($similarIds), 'no similar ids delivered');
		$this->assertInstanceOf('FACTFinder_Record', $similarIds[0], 'similar product is no record');
		$this->assertNotEmpty($similarIds[0], 'first similar record is empty');
	}
	
	public function testSimilarRecordAfterIdLoading()
	{
		$this->similarRecordsAdapter->setProductId('123');
		$this->similarRecordsAdapter->setIdsOnly(true);
		$similarIds = $this->similarRecordsAdapter->getSimilarRecords();
		$this->similarRecordsAdapter->setIdsOnly(false);
		$similarRecords = $this->similarRecordsAdapter->getSimilarRecords();
		
		$this->assertGreaterThan(0, count($similarIds), 'no similar ids delivered');
		$this->assertInstanceOf('FACTFinder_Record', $similarIds[0], 'similar product is no record');
		$this->assertNotEmpty($similarIds[0], 'first similar record is empty');
		
		$this->assertInstanceOf('FACTFinder_Record', $similarRecords[0], 'similar product is no record');
		$this->assertNotEmpty($similarRecords[0], 'first similar record is empty');
		$this->assertEquals('..grau..', $similarRecords[0]->getValue('Farbe'), 'first similar record does not contain all fields');
	}
	
	public function testMaxRecordCount()
	{
		$this->similarRecordsAdapter->setProductId('123');
		$this->similarRecordsAdapter->setMaxRecordCount(3);
		$similarRecords = $this->similarRecordsAdapter->getSimilarRecords();
		
		$this->assertGreaterThan(0, count($similarRecords), 'no similar records delivered');
		$this->assertLessThan(4, count($similarRecords), 'more similar records delivered than specified');
		$this->assertInstanceOf('FACTFinder_Record', $similarRecords[0], 'similar product is no record');
		$this->assertNotEmpty($similarRecords[0], 'first similar record is empty');
	}
	
	public function testSimilarAttributesLoading()
	{
		$this->similarRecordsAdapter->setProductId('123');
		$similarAttributes = $this->similarRecordsAdapter->getSimilarAttributes();
		
		$this->assertGreaterThan(0, count($similarAttributes), 'no similar attributes delivered');
	}
}