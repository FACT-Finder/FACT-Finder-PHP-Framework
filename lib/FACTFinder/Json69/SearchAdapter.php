<?php
/**
 * Search adapter using the xml interface.
 */
class FACTFinder_Json69_SearchAdapter extends FACTFinder_Json68_SearchAdapter
{
    protected function createGroupInstance($groupData) {
        $group = parent::createGroupInstance($groupData);

        if (isset($groupData['refKey'])) {
            $group->setRefKey($groupData['refKey']);
        }

        return $group;
    }

    protected function createFilter($elementData, $group) {
        $filter = parent::createFilter($elementData, $group);

        if (isset($elementData['refKey'])) {
            $filter->setRefKey($elementData['refKey']);
        }

        return $filter;
    }

    protected function createLink($item) {
        return $this->getParamsParser()->createPageLink(
            $this->getParamsParser()->parseParamsFromResultString(trim($item['searchParams'])),
            array('refKey' => $item['refKey'])
        );
    }

    protected function getRecordFromRawRecord($recordData, $position) {
        $record = parent::getRecordFromRawRecord($recordData, $position);

        if (isset($recordData['refKey'])) {
            $record->setRefKey($recordData['refKey']);
        }

        return $record;
    }

    protected function getResultFromRawResult($jsonData) {
        $result = parent::getResultFromRawResult($jsonData);

        if (isset($jsonData['searchResult']['refKey'])) {
            $result->setRefKey($jsonData['searchResult']['refKey']);
        }

        return $result;
    }
}