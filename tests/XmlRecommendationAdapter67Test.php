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
class XmlRecommendationAdapter67Test extends PHPUnit_Framework_TestCase
{
	protected static $config;
	protected static $encodingHandler;
	protected static $paramsParser;
	protected static $log;
	
	protected $dataProvider;
	protected $recommendationAdapter;
	
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
		$this->dataProvider->setFileLocation(RESOURCES_DIR.DS.'responses'.DS.'xml67');
		$this->recommendationAdapter = FF::getInstance('xml67/recommendationAdapter', $this->dataProvider, self::$paramsParser, self::$encodingHandler, self::$log);
	}
	
	public function testRecommendationLoading()
	{
		$this->recommendationAdapter->setProductId('123');
		$recommendations = $this->recommendationAdapter->getRecommendations();
		
		$this->assertGreaterThan(0, $recommendations->count(), 'no recommendations delivered');
		$this->assertInstanceOf('FACTFinder_Record', $recommendations[0], 'recommended product is no record');
		$this->assertNotEmpty($recommendations[0], 'first recommended record is empty');
	}
	
	public function testIdsOnly()
	{
		$this->recommendationAdapter->setProductId('123');
		$this->recommendationAdapter->setIdsOnly(true);
		$recommendations = $this->recommendationAdapter->getRecommendations();
		
		$this->assertGreaterThan(0, $recommendations->count(), 'no recommendations delivered');
		$this->assertInstanceOf('FACTFinder_Record', $recommendations[0], 'recommended product is no record');
		$this->assertNotEmpty($recommendations[0], 'first recommended record is empty');
	}
	
	public function testMaxResults()
	{
		$this->recommendationAdapter->setProductId('123');
		$this->recommendationAdapter->setMaxResults(3);
		$recommendations = $this->recommendationAdapter->getRecommendations();
		
		$this->assertEquals(3, $recommendations->count(), 'wrong number recommendations delivered');
		$this->assertInstanceOf('FACTFinder_Record', $recommendations[0], 'recommended product is no record');
		$this->assertNotEmpty($recommendations[0], 'first recommended record is empty');
	}
	
	public function testMultiProductRecommendationLoading()
	{
		$this->recommendationAdapter->setProductIds(array('123', '456'));
		$recommendations = $this->recommendationAdapter->getRecommendations();
		
		$this->assertGreaterThan(0, $recommendations->count(), 'no recommendations delivered');
		$this->assertInstanceOf('FACTFinder_Record', $recommendations[0], 'recommended product is no record');
		$this->assertNotEmpty($recommendations[0], 'first recommended record is empty');
	}
}