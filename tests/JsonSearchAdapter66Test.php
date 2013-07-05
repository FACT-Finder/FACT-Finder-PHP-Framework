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
class JsonSearchAdapter66Test extends PHPUnit_Framework_TestCase
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
     * @var FACTFinder_Xml67_SearchAdapter
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
        $this->dataProvider->setFileLocation(RESOURCES_DIR.DS.'responses'.DS.'json66');
        $this->dataProvider->setFileExtension(".json");
        $this->searchAdapter = FF::getInstance('json66/searchAdapter', $this->dataProvider, self::$paramsParser, self::$encodingHandler);
    }

    public function testGetResult()
    {
        $this->searchAdapter->setParam('query', 'bmx');

        $result = $this->searchAdapter->getResult();

        $this->assertInstanceOf('FACTFinder_Result', $result);
        $this->assertEquals(3, $result->getFoundRecordsCount());
        $this->assertEquals(1, count($result));
        $this->assertEquals("270863", $result[0]->getId());
    }

    public function testGetStatus()
    {
        $this->searchAdapter->setParam('query', 'bmx');

        $this->assertEquals(FACTFinder_Json66_SearchAdapter::RESULTS_FOUND, $this->searchAdapter->getStatus());
    }

    public function testGetSearchTimeInfo()
    {
        $this->searchAdapter->setParam('query', 'bmx');

        $this->assertFalse($this->searchAdapter->isSearchTimedOut());
    }

    public function testAsnLoading()
    {
        $this->searchAdapter->setParam('query', 'bmx');

        $asn = $this->searchAdapter->getAsn();

        $this->assertInstanceOf('FACTFinder_Asn', $asn);
        $this->assertEquals(4, count($asn));
        $this->assertTrue($asn[0]->isDefaultStyle());
        $this->assertEquals('Kategorie', $asn[0]->getName());
        $this->assertEquals(5, $asn[0]->getDetailedLinkCount());
        $this->assertEquals(3, count($asn[0]));
        $this->assertTrue($asn[0][0]->isSelected());
        $this->assertTrue($asn[0][1]->isSelected());
        $this->assertTrue($asn[0][2]->isSelected());
        $this->assertEquals(3, $asn[0][0]->getMatchCount());
        
        $this->assertFalse($asn[1][0]->isSelected());
        $this->assertFalse($asn[1][1]->isSelected());
        
        $this->assertTrue($asn[3]->isSliderStyle());
        $this->assertEquals('Preis', $asn[3]->getName());
        $this->assertEquals('€', $asn[3]->getUnit());
        $this->assertEquals(10, $asn[3]->getDetailedLinkCount());
        $slider = $asn[3][0];
        $this->assertEquals(20.0, $slider->getAbsoluteMax(), '', 0.001);
        $this->assertEquals(5.0, $slider->getAbsoluteMin(), '', 0.001);
        $this->assertEquals(15.95, $slider->getSelectedMax(), '', 0.001);
        $this->assertEquals(13.49, $slider->getSelectedMin(), '', 0.001);
        $this->assertEquals('products_price_min', $slider->getField());
    }

    public function testProductsPerPageOptionsLoading()
    {
        $this->searchAdapter->setParam('query', 'bmx');

        $pppo = $this->searchAdapter->getProductsPerPageOptions();
        
        $this->assertNotEmpty($pppo, 'products per page options should be loaded');
        $this->assertInstanceOf('FACTFinder_ProductsPerPageOptions', $pppo);
        $options = $pppo->getIterator();
        $this->assertEquals(3, count($options));
        $this->assertFalse($options[0]->isSelected());
        $this->assertTrue($options[1]->isSelected());
        $this->assertSame($options[0], $pppo->getDefaultOption());
        $this->assertSame($options[1], $pppo->getSelectedOption());
        $this->assertEquals('12', $options[0]->getValue());
    }

    public function testPagingLoading()
    {
        $this->searchAdapter->setParam('query', 'bmx');

        $paging = $this->searchAdapter->getPaging();
        $this->assertInstanceOf('FACTFinder_Paging', $paging);
        $this->assertEquals(1, $paging->getPageCount());
        $this->assertEquals(1, $paging->getCurrentPageNumber());
        $this->assertEquals(1, count($paging->getIterator()));
    }

    public function testSortingLoading()
    {
        $this->searchAdapter->setParam('query', 'bmx');

        $sorting = $this->searchAdapter->getSorting();
        $this->assertTrue(is_array($sorting));
        $this->assertEquals(5, count($sorting));
        $this->assertInstanceOf("FACTFinder_Item", $sorting[0]);
        $this->assertEquals('sort.relevanceDescription', $sorting[0]->getValue());
        $this->assertTrue($sorting[0]->isSelected());
        $this->assertFalse($sorting[1]->isSelected());
    }

    public function testBreadCrumbLoading()
    {
        $this->searchAdapter->setParam('query', 'bmx');

        $breadCrumb = $this->searchAdapter->getBreadCrumbTrail();

        $this->assertTrue(is_array($breadCrumb));
        $this->assertEquals(2, count($breadCrumb));
        $this->assertInstanceOf('FACTFinder_BreadCrumbItem', $breadCrumb[0]);
        $this->assertEquals('bmx', $breadCrumb[0]->getValue());
        $this->assertEquals('category1', $breadCrumb[1]->getFieldName());
    }
    
    public function testEmptyCampaigns()
    {
        $this->searchAdapter->setParam('query', 'bmx');
        
        $this->assertEquals(0, count($this->searchAdapter->getCampaigns()));
    }

    public function testCampaignLoading()
    {
        $this->searchAdapter->setParam('query', 'campaigns');

        $campaigns = $this->searchAdapter->getCampaigns();

        $this->assertInstanceOf('FACTFinder_CampaignIterator', $campaigns);
        $this->assertInstanceOf('FACTFinder_Campaign', $campaigns[0]);
        
        $this->assertTrue($campaigns->hasRedirect());
        $this->assertEquals('http://www.fact-finder.de', $campaigns->getRedirectUrl());
        
        $this->assertTrue($campaigns->hasFeedback());
        $expectedFeedback = implode(PHP_EOL, array("test feedback 1", "test feedback 2", ""));
        $this->assertEquals($expectedFeedback, $campaigns->getFeedback('0'));
        
        $this->assertTrue($campaigns->hasPushedProducts());
        $products = $campaigns->getPushedProducts();
        $this->assertEquals(3, count($products));
        $this->assertEquals('11660', $products[0]->getId());
        $this->assertEquals('Katalog >> Geschenkgutscheine', $products[0]->getValue('category'));
    }
    
    public function testNoError()
    {
        $this->searchAdapter->setParam('query', 'bmx');
        
        $this->assertNull($this->searchAdapter->getError());
        $this->assertNull($this->searchAdapter->getStackTrace());
    }
    
    public function testError()
    {
        $this->searchAdapter->setParam('query', 'error');
        
        $this->assertEquals('500', $this->searchAdapter->getError());
        $this->assertEquals('stacktrace', $this->searchAdapter->getStackTrace());
    }
}