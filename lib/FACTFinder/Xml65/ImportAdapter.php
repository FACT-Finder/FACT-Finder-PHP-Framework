<?php
/**
 * FACT-Finder PHP Framework
 *
 * @category  Library
 * @package   FACTFinder\Xml65
 * @copyright Copyright (c) 2012 Omikron Data Quality GmbH (www.omikron.net)
 */

/**
 * import adapter using the xml interface
 *
 * @author    Rudolf Batt <rb@omikron.net>
 * @version   $Id: TagCloudAdapter.php 25893 2010-06-29 08:19:43Z rb $
 * @package   FACTFinder\Xml65
 */
class FACTFinder_Xml65_ImportAdapter extends FACTFinder_Abstract_ImportAdapter
{
    /**
     * @return void
     **/
    public function init()
    {
		$this->log->info("Initializing new import adapter.");
        $this->getDataProvider()->setType('Import.ff');
		$this->getDataProvider()->setParam('format', 'xml');
    }

    /**
     * try to parse data as xml
     *
     * @throws Exception of data is no valid XML
     * @return SimpleXMLElement
     */
    protected function getData()
    {
        libxml_use_internal_errors(true);
        return new SimpleXMLElement(parent::getData()); //throws exception on error
    }

    /**
	 * @param  bool   $download        import files will also be updated if true
	 * @param  bool   $suggestImport   do suggest import if true, data import otherwise
     * @return object $report          import report in xml format
     */
    protected function triggerImport($download, $suggestImport = false)
    {
        $this->getDataProvider()->setCurlOptions(array(
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 360
        ));
        
        $this->getDataProvider()->setParam('download', $download ? 'true' : 'false');
		if($suggestImport) $this->getDataProvider()->setParam('type', 'suggest');
		else $this->getDataProvider()->unsetParam('type');
        
        $report = $this->getData();
        return $report;
    }
}
