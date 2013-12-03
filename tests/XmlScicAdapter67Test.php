<?php
class XmlScicAdapter67Test extends PHPUnit_Framework_TestCase
{
    protected static $config;
    protected static $encodingHandler;
    protected static $paramsParser;
    protected static $log;

    protected $dataProvider;
    /**
     * @var FACTFinder_Http_ScicAdapter
     */
    protected $scicAdapter;

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
        $this->scicAdapter = FF::getInstance('xml67/scicAdapter', $this->dataProvider, self::$paramsParser, self::$encodingHandler, self::$log);
    }

    public function testTrackClick()
    {
        $result = $this->scicAdapter->trackClick(
            1,
            md5(2),
            'query',
            3,
            4,
            5,
            100,
            'product',
            9,
            15
        );

        $this->assertTrue($result);
    }

    public function testTrackCart()
    {
        $result = $this->scicAdapter->trackCart(
            1,
            md5(2),
            3,
            4.00,
            5
        );

        $this->assertTrue($result);
    }

    public function testTrackCheckout()
    {
        $result = $this->scicAdapter->trackCheckout(
            1,
            md5(2),
            3,
            4.00,
            5
        );

        $this->assertTrue($result);
    }

    public function testTrackRecommendationClick()
    {
        $result = $this->scicAdapter->trackRecommendationClick(
            1,
            md5(2),
            3
        );

        $this->assertTrue($result);
    }
}
