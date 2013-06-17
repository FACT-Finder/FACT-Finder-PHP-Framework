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
class JsonTagCloudAdapter69Test extends PHPUnit_Framework_TestCase
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
        $this->dataProvider->setFileLocation(RESOURCES_DIR.DS.'responses'.DS.'json69');
        $this->dataProvider->setFileExtension(".json");
        $this->tagCloudAdapter = FF::getInstance('json69/tagCloudAdapter', $this->dataProvider, self::$paramsParser, self::$encodingHandler, self::$log);
    }

    public function testTagCloudLoading()
    {
        $tagCloud = $this->tagCloudAdapter->getTagCloud();
		
		$this->assertEquals(5, count($tagCloud), 'wrong number of tag queries delivered');
		$this->assertInstanceOf('FACTFinder_TagQuery', $tagCloud[0], 'tag cloud element is no tag query');
		$this->assertNotEmpty($tagCloud[0], 'first tag query is empty');
        $this->assertEquals(0.561, $tagCloud[0]->getWeight(), 'wrong weight delivered for first tag query', 0.0001);
        $this->assertEquals(1266, $tagCloud[0]->getSearchCount(), 'wrong search count delivered for first tag query');
        $this->assertEquals("28+zoll+damen", $tagCloud[0]->getValue(), 'wrong query delivered for first tag query');
    }
}