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

include LIB_DIR . DS . 'SAI' . DS . 'CurlStub.php';

class DataProviderHttpTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var SAI_CurlStub
     */
    protected $curlStub;

    protected static $config;
    protected static $encodingHandler;

    /**
     * @var FACTFinder_ParametersParser
     */
    protected static $paramsParser;

    protected static $log;

    /**
     * @var FACTFinder_Http_DataProvider
     */
    protected $dataProvider;

    public static function setUpBeforeClass()
    {
        $zendConfig = FF::getSingleton('zend/config/xml', RESOURCES_DIR.DS.'config-httpauth.xml', 'production');
        self::$config = FF::getSingleton('configuration', $zendConfig);

        self::$log = FF::getInstance('log4PhpLogger');
        self::$log->configure(RESOURCES_DIR.DS.'log4php.xml');

        self::$encodingHandler = FF::getInstance('encodingHandler', self::$config, self::$log);
        self::$paramsParser = FF::getInstance('parametersParser', self::$config, self::$encodingHandler, self::$log);
    }

    public function setUp()
    {
        $this->curlStub = new SAI_CurlStub();
        $this->dataProvider = FF::getInstance('http/dataProvider', $this->curlStub, self::$paramsParser->getServerRequestParams(), self::$config, self::$log);
    }

    public function testSetSingleParam()
    {
        $this->dataProvider->setParam('query', 'bmx');

        $actualParams = $this->dataProvider->getParams();

        $this->assertCount(1, $actualParams);
        $this->assertArrayHasKey('query', $actualParams);
        $this->assertEquals('bmx', $actualParams['query']);

        $this->dataProvider->setParam('format', 'xml');

        $actualParams = $this->dataProvider->getParams();

        $this->assertCount(2, $actualParams);
        $this->assertArrayHasKey('format', $actualParams);
        $this->assertEquals('xml', $actualParams['format']);
    }

    public function testSetParams()
    {
        $expectedParams = array(
            'query' => 'bmx',
            'channel' => 'de',
            'verbose' => 'true'
        );

        $this->dataProvider->setParams($expectedParams);

        $actualParams = $this->dataProvider->getParams();

        $this->assertEquals($expectedParams, $actualParams);
    }

    public function testResetParams()
    {
        $this->dataProvider->setParam('query', 'bmx');
        $this->dataProvider->setParam('format', 'xml');

        $expectedParams = array(
            'query' => 'bmx',
            'channel' => 'de',
            'verbose' => 'true'
        );

        $this->dataProvider->setParams($expectedParams);

        $actualParams = $this->dataProvider->getParams();

        $this->assertArrayNotHasKey('format', $actualParams);
        $this->assertEquals($expectedParams, $actualParams);
    }

    public function testUnsetParam()
    {
        $this->dataProvider->setParam('query', 'bmx');
        $this->dataProvider->setParam('format', 'xml');

        $this->dataProvider->unsetParam('format');

        $actualParams = $this->dataProvider->getParams();

        $this->assertCount(1, $actualParams);
        $this->assertArrayHasKey('query', $actualParams);
        $this->assertArrayNotHasKey('format', $actualParams);
    }

    public function testSetArrayParam()
    {
        $this->dataProvider->setArrayParam('productIds', array('123', '456'));

        $actualParams = $this->dataProvider->getParams();

        $this->assertCount(1, $actualParams);
        $this->assertArrayHasKey('productIds', $actualParams);
        $this->assertEquals(array('123', '456'), $actualParams['productIds']);
    }

    public function testHasUrlChanged()
    {
        $this->dataProvider->setType('Search.ff');
        $this->dataProvider->setParam('query', 'bmx');

        $this->assertTrue($this->dataProvider->hasUrlChanged());
        $this->dataProvider->setPreviousUrl($this->dataProvider->getNonAuthenticationUrl());
        $this->assertFalse($this->dataProvider->hasUrlChanged());

        $this->dataProvider->setParam('format', 'xml');

        $this->assertTrue($this->dataProvider->hasUrlChanged());
        $this->dataProvider->setPreviousUrl($this->dataProvider->getNonAuthenticationUrl());
        $this->assertFalse($this->dataProvider->hasUrlChanged());

        $this->dataProvider->setType('WhatsHot.ff');

        $this->assertTrue($this->dataProvider->hasUrlChanged());
        $this->dataProvider->setPreviousUrl($this->dataProvider->getNonAuthenticationUrl());
        $this->assertFalse($this->dataProvider->hasUrlChanged());
    }

    public function testExceptionOnPrematureHttpCodeRetrieval()
    {
        try {
            $this->dataProvider->getLastHttpCode();
        } catch(Exception $e) {
            $this->assertEquals("Cannot return last HTTP code. No request has been sent.", $e->getMessage());
            return;
        }
        $this->assertTrue(false, "Call should have thrown an Exception");
    }

    public function testExceptionOnMissingType()
    {
        try {
            $this->dataProvider->getData();
        } catch(Exception $e) {
            $this->assertEquals('Request type was not set! Cannot send request without knowing the type.', $e->getMessage());
            return;
        }
        $this->assertTrue(false, "Call should have thrown an Exception");
    }

    public function testGetData()
    {
        $requiredOptions = array(
            CURLOPT_URL => 'http://user:userpw@demoshop.fact-finder.de:80/FACT-Finder/WhatsHot.ff?format=xml&do=getTagCloud&channel=de&verbose=true'
        );
        $response = file_get_contents(RESOURCES_DIR . DS . 'responses' . DS . 'misc' . DS . 'WhatsHot-example.xml');
        $info = array(
            CURLINFO_HTTP_CODE => '200'
        );

        $this->curlStub->setResponse($response, $requiredOptions);
        $this->curlStub->setInfo($info, $requiredOptions);

        $this->dataProvider->setType('WhatsHot.ff');
        $this->dataProvider->setParam('format', 'xml');
        $this->dataProvider->setParam('do', 'getTagCloud');

        $this->dataProvider->getData();

        $this->assertEquals(2, floor(intval($this->dataProvider->getLastHttpCode()) / 100));
    }
}