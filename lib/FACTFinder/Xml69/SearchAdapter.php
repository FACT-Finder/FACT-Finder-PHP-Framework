<?php
/**
 * FACT-Finder PHP Framework
 *
 * @category  Library
 * @package   FACTFinder\Xml69
 * @copyright Copyright (c) 2013 Omikron Data Quality GmbH (www.omikron.net)
 */

/**
 * Search adapter using the xml interface.
 *
 * @package   FACTFinder\Xml69
 */
class FACTFinder_Xml69_SearchAdapter extends FACTFinder_Xml68_SearchAdapter
{
    protected function createGroupInstance($xmlGroup, $encodingHandler) {
        $group = parent::createGroupInstance($xmlGroup, $encodingHandler);

        if (isset($xmlGroup->attributes()->refKey)) {
            $group->setRefKey($xmlGroup->attributes()->refKey);
        }

        return $group;
    }

    protected function createFilter($xmlFilter, $group, $encodingHandler, $params) {
        $filter = parent::createFilter($xmlFilter, $group, $encodingHandler, $params);

        if (isset($xmlFilter->attributes()->refKey)) {
            $filter->setRefKey($xmlFilter->attributes()->refKey);
        }

        return $filter;
    }

    protected function getRecordFromRawRecord(SimpleXmlElement $rawRecord, $position) {
        $record = parent::getRecordFromRawRecord($rawRecord, $position);

        if (isset($rawRecord->attributes()->refKey)) {
            $record->setRefKey(strval($rawRecord->attributes()->refKey));
        }

        return $record;
    }

    protected function getResultFromRawResult($xmlResult) {
        $result = parent::getResultFromRawResult($xmlResult);

        if (isset($xmlResult->refKey)) {
            $result->setRefKey(strval($xmlResult->refKey));
        }

        return $result;
    }
}