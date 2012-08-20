<?php
/**
 * FACT-Finder PHP Framework
 *
 * @category  Test
 * @package   FACTFinder\Common
 * @copyright Copyright (c) 2012 Omikron Data Quality GmbH (www.omikron.net)
 */

 /**
  * self-explanatory test
  */
class ParameterParserTest extends PHPUnit_Framework_TestCase
{
	protected $paramsParser;
	
	public function setUp()
	{
		$log = FF::getInstance('log4PhpLogger');
		$log->configure(RESOURCES_DIR.DS.'log4php.xml');
		
		$zendConfig = FF::getSingleton('zend/config/xml', RESOURCES_DIR.DS.'config.xml', 'production');
		$config = FF::getSingleton('configuration', $zendConfig);
		$encodingHandler = FF::getInstance('encodingHandler', $config, $log);
		
		$this->paramsParser = FF::getInstance('parametersParser', $config, $encodingHandler, $log);
	}
	
	public function testRequestParsing() {
		$_SERVER['QUERY_STRING'] = 'blob=blib&gnarf=argl';
		$requestParams = $this->paramsParser->getRequestParams();
		
		$this->assertNotEmpty($requestParams, 'request params couldn`t be parsed');
		$this->assertEquals('blib', $requestParams['blob']);
		$this->assertEquals('argl', $this->paramsParser->getRequestParam('gnarf'));
		$this->assertNull($this->paramsParser->getRequestParam('foo'));
		$this->assertEquals('bar', $this->paramsParser->getRequestParam('foo', 'bar'));
	}
	
	public function testEncodingParameters() {
		$input = array('categoryROOT/Foo'.urlencode('/').'Bar' => 'Test/Me');
		$this->assertEquals('target?categoryROOT%2FFoo%252FBar=Test%2FMe', $this->paramsParser->createPageLink($input, array(), 'target'));
		
		//TODO: use muteable configuration object to control output encoding
		$input = array('white space' => 'möp');
		$this->assertEquals('target?white+space=m%F6p', $this->paramsParser->createPageLink($input, array(), 'target'));
	}
}