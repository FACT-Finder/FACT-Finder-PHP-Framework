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
class JsonProductCampaignAdapter69Test extends PHPUnit_Framework_TestCase
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
		$this->dataProvider->setFileLocation(RESOURCES_DIR.DS.'responses'.DS.'json69');
		$this->productCampaignAdapter = FF::getInstance('json69/productCampaignAdapter', $this->dataProvider, self::$paramsParser, self::$encodingHandler, self::$log);
	}
	
	public function testProductCampaignLoading()
	{
		$productIds = array();
		$productIds[] = 249601;
		$this->productCampaignAdapter->setProductIds($productIds);
		$campaigns = $this->productCampaignAdapter->getCampaigns();
		
		$this->assertGreaterThan(0, count($campaigns), 'no campaign delivered');
		$this->assertInstanceOf('FACTFinder_Campaign', $campaigns[0], 'is not a campaign');
		$this->assertNotEmpty($campaigns[0], 'first campaign is empty');
		
		$this->assertTrue($campaigns[0]->hasFeedback('header'));
		$this->assertEquals($campaigns[0]->getFeedback('header'), 'Produktkampagne');
		$this->assertTrue($campaigns[0]->hasPushedProducts());
	}
	
	public function testShoppingCartCampaignLoading()
	{
		$productIds = array();
		$productIds[] = 249601;
		$productIds[] = 19451;
		$this->productCampaignAdapter->makeShoppingCartCampaign();
		$this->productCampaignAdapter->setProductIds($productIds);
		$campaigns = $this->productCampaignAdapter->getCampaigns();
		
		$this->assertGreaterThan(0, count($campaigns), 'no campaign delivered');
		$this->assertInstanceOf('FACTFinder_Campaign', $campaigns[0], 'is not a campaign');
		$this->assertNotEmpty($campaigns[0], 'first campaign is empty');
		
		$this->assertTrue($campaigns[0]->hasFeedback('header'));
		$this->assertEquals($campaigns[0]->getFeedback('header'), 'Warenkorbkampagne');
		$this->assertTrue($campaigns[0]->hasPushedProducts());
	}
}
