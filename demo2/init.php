<?php
/**
 * initscript for the FACT-Finder PHP Framework demo; makes the basic defines and includes
 *
 * @author    Rudolf Batt <rb@omikron.net>, Martin Buettner <martin.buettner@omikron.net>
 * @revision  $Rev: -1 $
 * @update    $LastChangedDate: $
 **/


define('DS', DIRECTORY_SEPARATOR);

define('DEMO_DIR', dirname(__FILE__));
define('HELPER_DIR', DEMO_DIR.DS.'helpers');
define('I18N_DIR', DEMO_DIR.DS.'i18n');
define('LIB_DIR', dirname(DEMO_DIR).DS.'lib');
define('TEMPLATE_DIR', DEMO_DIR.DS.'templates');
define('USERDATA_DIR', DEMO_DIR.DS.'userdata');

// init
require_once I18N_DIR.DS.'class.i18n.inc.php';
require_once LIB_DIR.DS.'FACTFinder'.DS.'Loader.php';

include HELPER_DIR.DS.'initialization.php';
include HELPER_DIR.DS.'rendering.php';
include HELPER_DIR.DS.'i18n.php';

