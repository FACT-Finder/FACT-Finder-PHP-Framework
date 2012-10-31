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
class UrlBuilderTest extends PHPUnit_Framework_TestCase
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
     * @var FACTFinder_Http_UrlBuilder
     */
    protected $urlBuilder;

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
        $this->urlBuilder = FF::getInstance(
            'http/urlBuilder',
            self::$paramsParser->getServerRequestParams(),
            self::$config,
            self::$log
        );
    }

    public function testSetSingleParam()
    {
        $this->urlBuilder->setParam('query', 'bmx');

        $actualParams = $this->urlBuilder->getParams();

        $this->assertCount(1, $actualParams);
        $this->assertArrayHasKey('query', $actualParams);
        $this->assertEquals('bmx', $actualParams['query']);

        $this->urlBuilder->setParam('format', 'xml');

        $actualParams = $this->urlBuilder->getParams();

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

        $this->urlBuilder->setParams($expectedParams);

        $actualParams = $this->urlBuilder->getParams();

        $this->assertEquals($expectedParams, $actualParams);
    }

    public function testResetParams()
    {
        $this->urlBuilder->setParam('query', 'bmx');
        $this->urlBuilder->setParam('format', 'xml');

        $expectedParams = array(
            'query' => 'bmx',
            'channel' => 'de',
            'verbose' => 'true'
        );

        $this->urlBuilder->setParams($expectedParams);

        $actualParams = $this->urlBuilder->getParams();

        $this->assertArrayNotHasKey('format', $actualParams);
        $this->assertEquals($expectedParams, $actualParams);
    }

    public function testUnsetParam()
    {
        $this->urlBuilder->setParam('query', 'bmx');
        $this->urlBuilder->setParam('format', 'xml');

        $this->urlBuilder->unsetParam('format');

        $actualParams = $this->urlBuilder->getParams();

        $this->assertCount(1, $actualParams);
        $this->assertArrayHasKey('query', $actualParams);
        $this->assertArrayNotHasKey('format', $actualParams);
    }

    public function testSetArrayParam()
    {
        $this->urlBuilder->setArrayParam('productIds', array('123', '456'));

        $actualParams = $this->urlBuilder->getParams();

        $this->assertCount(1, $actualParams);
        $this->assertArrayHasKey('productIds', $actualParams);
        $this->assertEquals(array('123', '456'), $actualParams['productIds']);
    }

    public function testSetAction()
    {
        $expectedAction = 'Test.ff';
        $this->urlBuilder->setAction($expectedAction);

        $this->assertEquals($expectedAction, $this->urlBuilder->getAction());
    }

    public function testNonAuthenticationUrl()
    {
        $expectedAction = 'Test.ff';

        $this->urlBuilder->setAction($expectedAction);
        $this->urlBuilder->setParam('format', 'xml');

        $expectedPath = '/'.self::$config->getContext().'/'.$expectedAction;

        $expectedParams = array(
            'channel' => 'de',
            'format' => 'xml'
        );

        $this->assertUrlEquals(
            $expectedPath,
            $expectedParams,
            null,
            null,
            $this->urlBuilder->getNonAuthenticationUrl()
        );
    }

    public function testSimpleAuthenticationUrl()
    {
        $expectedAction = 'Test.ff';

        $this->urlBuilder->setAction($expectedAction);
        $this->urlBuilder->setParam('format', 'xml');

        $expectedPath = '/'.self::$config->getContext().'/'.$expectedAction;

        $expectedParams = array(
            'channel' => 'de',
            'format' => 'xml',
            'timestamp' => '%d',
            'username' => self::$config->getAuthUser(),
            'password' => md5(self::$config->getAuthPasswort())
        );

        $this->assertUrlEquals(
            $expectedPath,
            $expectedParams,
            null,
            null,
            $this->urlBuilder->getSimpleAuthenticationUrl()
        );
    }

    public function testAdvancedAuthenticationUrl()
    {
        $expectedAction = 'Test.ff';

        $this->urlBuilder->setAction($expectedAction);
        $this->urlBuilder->setParam('format', 'xml');

        $expectedPath = '/'.self::$config->getContext().'/'.$expectedAction;

        $url = $this->urlBuilder->getAdvancedAuthenticationUrl();

        $params = array();
        parse_str(parse_url($url, PHP_URL_QUERY), $params);
        $timestamp = $params['timestamp'];

        $pwHash = md5(self::$config->getAuthPasswort());
        $prefix = self::$config->getAdvancedAuthPrefix();
        $postfix = self::$config->getAdvancedAuthPostfix();

        $expectedParams = array(
            'channel' => 'de',
            'format' => 'xml',
            'timestamp' => $timestamp,
            'username' => self::$config->getAuthUser(),
            'password' => md5($prefix . time() . "000" . $pwHash . $postfix)
        );

        $this->assertUrlEquals(
            $expectedPath,
            $expectedParams,
            null,
            null,
            $this->urlBuilder->getAdvancedAuthenticationUrl()
        );
    }

    public function testHttpAuthenticationUrl()
    {
        $expectedAction = 'Test.ff';

        $this->urlBuilder->setAction($expectedAction);
        $this->urlBuilder->setParam('format', 'xml');

        $expectedPath = '/'.self::$config->getContext().'/'.$expectedAction;

        $expectedParams = array(
            'channel' => 'de',
            'format' => 'xml'
        );

        $this->assertUrlEquals(
            $expectedPath,
            $expectedParams,
            self::$config->getAuthUser(),
            self::$config->getAuthPasswort(),
            $this->urlBuilder->getHttpAuthenticationUrl()
        );
    }

    public function testOverwriteChannel()
    {
        $expectedAction = 'Test.ff';

        $this->urlBuilder->setAction($expectedAction);
        $this->urlBuilder->setParam('format', 'xml');
        $this->urlBuilder->setParam('channel', 'en');

        $expectedPath = '/'.self::$config->getContext().'/'.$expectedAction;

        $expectedParams = array(
            'channel' => 'en',
            'format' => 'xml'
        );

        $this->assertUrlEquals(
            $expectedPath,
            $expectedParams,
            null,
            null,
            $this->urlBuilder->getNonAuthenticationUrl()
        );
    }

    private function assertUrlEquals($expectedPath, $expectedParams, $expectedUser = null, $expectedPassword = null, $actualUrl)
    {
        $this->assertStringMatchesFormat($expectedPath, parse_url($actualUrl, PHP_URL_PATH));
        if($expectedUser !== null)
            $this->assertStringMatchesFormat($expectedUser, parse_url($actualUrl, PHP_URL_USER));
        if($expectedPassword !== null)
            $this->assertStringMatchesFormat($expectedPassword, parse_url($actualUrl, PHP_URL_PASS));

        $actualParams = array();
        parse_str(parse_url($actualUrl, PHP_URL_QUERY), $actualParams);

        $this->assertEquals(count($expectedParams), count($actualParams));

        foreach($expectedParams as $key => $value)
        {
            $this->assertArrayHasKey($key, $actualParams);
            $this->assertStringMatchesFormat($value, $actualParams[$key]);
        }
    }
}