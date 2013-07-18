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
    protected $refKey = null;

    protected function createLink($item) {
        if ($this->refKey == null)
            $this->refKey = $this->getResultFromRawResult($this->getData())->getRefKey();

        return $this->getParamsParser()->createPageLink(
            $this->getParamsParser()->parseParamsFromResultString(trim($item->searchParams)),
            array('sourceRefKey' => $this->refKey)
        );
    }

    protected function getResultFromRawResult($xmlResult) {
        $result = parent::getResultFromRawResult($xmlResult);

        if (isset($xmlResult->refKey)) {
            $result->setRefKey(strval($xmlResult->refKey));
        }

        return $result;
    }

    /**
     * @return array of FACTFinder_Item objects
     **/
    protected function createPaging()
    {
        $paging = null;
        $xmlResult = $this->getData();

        if (!empty($xmlResult->paging)) {
            $paging = FF::getInstance('paging',
                intval(trim($xmlResult->paging->attributes()->currentPage)),
                intval(trim($xmlResult->paging->attributes()->pageCount)),
                $this->getParamsParser()
            );
            if (isset($xmlResult->refKey))
                $paging->setSourceRefKey((string) $xmlResult->refKey);
        } else {
            $paging = FF::getInstance('paging', 1, 1, $this->getParamsParser());
        }
        return $paging;
    }

    /**
     * @return FACTFinder_ProductsPerPageOptions
     */
    protected function createProductsPerPageOptions()
    {
        $pppOptions = array(); //default
        $xmlResult = $this->getData();

        if (!empty($xmlResult->productsPerPageOptions)) {
            $defaultOption = intval(trim($xmlResult->productsPerPageOptions->attributes()->default));
            $selectedOption = intval(trim($xmlResult->productsPerPageOptions->attributes()->selected));

            $options = array();
            foreach ($xmlResult->productsPerPageOptions->option AS $option) {
                $value = intval(trim($option->attributes()->value));
                $searchParams = $this->getParamsParser()->parseParamsFromResultString(trim($option->searchParams));
                $searchParams['sourceRefKey'] = (string) $xmlResult->refKey;
                $url = $this->getParamsParser()->createPageLink($searchParams);
                $options[$value] = $url;
            }
            $pppOptions = FF::getInstance('productsPerPageOptions', $options, $defaultOption, $selectedOption);
        }
        return $pppOptions;
    }
}