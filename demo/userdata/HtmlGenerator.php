<?php

/*
 * demonstrative implementation how the FACT-Finder Framework could be used ot create a search result page
 *
 * @author    Rudolf Batt <rb@omikron.net>
 * @revision  $Rev: 12328 $
 * @update    $LastChangedDate: 2008-12-10 18:21:40 +0100 (Mi, 10 Dez 2008) $
 */
class HtmlGenerator
{
	private $searchAdapter;
	private $tagCloudAdapter;
    private $templateDir;
    private $paramsParser;
    private $i18n;
	private $config;
	private $log;

    public function __construct($searchAdapter, $tagCloudAdapter, $paramsParser, $config, $log, $templateDir)
    {
    	$this->searchAdapter   = $searchAdapter;
		$this->tagCloudAdapter = $tagCloudAdapter;
    	$this->paramsParser    = $paramsParser;
    	$this->config          = $config;
		$this->log			   = $log;
    	$this->templateDir     = $templateDir;
    }

    /**
     * returns a i18n object
     *
     * @return i18n
    **/
    public function getI18n()
    {
        if ($this->i18n == null) {
        	$params = $this->paramsParser->getRequestParams();

            //init i18n
            $defaultLang = $this->config->getLanguage();
            if ($defaultLang == null || $defaultLang == '') {
            	$defaultLang = 'de_de';
            }
            if (isset($params['lang'])) {
                $lang = $params['lang'];
            }
            $this->i18n = new i18n($defaultLang, I18N_DIR, $defaultLang, FF::getInstance('encodingHandler', $this->config));
        }
        return $this->i18n;
    }

    private function getTemplate($name) {
    	return $this->templateDir.DS.$name.'.phtml';
    }

    public function getHtmlCode()
    {
    	ob_start();

		$encoding	= $this->config->getPageContentEncoding();
		$i18n		= $this->getI18n();
		$ffparams	= $this->paramsParser->getFactfinderParams();

		try {
			FACTFinder_Http_ParallelDataProvider::loadAllData();
			$campaigns = $this->searchAdapter->getCampaigns();
			if ($campaigns->hasRedirect()) {
				throw new RedirectException($campaigns->getRedirectUrl());
			}
			
			$status                 = $this->searchAdapter->getStatus();
			$isArticleNumberSearch  = $this->searchAdapter->isArticleNumberSearch();
			$isSearchTimedOut       = $this->searchAdapter->isSearchTimedOut();

			$productsPerPageOptions = $this->searchAdapter->getProductsPerPageOptions();
			$breadCrumbTrail        = $this->searchAdapter->getBreadCrumbTrail();
			$singleWordSearch       = $this->searchAdapter->getSingleWordSearch();
			$paging                 = $this->searchAdapter->getPaging();
			$sorting                = $this->searchAdapter->getSorting();
			$asn                    = $this->searchAdapter->getAsn();
			$result                 = $this->searchAdapter->getResult();
			
			$tagCloud				= $this->tagCloudAdapter->getTagCloud();
			
			$util = FF::getInstance('util', $ffparams, $this->searchAdapter);

			switch ($status) {
				case FACTFinder_Default_SearchAdapter::RESULTS_FOUND:
					include $this->getTemplate('index');
					break;
				case FACTFinder_Default_SearchAdapter::NOTHING_FOUND:
					$message = $i18n->msg('nomatch_head_searchFor', htmlspecialchars($ffparams->getQuery()));
					include $this->getTemplate('noMatch');
					break;
				case FACTFinder_Default_SearchAdapter::NO_RESULT:
					$error = $i18n->msg('error_noResult');
					include $this->getTemplate('error');
					break;
				default:
					throw new Exception('No result (unknown status)');
			}
		} catch (Exception $e) {
			if ($e instanceof RedirectException) {
				$this->doRedirect($e->getMessage());
			} else if($e->getMessage() == FACTFinder_Default_SearchAdapter::NO_QUERY) {
				$message = $i18n->msg('error_noQuery');
				include $this->getTemplate('noMatch');
			} else {
				$error = $e->getMessage();
				include $this->getTemplate('error');
			}
		}
    	return ob_get_clean();
    }

    private function doRedirect($url)
    {
    	if (!headers_sent()) {
                header('Location: '.$url);
        } else {
                echo '<meta http-equiv="refresh" content="0; URL='.$url.'"> <a href="'.$url.'"></a>';
        }
    }
}

// internal class are not allowed in php :(
class RedirectException extends Exception{}