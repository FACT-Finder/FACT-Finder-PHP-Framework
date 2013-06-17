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
class JsonSearchAdapter69Test extends PHPUnit_Framework_TestCase
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
		$this->dataProvider->setFileLocation(RESOURCES_DIR.DS.'responses'.DS.'json69');
		$this->searchAdapter = FF::getInstance('json69/searchAdapter', $this->dataProvider, self::$paramsParser, self::$encodingHandler);
	}

    public function testResultLoading()
    {
        $this->searchAdapter->setParam('query', 'fahrrad');

        $result = $this->searchAdapter->getResult();

        $this->assertInstanceOf('FACTFinder_Result', $result);
        $this->assertEquals(7355, $result->getFoundRecordsCount());
    }

    public function testGetStatus()
    {
        $this->searchAdapter->setParam('query', 'fahrrad');

        $status = $this->searchAdapter->getStatus();

        $this->assertEquals(FACTFinder_Xml69_SearchAdapter::RESULTS_FOUND, $status);
    }

    public function testAsnLoading()
    {
        $this->searchAdapter->setParam('query', 'fahrrad');

        $asn = $this->searchAdapter->getAsn();

        $this->assertInstanceOf('FACTFinder_Asn', $asn);
        $this->assertEquals(13, count($asn));
    }
    
    public function testAsnFilterStyles()
    {
        $this->searchAdapter->setParam('query', 'fahrrad');
        
        $asn = $this->searchAdapter->getAsn();
        
        $this->assertTrue($asn[0]->isTreeStyle());
        $this->assertTrue($asn[1]->isDefaultStyle());
        $this->assertTrue($asn[2]->isMultiSelectStyle());
        $this->assertTrue($asn[3]->isSliderStyle());
    }

    public function testProductsPerPageOptionsLoading()
    {
        $this->searchAdapter->setParam('query', 'fahrrad');

        $pppo = $this->searchAdapter->getProductsPerPageOptions();

        $this->assertNotEmpty($pppo, 'products per page options should be loaded');
        $this->assertInstanceOf('FACTFinder_ProductsPerPageOptions', $pppo);
        $this->assertEquals(24, $pppo->getSelectedOption()->getValue());
        $this->assertEquals(12, $pppo->getDefaultOption()->getValue());
    }

    public function testPagingLoading()
    {
        $this->searchAdapter->setParam('query', 'fahrrad');

        $paging = $this->searchAdapter->getPaging();

        $this->assertInstanceOf('FACTFinder_Paging', $paging);
        $this->assertEquals(307, $paging->getPageCount());
        $this->assertEquals(1, $paging->getCurrentPageNumber());
    }

    public function testSortingLoading()
    {
        $this->searchAdapter->setParam('query', 'fahrrad');

        $sorting = $this->searchAdapter->getSorting();

        $this->assertTrue(is_array($sorting));
        $this->assertEquals(5, count($sorting));
        $this->assertInstanceOf("FACTFinder_Item", $sorting[0]);
    }

    public function testBreadCrumbLoading()
    {
        $this->searchAdapter->setParam('query', 'fahrrad');

        $breadCrumb = $this->searchAdapter->getBreadCrumbTrail();

        $this->assertTrue(is_array($breadCrumb));
        $this->assertEquals(1, count($breadCrumb));
        $this->assertInstanceOf('FACTFinder_BreadCrumbItem', $breadCrumb[0]);
    }

    public function testCampaignLoading()
    {
        $this->searchAdapter->setParam('query', 'fahrrad');

        $campaigns = $this->searchAdapter->getCampaigns();

        $this->assertInstanceOf('FACTFinder_CampaignIterator', $campaigns);
        $this->assertEquals(1, count($campaigns));
        $this->assertInstanceOf('FACTFinder_Campaign', $campaigns[0]);
        $this->assertTrue($campaigns[0]->hasActiveQuestions());
    }
}