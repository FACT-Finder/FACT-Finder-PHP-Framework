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
class TagCloudAdapter67Test extends PHPUnit_Framework_TestCase
{
	protected static $config;
	protected static $encodingHandler;
	protected static $paramsParser;
	protected static $log;
	
	protected $dataProvider;
	protected $tagCloudAdapter;
	
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
		$this->tagCloudAdapter = FF::getInstance('xml67/tagCloudAdapter', $this->dataProvider, self::$paramsParser, self::$encodingHandler, self::$log);
	}
	
	public function testTagCloudLoading()
	{
		$tagCloud = $this->tagCloudAdapter->getTagCloud();
		
		$this->assertEquals(10, count($tagCloud), 'wrong number of tag queries delivered');
		$this->assertInstanceOf('FACTFinder_TagQuery', $tagCloud[0], 'tag cloud element is no tag query');
		$this->assertNotEmpty($tagCloud[0], 'first tag query is empty');
		$this->assertEquals(77, $tagCloud[0]->getSearchCount(), 'wrong search count for first tag query delivered');
	}
}