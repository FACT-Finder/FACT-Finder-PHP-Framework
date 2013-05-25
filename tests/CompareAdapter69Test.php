<?php
/**
 * FACT-Finder PHP Framework
 *
 * @category  Test
 * @package   FACTFinder\Xml69
 * @copyright Copyright (c) 2013 Omikron Data Quality GmbH (www.omikron.net)
 */

 /**
  * self-explanatory test
  */
class CompareAdapter69Test extends PHPUnit_Framework_TestCase
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
		$this->dataProvider->setFileLocation(RESOURCES_DIR.DS.'responses'.DS.'xml69');
		$this->compareAdapter = FF::getInstance('xml69/compareAdapter', $this->dataProvider, self::$paramsParser, self::$encodingHandler, self::$log);
	}
	
	public function testComparisonLoading()
	{
		$productIds = array();
		$productIds[] = 249601;
		$productIds[] = 19451;
		$productIds[] = 19447;
		$this->compareAdapter->setProductIds($productIds);
		$comparedRecords = $this->compareAdapter->getComparedRecords();
		
		$this->assertGreaterThan(0, count($comparedRecords), 'no similar records delivered');
		$this->assertEquals(3, count($comparedRecords), 'wrong number of records delivered');
		$this->assertInstanceOf('FACTFinder_Record', $comparedRecords[0], 'similar product is no record');
		$this->assertNotEmpty($comparedRecords[0], 'first similar record is empty');
	}
	
	public function testSlimComparisonLoading()
	{
		$productIds = array();
		$productIds[] = 249601;
		$productIds[] = 19451;
		$productIds[] = 19447;
		$this->compareAdapter->setProductIds($productIds);
		$this->compareAdapter->setIdsOnly(true);
		$comparedRecords = $this->compareAdapter->getComparedRecords();
		
		$this->assertGreaterThan(0, count($comparedRecords), 'no similar records delivered');
		$this->assertEquals(3, count($comparedRecords), 'wrong number of records delivered');
		$this->assertInstanceOf('FACTFinder_Record', $comparedRecords[0], 'similar product is no record');
		$this->assertNotEmpty($comparedRecords[0], 'first similar record is empty');
	}
	
	public function testAttributesLoading()
	{
		$productIds = array();
		$productIds[] = 249601;
		$productIds[] = 19451;
		$productIds[] = 19447;
		$this->compareAdapter->setProductIds($productIds);
		$comparableAttributes = $this->compareAdapter->getComparableAttributes();
		
		$this->assertGreaterThan(0, count($comparableAttributes), 'no similar attributes delivered');
		$this->assertEquals(3, count($comparableAttributes), 'wrong number of similar attributes delivered');
		$this->assertTrue($comparableAttributes['Colors']);
		$this->assertTrue($comparableAttributes['Hersteller']);
	}
}