<?php
class JsonProductCampaignAdapter67Test extends PHPUnit_Framework_TestCase
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
		$this->dataProvider->setFileLocation(RESOURCES_DIR.DS.'responses'.DS.'json67');
        $this->dataProvider->setFileExtension(".json");
		$this->productCampaignAdapter = FF::getInstance('json67/productCampaignAdapter', $this->dataProvider, self::$paramsParser, self::$encodingHandler, self::$log);
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
        $this->assertEquals('277992', $products[0]->getId());
        $this->assertEquals('Katalog', $products[0]->getValue('category0'));
        
        $this->assertTrue($campaigns->hasActiveQuestions());
        $questions = $campaigns->getActiveQuestions();
        $this->assertEquals(1, count($questions));
        $this->assertEquals('question text', $questions[0]->getText());
        $answers = $questions[0]->getAnswers();
        $this->assertEquals(2, count($answers));
        $this->assertEquals('answer text 1', $answers[0]->getText());
        $this->assertFalse($answers[0]->hasSubquestions());
        $this->assertEquals('answer text 2', $answers[1]->getText());
        $this->assertFalse($answers[1]->hasSubquestions());
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
        $this->assertEquals('277992', $products[0]->getId());
        $this->assertEquals('Katalog', $products[0]->getValue('category0'));
        
        $this->assertTrue($campaigns->hasActiveQuestions());
        $questions = $campaigns->getActiveQuestions();
        $this->assertEquals(1, count($questions));
        $this->assertEquals('question text', $questions[0]->getText());
        $answers = $questions[0]->getAnswers();
        $this->assertEquals(2, count($answers));
        $this->assertEquals('answer text 1', $answers[0]->getText());
        $this->assertFalse($answers[0]->hasSubquestions());
        $this->assertEquals('answer text 2', $answers[1]->getText());
        $this->assertFalse($answers[1]->hasSubquestions());
	}
}