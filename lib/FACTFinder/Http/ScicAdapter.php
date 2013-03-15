<?php
/**
 * FACT-Finder PHP Framework
 *
 * @category  Library
 * @package   FACTFinder\Http
 * @copyright Copyright (c) 2012 Omikron Data Quality GmbH (www.omikron.net)
 */

/**
 * http scic adapater
 *
 * @author    Rudolf Batt <rb@omikron.net>
 * @version   $Id: ScicAdapter.php 25893 2010-06-29 08:19:43Z rb $
 * @package   FACTFinder\Http
 */
class FACTFinder_Http_ScicAdapter extends FACTFinder_Default_ScicAdapter
{
    /**
     * init
     */
    protected function init()
	{
		$this->log->info("Initializing new SCIC adapter.");
        $this->getDataProvider()->setType('SCIC.ff');
        $this->getDataProvider()->setCurlOptions(array(
            CURLOPT_CONNECTTIMEOUT => $this->getDataProvider()->getConfig()->getScicConnectTimeout(),
            CURLOPT_TIMEOUT => $this->getDataProvider()->getConfig()->getScicTimeout()
        ));
    }

    /**
     * send tracking
     *
     * @return boolean $success
     */
    public function applyTracking() {
        $success = trim($this->getData());
        return $success == 'true';
    }
}