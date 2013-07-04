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
		
		$this->assertGreaterThan(0, count($campaigns), 'no campaign delivered');
		$this->assertInstanceOf('FACTFinder_Campaign', $campaigns[0], 'similar product is no record');
		$this->assertNotEmpty($campaigns[0], 'first campaign is empty');
		
		$this->assertTrue($campaigns[0]->hasFeedback('header'));
		$this->assertEquals($campaigns[0]->getFeedback('header'), 'Produktkampagne');
		$this->assertTrue($campaigns[0]->hasPushedProducts());
	}
	
	public function testShoppingCartCampaignLoading()
	{
		$productIds = array();
		$productIds[] = 123;
		$productIds[] = 456;
		$this->productCampaignAdapter->makeShoppingCartCampaign();
		$this->productCampaignAdapter->setProductIds($productIds);
		$campaigns = $this->productCampaignAdapter->getCampaigns();
		
		$this->assertGreaterThan(0, count($campaigns), 'no campaign delivered');
		$this->assertInstanceOf('FACTFinder_Campaign', $campaigns[0], 'similar product is no record');
		$this->assertNotEmpty($campaigns[0], 'first campaign is empty');
		
		$this->assertTrue($campaigns[0]->hasFeedback('header'));
		$this->assertEquals($campaigns[0]->getFeedback('header'), 'Warenkorbkampagne');
		$this->assertTrue($campaigns[0]->hasPushedProducts());
	}
}