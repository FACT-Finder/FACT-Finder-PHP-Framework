<?php
/**
 * FACT-Finder PHP Framework
 *
 * @category  Library
 * @package   FACTFinder\Xml69
 * @copyright Copyright (c) 2013 Omikron Data Quality GmbH (www.omikron.net)
 */

/**
 * Tracking adapter for FF 6.9.
 *
 * @package FACTFinder\Xml69
 */
class FACTFinder_Xml69_TrackingAdapter extends FACTFinder_Abstract_Adapter
{
    // A result (product, banner, ASN element, ...) referenced by the key has been displayed.
    const EVENT_DISPLAY = 'display';
    // Visitor has given feedback about a ResultNode. Reference Key is optional.
    const EVENT_FEEDBACK = 'feedback';
    // The user clicked on a product / detail view.
    const EVENT_INSPECT = 'inspect';
    // The user checked the availability of a product.
    const EVENT_AVAILABILITY_CHECK = 'availabilityCheck';
    // The user added an item to the cart.
    const EVENT_CART = 'cart';
    // The user bought or booked a product or service.
    const EVENT_BUY = 'buy';
    // A request of the user could be answered from the shop cache.
    const EVENT_CACHE_HIT = 'cacheHit';
    // A new session has been created for a user.
    const EVENT_SESSION_START = 'sessionStart';

    /**
     * Set up the tracking adapter for 6.9.
     */
    protected function init()
    {
        $this->log->info("Initializing new Tracking adapter.");
        $this->getDataProvider()->setType('Tracking.ff');
        $this->getDataProvider()->setCurlOptions(array(
            CURLOPT_CONNECTTIMEOUT => $this->getDataProvider()->getConfig()->getScicConnectTimeout(),
            CURLOPT_TIMEOUT => $this->getDataProvider()->getConfig()->getScicTimeout()
        ));
    }

    /**
     * Trigger the actual tracking request.
     *
     * @return boolean $success
     */
    public function applyTracking()
    {
        // Is this even correct for the new interface?
        $success = trim($this->getData());
        return $success == 'The event was successfully tracked';
    }

    public function prepareDefaultParams($inputParams) {

        $sid = $inputParams['sid'];
        if (strlen($sid) == 0)
            $sid = session_id();

        $sourceRefKey = $inputParams['sourceRefKey'];
        if (strlen($sourceRefKey) == 0)
            throw new UnexpectedValueException("No sourceRefKey in parameters");

        $params = array('sourceRefKey' => $sourceRefKey, 'sid' => $sid);

        $optParams = array('userId', 'cookieId', 'price', 'amount', 'positive', 'message');
        foreach ($optParams AS $optParam) {
            if (isset($inputParams[$optParam]) && strlen($inputParams[$optParam]) > 0)
                $params[$optParam] = $inputParams[$optParam];
        }

        return $params;
    }

    public function doTrackingFromRequest()
    {
        $params = $this->getParamsParser()->getServerRequestParams();
        $this->prepareDefaultParams($params);
        return $this->applyTracking();
    }

    public function trackEvent($event, $inputParams) {
        $params = prepareDefaultParams($inputParams);

        $events = array(EVENT_DISPLAY, EVENT_FEEDBACK, EVENT_INSPECT, EVENT_AVAILABILITY_CHECK, EVENT_CART, EVENT_BUY,
            EVENT_CACHE_HIT, EVENT_SESSION_START);

        if (!in_array($event, $events, true)) {
            throw new UnexpectedValueException("Event $event not known.");
        }

        $params['event'] = $event;

        $this->getDataProvider()->setParams($params);

        return $this->applyTracking();
    }
}