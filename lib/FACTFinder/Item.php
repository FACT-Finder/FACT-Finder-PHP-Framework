<?php
/**
 * FACT-Finder PHP Framework
 *
 * @category  Library
 * @package   FACTFinder\Common
 * @copyright Copyright (c) 2012 Omikron Data Quality GmbH (www.omikron.net)
 */

/**
 * an factfinder item is a simple selectable item on a website, so it is represented by a value and an url
 * it is NOT defined in this class, what this item affects on the website
 *
 * @author    Rudolf Batt <rb@omikron.net>
 * @version   $Id: Item.php 25893 2010-06-29 08:19:43Z rb $
 * @package   FACTFinder\Common
**/
class FACTFinder_Item
{
    private $value;
    private $url;
    private $isSelected;

    /**
     * @param string value
     * @param string url
     * @param boolean is selected (default: false)
     */
    public function __construct($value, $url, $isSelected = false){
        $this->value = strval($value);
        $this->url = strval($url);
        $this->isSelected = $isSelected == true;
    }

    /**
     * @return string
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * @return boolean
     */
    public function isSelected() {
        return $this->isSelected;
    }

    /**
     * Allows to override the URL of the item
     * @param $url string new URL to set
     */
    public function setUrl($url) {
        $this->url = $url;
    }
}