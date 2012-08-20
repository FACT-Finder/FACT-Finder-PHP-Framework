<?php
/**
 * FACT-Finder PHP Framework
 *
 * @category  Test
 * @package   FACTFinder\Common
 * @copyright Copyright (c) 2012 Omikron Data Quality GmbH (www.omikron.net)
 */

 /**
  * bootstrap file for unit tests
  */
if (!defined('DS'))				define('DS', DIRECTORY_SEPARATOR);
if (!defined('TEST_DIR'))		define('TEST_DIR', dirname(__FILE__));
if (!defined('LIB_DIR'))		define('LIB_DIR', dirname(TEST_DIR).DS.'lib');
if (!defined('RESOURCES_DIR'))	define('RESOURCES_DIR', TEST_DIR.DS.'resources');

require_once LIB_DIR.DS.'FACTFinder'.DS.'Loader.php';
