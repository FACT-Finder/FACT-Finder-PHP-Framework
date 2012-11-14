<?php
/**
 * FACT-Finder PHP Framework
 *
 * @category  Library
 * @package   FACTFinder\Xml67
 * @copyright Copyright (c) 2012 Omikron Data Quality GmbH (www.omikron.net)
 */

/**
 * product campaign adapter using the xml interface
 *
 * @author    Rudolf Batt <rb@omikron.net>, Martin Buettner <martin.buettner@omikron.net>
 * @version   $Id: ProductCampaignAdapter.php 43440 2012-02-08 12:42:13Z martin.buettner $
 * @package   FACTFinder\Xml67
 */
class FACTFinder_Xml65_ProductCampaignAdapter extends FACTFinder_Abstract_ProductCampaignAdapter
{	
    /**
     * @return array of FACTFinder_Campaign objects
     */
    protected function createCampaigns()
    {
        $this->log->debug("Product Campaigns not supported before FACT-Finder 6.7.");
        $campaigns = array();
        $campaignIterator = FF::getInstance('campaignIterator', $campaigns);
        return $campaignIterator;
    }
}
	