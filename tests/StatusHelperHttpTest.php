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
class StatusHelperHttpTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var FACTFinder_Configuration
     */
    protected static $config;
    protected static $encodingHandler;

    /**
     * @var FACTFinder_ParametersParser
     */
    protected static $paramsParser;

    protected static $log;

    /**
     * @var FACTFinder_Http_StatusHelper
     */
    protected $statusHelper;

    public static function setUpBeforeClass()
    {
        $zendConfig = FF::getInstance('zend/config/xml', RESOURCES_DIR.DS.'config.xml', 'production');
        self::$config = FF::getInstance('configuration', $zendConfig);

        self::$log = FF::getInstance('log4PhpLogger');
        self::$log->configure(RESOURCES_DIR.DS.'log4php.xml');

        self::$encodingHandler = FF::getInstance('encodingHandler', self::$config, self::$log);
        self::$paramsParser = FF::getInstance('parametersParser', self::$config, self::$encodingHandler, self::$log);
    }

    public function setUp()
    {
        $this->statusHelper = FF::getInstance(
            'http/statusHelper',
            self::$config,
            self::$log
        );

        FACTFinder_Http_ParallelDataProvider::loadAllData();
    }

    public function testGetVersionNumber()
    {
        $actualVersionNumber = $this->statusHelper->getVersionNumber();

        $this->assertEquals(68, $actualVersionNumber);
    }

    public function testGetVersionString()
    {
        $actualVersionNumber = $this->statusHelper->getVersionString();

        $this->assertEquals('6.8', $actualVersionNumber);
    }

    public function testGetStatusCode()
    {
        $statusCode = $this->statusHelper->getStatusCode();

        $this->assertEquals(FFE_OK, $statusCode);
    }
}