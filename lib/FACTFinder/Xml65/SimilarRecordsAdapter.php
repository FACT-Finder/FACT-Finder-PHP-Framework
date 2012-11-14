<?php
/**
 * FACT-Finder PHP Framework
 *
 * @category  Library
 * @package   FACTFinder\Xml66
 * @copyright Copyright (c) 2012 Omikron Data Quality GmbH (www.omikron.net)
 */

/**
 * similar records adapter using the xml interface
 *
 * @author    Martin Buettner <martin.buettner@omikron.net>
 * @version   $Id: SimilarRecordsAdapter.php 42804 2012-01-20 10:46:43Z mb $
 * @package   FACTFinder\Xml66
 */
class FACTFinder_Xml65_SimilarRecordsAdapter extends FACTFinder_Abstract_SimilarRecordsAdapter
{
	/**
     * @param string id of the product which should be used to get similar attributes
     * @return array $similarAttributes of strings (field names as keys)
     **/
    protected function createSimilarAttributes() {
        $this->log->debug("Similar records not supported before FACT-Finder 6.6!");
        $similarAttributes = array();
        return $similarAttributes;
    }

    /**
     * @param string id of the product which should be used to get similar records
     * @return array $similarRecords list of FACTFinder_Record items
     **/
    protected function createSimilarRecords() {
        $this->log->debug("Similar records not supported before FACT-Finder 6.6!");
        $similarRecords = array();
        return $similarRecords;
    }
}
