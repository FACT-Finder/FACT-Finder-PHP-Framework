<?php
/**
 * initscript for the FACT-Finder PHP Framework demo using render functions
 *
 * @author    Rudolf Batt <rb@omikron.net>, Martin Buettner <martin.buettner@omikron.net>
 * @update    $LastChangedDate: $
 **/

require_once 'init.php';

// used for campaign redirects
class RedirectException extends Exception { }

$log = FF::getInstance('log4PhpLogger');
$log->configure(USERDATA_DIR.DS.'log4php.xml');
FF::setLogger($log);
// if required a second logger with a different configuration can be created to handle logging outside of the library differently

// construct application
$searchAdapter = getSearchAdapter('xml69', $log);
$tagCloudAdapter = getTagCloudAdapter('xml69', $log);

// get data from FACT-Finder
$config 		= FF::getSingleton('configuration');
$paramsParser 	= FF::getSingleton('parametersParser');

$encoding	= $config->getPageContentEncoding();
$i18n		= getI18n($config, $paramsParser);
$ffparams	= $paramsParser->getFactfinderParams();

// This sets all variables needed to display the shop and also takes care of some basic routing
try {
	$campaigns = $searchAdapter->getCampaigns();
	if ($campaigns->hasRedirect()) {
		throw new RedirectException($campaigns->getRedirectUrl());
	}

	$status                 = $searchAdapter->getStatus();
	$isArticleNumberSearch  = $searchAdapter->isArticleNumberSearch();
	$isSearchTimedOut       = $searchAdapter->isSearchTimedOut();

	$productsPerPageOptions = $searchAdapter->getProductsPerPageOptions();
	$breadCrumbTrail        = $searchAdapter->getBreadCrumbTrail();
	$singleWordSearch       = $searchAdapter->getSingleWordSearch();
	$paging                 = $searchAdapter->getPaging();
	$sorting                = $searchAdapter->getSorting();
	$asn                    = $searchAdapter->getAsn();
	$result                 = $searchAdapter->getResult();
			
	$tagCloud				= $tagCloudAdapter->getTagCloud();

	$util = FF::getInstance('util', $ffparams, $searchAdapter);
	
	// Demo-session, needed to make tracking work
	$sid = session_id();
	if ($sid == '') session_start();

	switch ($status) {
		case FACTFinder_Default_SearchAdapter::NO_RESULT:
			$message = $i18n->msg('error_noResult');
			break;
		case FACTFinder_Default_SearchAdapter::NOTHING_FOUND:
			$message = $i18n->msg('nomatch_head_searchFor', htmlspecialchars($ffparams->getQuery()));
			break;
		case FACTFinder_Default_SearchAdapter::RESULTS_FOUND:
			break;
		default:
			throw new Exception('No result (unknown status)');
	}
} catch (Exception $e) {
	if ($e instanceof RedirectException) {
		$url = $e->getMessage();
	    if (!headers_sent()) {
                header('Location: '.$url);
        } else {
                echo '<meta http-equiv="refresh" content="0; URL='.$url.'"> <a href="'.$url.'"></a>';
        }
	} elseif ($e->getMessage() == FACTFinder_Default_SearchAdapter::NO_QUERY) {
		$status = FACTFinder_Default_SearchAdapter::NO_RESULT;
		$message = $i18n->msg('error_noQuery');
	} else {
		$status = FACTFinder_Default_SearchAdapter::NO_RESULT;
		$message = $e->getMessage();
	}
}

// run / show view
include TEMPLATE_DIR.DS.'index.phtml';
