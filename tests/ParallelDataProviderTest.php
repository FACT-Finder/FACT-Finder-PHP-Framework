<?php
/**
 * FACT-Finder PHP Framework
 *
 * @category  Test
 * @package   FACTFinder\Http
 * @copyright Copyright (c) 2012 Omikron Data Quality GmbH (www.omikron.net)
 */

 /**
  * self-explanatory test
  */
class ParallaDataProviderTest extends PHPUnit_Framework_TestCase
{
	protected static $config;
	protected static $config2;
	protected static $encodingHandler;
	protected static $encodingHandler2;
	protected static $paramsParser;
	protected static $paramsParser2;
	protected static $log;
	
	protected $dataProvider;
	protected $searchAdapter;
	protected $tagCloudAdapter;
	protected $tagCloudAdapter2;
	
	public static function setUpBeforeClass()
	{
		$zendConfig = FF::getSingleton('zend/config/xml', RESOURCES_DIR.DS.'config.xml', 'production');
		$zendConfig2 = FF::getSingleton('zend/config/xml', RESOURCES_DIR.DS.'config2.xml', 'production');
		self::$config = FF::getSingleton('configuration', $zendConfig);
		self::$config2 = FF::getSingleton('configuration', $zendConfig2);
		
		self::$log = FF::getInstance('log4PhpLogger');
		self::$log->configure(RESOURCES_DIR.DS.'log4php.xml');
		
		self::$encodingHandler = FF::getInstance('encodingHandler', self::$config, self::$log);
		self::$encodingHandler2 = FF::getInstance('encodingHandler', self::$config2, self::$log);
		self::$paramsParser = FF::getInstance('parametersParser', self::$config, self::$encodingHandler, self::$log);
		self::$paramsParser2 = FF::getInstance('parametersParser', self::$config2, self::$encodingHandler2, self::$log);
	}

	public function setUp()
	{
		$dataProvider_tagCloud = FACTFinder_Http_ParallelDataProvider::getDataProvider(self::$paramsParser->getServerRequestParams(), self::$config, self::$log);	
		$dataProvider_tagCloud2 = FACTFinder_Http_ParallelDataProvider::getDataProvider(self::$paramsParser2->getServerRequestParams(), self::$config2, self::$log);
		$dataProvider_search = FACTFinder_Http_ParallelDataProvider::getDataProvider(self::$paramsParser->getServerRequestParams(), self::$config, self::$log);
		
		$dataProvider_search->setParam('query', 'bmx');
		
		$this->tagCloudAdapter = FF::getInstance('xml65/tagCloudAdapter', $dataProvider_tagCloud, self::$paramsParser, self::$encodingHandler, self::$log);
		$this->tagCloudAdapter2 = FF::getInstance('xml65/tagCloudAdapter', $dataProvider_tagCloud2, self::$paramsParser2, self::$encodingHandler2, self::$log);
		$this->searchAdapter = FF::getInstance('xml65/searchAdapter', $dataProvider_search, self::$paramsParser, self::$encodingHandler, self::$log);
	}
	
	/**
	 * this test has to run before the other tests run, because the other test load the data via and this test wouldn't fail anymore.
	 *
     * @expectedException DataNotLoadedException
     */
	public function testExceptionOnPrematureDataRetrieval() // What a name
	{
		// no call to FACTFinder_Http_ParallelDataProvider::loadAllData();
		$result = $this->tagCloudAdapter->getTagCloud();
	}
	
	public function testParallelLoading()
	{
		FACTFinder_Http_ParallelDataProvider::loadAllData();
		
		$result = $this->searchAdapter->getResult();
		$tagCloud = $this->tagCloudAdapter->getTagCloud();
		
		$this->assertGreaterThan(0, count($tagCloud), 'no tag queries delivered');
		$this->assertInstanceOf('FACTFinder_TagQuery', $tagCloud[0], 'tag cloud element is no tag query');
		$this->assertNotEmpty($tagCloud[0], 'first tag query is empty');
	}
	
	public function testParallelChannelLoading()
	{
		FACTFinder_Http_ParallelDataProvider::loadAllData();
		
		$tagCloud = $this->tagCloudAdapter->getTagCloud();
		$tagCloud2 = $this->tagCloudAdapter2->getTagCloud();
		
		$this->assertGreaterThan(0, count($tagCloud), 'no tag queries delivered');
		$this->assertInstanceOf('FACTFinder_TagQuery', $tagCloud[0], 'tag cloud element is no tag query');
		$this->assertNotEmpty($tagCloud[0], 'first tag query is empty');
		
		$this->assertGreaterThan(0, count($tagCloud2), 'no tag queries delivered');
		$this->assertInstanceOf('FACTFinder_TagQuery', $tagCloud2[0], 'tag cloud element is no tag query');
		$this->assertNotEmpty($tagCloud2[0], 'first tag query is empty');
	}
}