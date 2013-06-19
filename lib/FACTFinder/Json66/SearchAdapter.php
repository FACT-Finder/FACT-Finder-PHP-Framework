<?php
/**
 * search adapter using the json interface. expects a json formated string from the dataprovider
 */
class FACTFinder_Json66_SearchAdapter extends FACTFinder_Default_SearchAdapter
{
    protected $status = null;
    protected $isArticleNumberSearch;
    private $jsonData;

    /**
     * init
     */
    protected function init()
    {
        $this->log->info("Initializing new search adapter.");
        $this->getDataProvider()->setParam('format', 'json');
        $this->getDataProvider()->setType('Search.ff');
    }
    
    /**
     * try to parse data as json
     *
     * @throws Exception of data is no valid JSON
     * @return stdClass
     */
    protected function getData()
    {
        if($this->jsonData === null)
        {
            $this->jsonData = json_decode(parent::getData(), true); // the second parameter turns JSON-objects into associative arrays which makes extracting the record fields easier
            if ($this->jsonData === null)
                throw new InvalidArgumentException("json_decode() raised error ".json_last_error());
        }
        return $this->jsonData;
    }

    /**
     * get status of the article number search
     *
     * @return string status
     **/
    public function getArticleNumberSearchStatus()
    {
        if ($this->articleNumberSearchStatus == null) {

            $this->isArticleNumberSearch = false;
            $this->articleNumberSearchStatus = self::NO_RESULT;

            if ($this->getStatus() != self::NO_RESULT) {
                $this->loadArticleNumberSearchInformations();
            }
        }
        return $this->articleNumberSearchStatus;
    }

    /**
     * returns true if the search was an article number search
     *
     * @return boolean isArticleNumberSearch
     **/
    public function isArticleNumberSearch()
    {
        if ($this->isArticleNumberSearch === null) {

            $this->isArticleNumberSearch = false;

            if ($this->getStatus() != self::NO_RESULT) {
                $this->loadArticleNumberSearchInformations();
            }
        }
        return $this->isArticleNumberSearch;
    }

    /**
     * fetch article number search status from the xml result
     *
     * @return void
     */
    private function loadArticleNumberSearchInformations()
    {
        $jsonData = $this->getData();
        switch ($jsonData["searchResult"]["articleNumberSearchStatus"]) {
            case 'nothingFound':
                $this->isArticleNumberSearch = true;
                $this->articleNumberSearchStatus = self::NOTHING_FOUND;
                break;
            case 'resultsFound':
                $this->isArticleNumberSearch = true;
                $this->articleNumberSearchStatus = self::RESULTS_FOUND;
                break;
            case 'noArticleNumberSearch':
            default:
                $this->isArticleNumberSearch = false;
                $this->articleNumberSearchStatus = self::NO_RESULT;
        }
    }

    /**
     * returns true if the search-process was aborted because of a timeout
     *
     * @return boolean true if search timed out
     **/
    public function isSearchTimedOut()
    {
        $jsonData = $this->getData();
        return $jsonData['searchResult']['timedOut'];
    }

    /**
     * get search status
     *
     * @return string status
     **/
    public function getStatus()
    {
        $jsonData = $this->getData();
        if ($this->status == null) {
            switch ($jsonData['searchResult']['resultStatus']) {
                case 'nothingFound':
                    $this->status = self::NOTHING_FOUND;
                    break;
                case 'resultsFound':
                    $this->status = self::RESULTS_FOUND;
                    break;
                default:
                    $this->status = self::NO_RESULT;
            }
        }
        return $this->status;
    }

    protected function createSearchParams()
    {
        $breadCrumbTrail = $this->getBreadCrumbTrail();
        if (sizeof($breadCrumbTrail) > 0) {
            $paramString = $breadCrumbTrail[sizeof($breadCrumbTrail) - 1]->getUrl();
            $searchParams = $this->getParamsParser()->getFactfinderParamsFromString($paramString);
        } else {
            $searchParams = $this->getParamsParser()->getFactfinderParams();
        }
        return $searchParams;
    }
    /**
     * create result object
     **/
    protected function createResult()
    {
        return $this->getResultFromRawResult($this->getData());
    }

    protected function getResultFromRawResult($jsonData) {
        //init default values
        $result      = array();
        $resultCount = 0;

        $searchResultData = $jsonData['searchResult'];
        
        //load result values from the xml element
        if (!empty($searchResultData['records'])) {
            $resultCount = (int)$searchResultData['resultCount'];
            $encodingHandler = $this->getEncodingHandler();

            $paging = $this->getPaging();
            $positionOffset = ($paging->getCurrentPageNumber() - 1) * $this->getProductsPerPageOptions()->getSelectedOption()->getValue();

            //load result
            $positionCounter = 1;
            foreach($searchResultData['records'] AS $recordData){
                // get current position
                $position = $positionOffset + $positionCounter;
                $positionCounter++;

                $result[] = $this->getRecordFromRawRecord($recordData, $position);
            }
        }
        return FF::getInstance('result', $result, $resultCount);
    }

	protected function getRecordFromRawRecord($recordData, $position)
	{
        $originalPosition = $position;
        
        $fieldValues = $recordData['record'];
        
        if (isset($fieldValues['__ORIG_POSITION__']))
        {
            $originalPosition = (int) $fieldValues['__ORIG_POSITION__'];
            unset($fieldValues['__ORIG_POSITION__']);
        }
        
        $record = FF::getInstance('record',
            strval($recordData['id']),
            $recordData['searchSimilarity'],
            $position,
            $originalPosition,
            $fieldValues
        );

		$record->setSeoPath(strval($recordData['seoPath']));

        foreach($recordData['keywords'] AS $keyword) {
            $record->addKeyword(strval($keyword));
        }
        
		return $record;
	}

    /**
     * @return FACTFinder_Asn
     **/
    protected function createAsn()
    {
        $xmlResult = $this->getData();
        $asn = array();

        if (!empty($xmlResult->asn)) {
            $encodingHandler = $this->getEncodingHandler();
            $params = $this->getParamsParser()->getRequestParams();

            foreach ($xmlResult->asn->group AS $xmlGroup) {
                $group = $this->createGroupInstance($xmlGroup, $encodingHandler);

                //get filters of the current group
                foreach ($xmlGroup->element AS $xmlFilter) {
                    $filter = $this->createFilter($xmlFilter, $group, $encodingHandler, $params);

                    $group->addFilter($filter);
                }
                $asn[] = $group;
            }
        }
        return FF::getInstance('asn', $asn);
    }

    protected function createGroupInstance($xmlGroup, $encodingHandler)
    {
        $groupUnit = '';
        if (isset($xmlGroup->attributes()->unit)) {
            $groupUnit = strval($xmlGroup->attributes()->unit);
        }

        return FF::getInstance('asnGroup',
            array(),
            $encodingHandler->encodeServerContentForPage((string)$xmlGroup->attributes()->name),
            $encodingHandler->encodeServerContentForPage((string)$xmlGroup->attributes()->detailedLinks),
            $encodingHandler->encodeServerContentForPage($groupUnit),
            $this->getGroupStyle($xmlGroup)
        );
    }

    protected function getGroupStyle($xmlGroup)
    {
        $style = strval($xmlGroup->attributes()->style);
        return $style == 'SLIDER' ? $style : 'DEFAULT';
    }

    protected function createFilter($xmlFilter, $group, $encodingHandler, $params)
    {
        $filterLink = $this->createLink($xmlFilter);

        if ($group->isSliderStyle()) {
            // get last (empty) parameter from the search params property
            $params = $this->getParamsParser()->parseParamsFromResultString(trim($xmlFilter->searchParams));
            end($params);
            $filterLink .= '&' . key($params) . '=';

            $filter = FF::getInstance('asnSliderFilter',
                $filterLink,
                strval($xmlFilter->attributes()->absoluteMin),
                strval($xmlFilter->attributes()->absoluteMax),
                strval($xmlFilter->attributes()->selectedMin),
                strval($xmlFilter->attributes()->selectedMax),
                isset($xmlFilter->attributes()->field) ? strval($xmlFilter->attributes()->field) : ''
            );
        } else {
            $filter = FF::getInstance('asnFilterItem',
                $encodingHandler->encodeServerContentForPage(trim($xmlFilter->attributes()->name)),
                $filterLink,
                strval($xmlFilter->attributes()->selected) == 'true',
                strval($xmlFilter->attributes()->count),
                strval($xmlFilter->attributes()->clusterLevel),
                strval($xmlFilter->attributes()->previewImage),
                isset($xmlFilter->attributes()->field) ? strval($xmlFilter->attributes()->field) : ''
            );
        }

        return $filter;
    }
    
    protected function createLink($item)
    {
        return $this->getParamsParser()->createPageLink(
            $this->getParamsParser()->parseParamsFromResultString(trim($item['searchParams']))
        );
    }

    /**
     * @return array of FACTFinder_SortItem objects
     **/
    protected function createSorting()
    {
        $sorting = array();
        $jsonData = $this->getData();

        $encodingHandler = $this->getEncodingHandler();
        foreach ($jsonData['searchResult']['sortsList'] AS $sortItemData) {
            $sortLink = $this->createLink($sortItemData);
            
            $sorting[] = FF::getInstance('item',
                $encodingHandler->encodeServerContentForPage(trim($sortItemData['description'])),
                $sortLink,
                $sortItemData['selected']
            );
        }
        return $sorting;
    }

    /**
     * @return array of FACTFinder_Item objects
     **/
    protected function createPaging()
    {
        $paging = null;
        $jsonData = $this->getData();
        $pagingData = $jsonData['searchResult']['paging'];
        if (!empty($pagingData)) {
            $paging = FF::getInstance('paging',
                $pagingData['currentPage'],
                $pagingData['pageCount'],
                $this->getParamsParser()
            );
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
        $jsonData = $this->getData();
        
        if (!empty($jsonData['searchResult']['resultsPerPageList']))
        {
            $defaultOption = -1;
            $selectedOption = -1;
            $options = array();
            foreach ($jsonData['searchResult']['resultsPerPageList'] AS $optionData) {
                $value = $optionData['value'];
                
                if($optionData['default'])
                    $defaultOption = $value;
                if($optionData['selected'])
                    $selectedOption = $value;
                
                $url = $this->getParamsParser()->createPageLink(
                    $this->getParamsParser()->parseParamsFromResultString(trim($optionData['searchParams']))
                );
                $options[$value] = $url;
            }
            $pppOptions = FF::getInstance('productsPerPageOptions', $options, $defaultOption, $selectedOption);
        }
        return $pppOptions;
    }

    /**
     * @return array of FACTFinder_BreadCrumbItem objects
     */
    protected function createBreadCrumbTrail()
    {
        $breadCrumbTrail = array();
        $jsonData = $this->getData();
        
        $breadCrumbTrailData = $jsonData['searchResult']['breadCrumbTrailItems'];
        
        $encodingHandler = $this->getEncodingHandler();

        $i = 1;
        foreach($breadCrumbTrailData as $breadCrumbData)
        {
            $link = $this->createLink($breadCrumbData);
            
            $fieldName = '';
            
            $type = $encodingHandler->encodeServerContentForPage($breadCrumbData['type']);
            
            if ($type == 'filter') {
                $fieldName = $encodingHandler->encodeServerContentForPage($breadCrumbData['associatedFieldName']);
            }
            
            $breadCrumbTrail[] = FF::getInstance('breadCrumbItem',
                $encodingHandler->encodeServerContentForPage(trim($breadCrumbData['text'])),
                $link,
                ($i == count($breadCrumbTrailData)),
                $type,
                $fieldName,
                '' // The JSON response does not have a separate field for the unit but instead includes
                   // it in the "text" field.
            );
            ++$i;
        }
        
        return $breadCrumbTrail;
    }


    /**
     * @return array of FACTFinder_Campaign objects
     */
    protected function createCampaigns()
    {
        $campaigns = array();
        $xmlResult = $this->getData();

        if (!empty($xmlResult->campaigns)) {
            $encodingHandler = $this->getEncodingHandler();

            foreach ($xmlResult->campaigns->campaign AS $xmlCampaign) {
                //get redirect
                $redirectUrl = '';
                if (!empty($xmlCampaign->target->destination)) {
                    $redirectUrl = $encodingHandler->encodeServerUrlForPageUrl(strval($xmlCampaign->target->destination));
                }

                $campaign = FF::getInstance('campaign',
                    $encodingHandler->encodeServerContentForPage(strval($xmlCampaign->attributes()->name)),
                    $encodingHandler->encodeServerContentForPage(strval($xmlCampaign->attributes()->category)),
                    $redirectUrl
                );

                //get feedback
                if (!empty($xmlCampaign->feedback)) {
                    $feedback = array();
                    foreach ($xmlCampaign->feedback->text as $text) {
                        $nr = intval(trim($text->attributes()->nr));
                        $feedback[$nr] = $encodingHandler->encodeServerContentForPage((string)$text);
                    }
                    $campaign->addFeedback($feedback);
                }

                //get pushed products
                if (!empty($xmlCampaign->pushedProducts)) {
                    $pushedProducts = array();
                    foreach ($xmlCampaign->pushedProducts->product AS $xmlProduct) {
                        $product = FF::getInstance('record', $xmlProduct->attributes()->id, 100);

                        // fetch product values
                        $fieldValues = array();
                        foreach ($xmlProduct->field AS $current_field) {
                            $currentFieldname = (string)$current_field->attributes()->name;
                            $fieldValues[$currentFieldname] = (string)$current_field;
                        }
                        $product->setValues($encodingHandler->encodeServerContentForPage($fieldValues));
                        $pushedProducts[] = $product;
                    }
                    $campaign->addPushedProducts($pushedProducts);
                }

                $campaigns[] = $campaign;
            }
        }
        $campaignIterator = FF::getInstance('campaignIterator', $campaigns);
        return $campaignIterator;
    }

    /**
     * @return array of FACTFinder_SingleWordSearchItem objects
     */
    protected function createSingleWordSearch()
	{
        $xmlResult = $this->getData();
        $singleWordSearch = array();
        if (isset($xmlResult->singleWordSearch)) {
            $encodingHandler = $this->getEncodingHandler();
            foreach ($xmlResult->singleWordSearch->item AS $item) {
                $query = $encodingHandler->encodeServerContentForPage(strval($item->attributes()->word));
                $singleWordSearchItem = FF::getInstance('singleWordSearchItem',
                    $query,
                    $this->getParamsParser()->createPageLink(array('query' => $query)),
                    intval(trim($item->attributes()->count))
                );

				//add preview records
				if (isset($item->record)) {
					$position = 1;
					foreach($item->record AS $rawRecord) {
						$record = $this->getRecordFromRawRecord($rawRecord, $position);
						$singleWordSearchItem->addPreviewRecord($record);
						$position++;
					}
				}

				$singleWordSearch[] = $singleWordSearchItem;
            }
        }
        return $singleWordSearch;
    }

    /**
     * get error if there is one
     *
     * @return string if error exists, else null
     */
    public function getError()
    {
        $error = null;
        $xmlResult = $this->getData();
        if (!empty($xmlResult->error)) {
            $error = trim(strval($xmlResult->error));
        }
        return $error;
    }

    /**
     * get stacktrace if there is one
     *
     * @return string if stacktrace exists, else null
     */
    public function getStackTrace()
    {
        $stackTrace = null;
        $xmlResult = $this->getData();
        if (!empty($xmlResult->stacktrace)) {
            $stackTrace = trim(strval($xmlResult->stacktrace));
        }
        return $stackTrace;
    }
}