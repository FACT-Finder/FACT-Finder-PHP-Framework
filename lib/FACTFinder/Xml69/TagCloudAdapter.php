<?php
/**
 * FACT-Finder PHP Framework
 *
 * @category  Library
 * @package   FACTFinder\Xml69
 * @copyright Copyright (c) 2012 Omikron Data Quality GmbH (www.omikron.net)
 */

/**
 * tag cloud adapter using the xml interface
 *
 * @package   FACTFinder\Xml69
 */
class FACTFinder_Xml69_TagCloudAdapter extends FACTFinder_Xml68_TagCloudAdapter
{
    public function init() {
        parent::init();
        $this->getDataProvider()->setType('TagCloud.ff');
    }
}
