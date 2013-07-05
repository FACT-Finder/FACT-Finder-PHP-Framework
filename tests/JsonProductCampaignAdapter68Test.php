<?php
class JsonProductCampaignAdapter68Test extends PHPUnit_Framework_TestCase
{
	protected static $config;
	protected static $encodingHandler;
	protected static $paramsParser;
	protected static $log;
	
	protected $dataProvider;
	protected $productCampaignAdapter;
	
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
		$this->productCampaignAdapter = FF::getInstance('json68/productCampaignAdapter', $this->dataProvider, self::$paramsParser, self::$encodingHandler, self::$log);
	}
	
	public function testProductCampaignLoading()
	{
		$productIds = array();
		$productIds[] = 123;
		$this->productCampaignAdapter->setProductIds($productIds);
		$campaigns = $this->productCampaignAdapter->getCampaigns();
		
		$this->assertInstanceOf('FACTFinder_CampaignIterator', $campaigns);
        $this->assertInstanceOf('FACTFinder_Campaign', $campaigns[0]);
        
        $this->assertTrue($campaigns->hasRedirect());
        $this->assertEquals('http://www.fact-finder.de', $campaigns->getRedirectUrl());
        
        $this->assertTrue($campaigns->hasFeedback());
        $expectedFeedback = "test feedback" . PHP_EOL;
        $this->assertEquals($expectedFeedback, $campaigns->getFeedback('html header'));
        $this->assertEquals($expectedFeedback, $campaigns->getFeedback('9'));
        
        $this->assertTrue($campaigns->hasPushedProducts());
        $products = $campaigns->getPushedProducts();
        $this->assertEquals(1, count($products));
        $this->assertEquals('278003', $products[0]->getId());
        $this->assertEquals('KHE', $products[0]->getValue('Brand'));
        
        $this->assertFalse($campaigns->hasActiveQuestions());
	}
	
	public function testShoppingCartCampaignLoading()
	{
		$productIds = array();
		$productIds[] = 456;
		$productIds[] = 789;
		$this->productCampaignAdapter->makeShoppingCartCampaign();
		$this->productCampaignAdapter->setProductIds($productIds);
		$campaigns = $this->productCampaignAdapter->getCampaigns();
		
		$this->assertInstanceOf('FACTFinder_CampaignIterator', $campaigns);
        $this->assertInstanceOf('FACTFinder_Campaign', $campaigns[0]);
        
        $this->assertTrue($campaigns->hasRedirect());
        $this->assertEquals('http://www.fact-finder.de', $campaigns->getRedirectUrl());
        
        $this->assertTrue($campaigns->hasFeedback());
        $expectedFeedback = "test feedback" . PHP_EOL;
        $this->assertEquals($expectedFeedback, $campaigns->getFeedback('html header'));
        $this->assertEquals($expectedFeedback, $campaigns->getFeedback('9'));
        
        $this->assertTrue($campaigns->hasPushedProducts());
        $products = $campaigns->getPushedProducts();
        $this->assertEquals(1, count($products));
        $this->assertEquals('278003', $products[0]->getId());
        $this->assertEquals('KHE', $products[0]->getValue('Brand'));
        
        $this->assertFalse($campaigns->hasActiveQuestions());
	}
}