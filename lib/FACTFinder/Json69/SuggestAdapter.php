<?php
/**
 * FACT-Finder PHP Framework
 *
 * @category  Library
 * @package   FACTFinder\Json69
 * @copyright Copyright (c) 2012
 */

/**
 * suggest adapter using the json interface. expects a json formated string from the dataprovider
 *
 * @package   FACTFinder\Json69
 */
class FACTFinder_Json69_SuggestAdapter extends FACTFinder_Http_SuggestAdapter
{

	/**
	 * @param $callbackName
	 * @return string
	 */
	public function getSuggestions ($callbackName) {
		$jsonCallback = $callbackName . '('
			. $this->getData() . ')';

		return $jsonCallback;
	}


	protected function getData () {
		$this->getDataProvider()->setParam('format', 'json');
		return $this->getDataProvider()->getData();
	}

}
