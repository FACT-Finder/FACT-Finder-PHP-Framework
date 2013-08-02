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
class JsonRecommendationAdapter67Test extends PHPUnit_Framework_TestCase
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
		$this->dataProvider->setFileLocation(RESOURCES_DIR.DS.'responses'.DS.'json67');
        $this->dataProvider->setFileExtension(".json");
		$this->recommendationAdapter = FF::getInstance('json67/recommendationAdapter', $this->dataProvider, self::$paramsParser, self::$encodingHandler, self::$log);
	}
	
	public function testRecommendationLoading()
	{
		$this->recommendationAdapter->setProductId('274036');
		$recommendations = $this->recommendationAdapter->getRecommendations();
		
		$this->assertEquals(1, $recommendations->count(), 'wrong number of recommendations delivered');
		$this->assertInstanceOf('FACTFinder_Record', $recommendations[0], 'recommended product is no record');
		$this->assertNotEmpty($recommendations[0], 'first recommended record is empty');
        $this->assertEquals('10055', $recommendations[0]->getId(), 'wrong id delivered for first recommended record');
	}
	
	public function testIdsOnly()
	{
		$this->recommendationAdapter->setProductId('274036');
		$this->recommendationAdapter->setIdsOnly(true);
		$recommendations = $this->recommendationAdapter->getRecommendations();
		
		$this->assertEquals(1, $recommendations->count(), 'wrong number of recommendations delivered');
		$this->assertInstanceOf('FACTFinder_Record', $recommendations[0], 'recommended product is no record');
		$this->assertNotEmpty($recommendations[0], 'first recommended record is empty');
        $this->assertEquals('10055', $recommendations[0]->getId(), 'wrong id delivered for first recommended record');
    }
    
    public function testReload()
    {
        $this->recommendationAdapter->setProductId('274036');
		$recommendations = $this->recommendationAdapter->getRecommendations();
        $this->assertEquals('10055', $recommendations[0]->getId(), 'wrong id delivered for first recommended record');
        $this->recommendationAdapter->setProductId('233431');
		$recommendations = $this->recommendationAdapter->getRecommendations();
        $this->assertEquals('232727', $recommendations[0]->getId(), 'wrong id delivered for first recommended record');
    }
    
    public function testReloadAfterIdsOnly()
    {
        $this->recommendationAdapter->setProductId('274036');
        $this->recommendationAdapter->setIdsOnly(true);
		$recommendations = $this->recommendationAdapter->getRecommendations();
        $this->recommendationAdapter->setIdsOnly(false);
		$recommendations = $this->recommendationAdapter->getRecommendations();
        $this->assertNotNull($recommendations[0]->getValue('products_description'), 'did not load full recommendation record');
    }
	
	public function testMultiProductRecommendationLoading()
	{
		$this->recommendationAdapter->setProductIds(array('274036', '233431'));
		$recommendations = $this->recommendationAdapter->getRecommendations();
		
		$this->assertEquals(1, $recommendations->count(), 'wrong number of recommendations delivered');
		$this->assertInstanceOf('FACTFinder_Record', $recommendations[0], 'recommended product is no record');
		$this->assertNotEmpty($recommendations[0], 'first recommended record is empty');
        $this->assertEquals('222361', $recommendations[0]->getId(), 'wrong id delivered for first recommended record');	
	}
}