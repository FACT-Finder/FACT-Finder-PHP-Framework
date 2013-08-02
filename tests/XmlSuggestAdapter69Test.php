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
class XmlSuggestAdapter69Test extends PHPUnit_Framework_TestCase
{
    protected static $config;
    protected static $encodingHandler;
    protected static $paramsParser;
    protected static $log;

    protected $dataProvider;
    protected $suggestAdapter;

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
        $this->dataProvider->setFileLocation(RESOURCES_DIR.DS.'responses'.DS.'xml69');
        $this->suggestAdapter = FF::getInstance('xml69/suggestAdapter', $this->dataProvider, self::$paramsParser, self::$encodingHandler, self::$log);
    }

    public function testSuggestionLoading()
    {
        $this->dataProvider->setParam('query', 'fahrrad');
        $this->dataProvider->setParam('channel', 'de');

        $suggestions = $this->suggestAdapter->getSuggestions();

        $this->assertGreaterThan(0, count($suggestions), 'no suggest queries delivered');
        $this->assertInstanceOf('FACTFinder_SuggestQuery', $suggestions[0], 'suggestion element is no suggest query');
        $this->assertNotEmpty($suggestions[0], 'first suggest query is empty');
    }
}