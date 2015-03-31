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
class XmlSearchAdapter69WithCampaignTest extends PHPUnit_Framework_TestCase
{
	protected static $config;
	protected static $encodingHandler;
	protected static $paramsParser;
	protected static $log;

    /**
     * @var FACTFinder_Http_DataProvider
     */
    protected $dataProvider;
    /**
     * @var FACTFinder_Xml69_SearchAdapter
     */
    protected $searchAdapter;
	
	public static function setUpBeforeClass()
	{
		$zendConfig = FF::getSingleton('zend/config/xml', RESOURCES_DIR.DS.'config.xml', 'production');
		self::$config = FF::getSingleton('configuration', $zendConfig);
		
        self::$log = FF::getInstance('log4PhpLogger');
		self::$log->configure(RESOURCES_DIR.DS.'log4php.xml');
		
		self::$encodingHandler = FF::getInstance('encodingHandler', self::$config);
		self::$paramsParser = FF::getInstance('parametersParser', self::$config, self::$encodingHandler);
	}

	public function setUp()
	{
		$this->dataProvider = FF::getInstance('http/dummyProvider', self::$paramsParser->getServerRequestParams(), self::$config);
		$this->dataProvider->setFileLocation(RESOURCES_DIR.DS.'responses'.DS.'xml69');
		$this->searchAdapter = FF::getInstance('xml69/searchAdapter', $this->dataProvider, self::$paramsParser, self::$encodingHandler);
        $this->searchAdapter->setParam('query', 'brushless');
	}

    public function testCampaignLoading()
    {
        $campaigns = $this->searchAdapter->getCampaigns();

        $this->assertInstanceOf('FACTFinder_CampaignIterator', $campaigns);
        $this->assertEquals(1, count($campaigns));
        $this->assertInstanceOf('FACTFinder_Campaign', $campaigns[0]);
        $this->assertTrue($campaigns[0]->hasFeedback());
        $this->assertTrue(strpos($campaigns[0]->getFeedback('above asn'), 'Hochwertige Brushless Motoren') > 0);
    }
}