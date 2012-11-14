<?php
/**
 * FACT-Finder PHP Framework
 *
 * @category  Library
 * @package   FACTFinder\Xml66
 * @copyright Copyright (c) 2012 Omikron Data Quality GmbH (www.omikron.net)
 */

/**
 * product comparison adapter using the xml interface
 *
 * @author    Martin Buettner <martin.buettner@omikron.net>
 * @version   $Id: SimilarRecordsAdapter.php 42955 2012-01-25 17:04:47Z mb $
 * @package   FACTFinder\Xml66
 */
class FACTFinder_Xml65_CompareAdapter extends FACTFinder_Abstract_CompareAdapter
{
	/**
     * @return array $comparableAttributes of strings (field names as keys, hasDifferences as values)
     **/
    protected function createComparableAttributes() {
        $this->log->debug("Product comparison not supported before FACT-Finder 6.6!");
		$comparableAttributes = array();
        return $comparableAttributes;
    }

    /**
     * @return array $comparedRecords list of FACTFinder_Record items
     **/
    protected function createComparedRecords() {
        $this->log->debug("Product comparison not supported before FACT-Finder 6.6!");
		$comparedRecords = array();
        return $comparedRecords;
    }
}
