<?php
/**
 * This script contains rendering functions for all the components used in this FACT-Finder-based shop.
 * They can be called from anywhere within the HTML-template and render a complete "block"-element
 * to the standard output.
 *
 * @author    Rudolf Batt <rb@omikron.net>, Martin Buettner <martin.buettner@omikron.net>
 * @revision  $Rev: -1 $
 * @update    $LastChangedDate: $
 **/

// The function renderProduct(...) will need these
if (!isset($fieldname)) {
	include HELPER_DIR.DS.'fieldnamesConfig.php';
}
 
/**
 * This function renders the (visual) header to the standard output.
 *
 * @param	i18n							$i18n	internationalization object
 **/
 
function renderHeader($i18n) {
	echo '<!-- header -->
		<div id="logo">
			<a href="index.php"><img src="', $i18n->msg('header_imageURL'), '" alt="FACT-Finder" /></a>
		</div>
	';
}
 
/**
 * This function renders the FACT-Finder search box to the standard output.
 *
 * @param	FACTFinder_Parameters			$ffparams	the params object returned by the params parser
 * @param	i18n							$i18n		internationalization object
 **/
function renderSearchBox($ffparams, $i18n) {
	echo '<!-- search box -->
		<div class="suggestFormWrap" >
			<form action="" method="GET" name="search_word_inc">
				<input type="hidden" name="queryFromSuggest" />
				<!-- input type="hidden" name="{* $channelParam *}" value="{* channel *}" / -->
				<input type="text" name="query" id="query"
					value="', getInitialSearchQuery($ffparams, $i18n), '" autocomplete="off" />

				<input type="image" id="submit" src="', $i18n->msg('searchbox_submitButtonURL'), '"/>
				<div id="suggestLayer" class="suggestLayer"></div>
				<script language="JavaScript" type="text/javascript">
				<!--
						var suggest				= new FFSuggest();
						var searchURL			= \'suggest.php\';
						var formname			= \'search_word_inc\';
						var queryParamName		= \'query\';
						var channelParamName	= \'channel\';
						var channel				= \'', $ffparams->getChannel(), '\';
						var divLayername		= \'suggestLayer\';
						var instanceName		= \'suggest\';
						var debugMode			= false;
						var showImages          = false;
						suggest.init(searchURL, formname, queryParamName, divLayername, instanceName, debugMode, channelParamName, channel, showImages);
						document.onmousedown = suggest.hideLayerOutsideCall;
				// -->
				</script>
			</form>
		</div>
	';
}

/**
 * Auxiliary function to render the initial search query.
 *
 * @param	FACTFinder_Parameters			$ffparams	the params object returned by the params parser
 * @param	i18n							$i18n		internationalization object
 * @return  string		initial search query
 **/
function getInitialSearchQuery($ffparams, $i18n) {
	if ($ffparams->getQuery() == '' || $ffparams->isNavigation()) {
		return $i18n->msg("searchbox_defaultText").'" class="default';
	} else {
		return htmlspecialchars($ffparams->getQuery());
	}
}

/**
 * This function renders a column with all the After Search Navigation elements to the standard output.
 *
 * @param	FACTFinder_Asn					$asn		the asn object returned by the search adapter
 * @param	FACTFinder_Parameters			$ffparams	the params object returned by the params parser
 * @param	i18n							$i18n		internationalization object
 * @param	FACTFinder_CampaignIterator		$campaigns	the campaigns object returned by the search adapter
 **/
function renderAsnColumn($asn, $ffparams, $i18n, $campaigns = NULL) {
	if (isset($asn)) {
		foreach($asn AS $group) {
			echo '<div class="asnGroup"> <h3>'.$group->getName().'</h3>';
			if ($group->isDefaultStyle()) {
				$linkCount = 0;
				foreach($group AS $element) {
					if ($element->isSelected()) {
						// show deselect links
						echo '<p class="asnElement selected lvl'. $element->getClusterLevel() .'">
								<a href="'.$element->getUrl().'">'.$element->getValue().' '.$group->getUnit().'<br>
									<span>', $i18n->msg('asn_removeFilter'), '</span></a>
							</p>';
					} else {
						if ($linkCount < $group->getDetailedLinkCount()) {
							// show filters as usual links
							echo '<p class="asnElement lvl'. $element->getClusterLevel() .'">
									<a href="'.$element->getUrl().'">'.$element->getValue().' '.$group->getUnit();
							if (!$ffparams->isNavigation()) {
								echo '<span>('.$element->getMatchCount().')</span>';
							}
							echo '</a></p>';
							$linkCount++;
						} else {
							// show filters as drop down
							if ($linkCount == $group->getDetailedLinkCount()) {
								echo '<select onchange="if (this.value != \'\') {document.location.href=this.value;}">
										<option value="">', $i18n->msg('asn_moreGroupElements', (count($group) - $linkCount)), '</option>';
							}
							echo '<option value="'.$element->getUrl().'">'.$element->getValue().' '.$group->getUnit();
							if (!$ffparams->isNavigation()) {
								echo '('.$element->getMatchCount().')';
							}
							echo '</option>';
							$linkCount++;
						}
					}
				}
				if ($linkCount >= $group->getDetailedLinkCount()) {
					echo '</select>';	
				}
			} elseif ($group->isSliderStyle()) {
				foreach($group AS $slider) {
					echo '<!-- OneTouchSlider setup script -->
					<script>
						// This is needed for currency symbols defined as HTML entities
						function htmlDecode(input){
							var e = document.createElement(\'div\');
							e.innerHTML = input;
							return e.childNodes.length === 0 ? "" : e.childNodes[0].nodeValue;
						}
						
						function oneTouchSliderOnLoad() {
							oneTouchSlider.addSlider({
								hostElementId:\'price-slider\',
								leftBorder:'.$slider->getAbsoluteMin().',
								rightBorder:'.$slider->getAbsoluteMax().',
								currentLeft:'.$slider->getSelectedMin().',
								currentRight:'.$slider->getSelectedMax().',
								unit:htmlDecode(\''.$i18n->msg('product_priceUnit').'\'),
								callback:sliderSelect
							});
						}

						function sliderSelect(left, right) {
							window.location.href="'.$slider->getBaseUrl().'"+left+" - "+right;
						}
					</script>
					';
					echo '<div id="price-slider" style="padding:20px;"> </div>';
				}
			}
			echo '</div>'; // eof group
			if(isset($campaigns)) echo $campaigns->getFeedback('below each asn group');
		}
	}
}

/**
 * Helper function to render the whole advisor tree (only used as debug output)
 **/
function renderAdvisorQuestion($question) {
	echo '<span class="question">'.$question->getText().'</span><br/>';
	echo '<div style="margin-left:10px;">';
	foreach($question->getAnswers() AS $answer) {
		echo '<span class="answer">'.$answer->getText().'</span><br/>';
		if($answer->hasSubquestions()) {
			echo '<div style="margin-left:10px;">';
			foreach($answer->getSubquestions() AS $subquestion) {
				renderAdvisorQuestion($subquestion);
			}
			echo '</div>';
		}
	}
	echo '</div>';
}

/**
 * This function renders advisor campaigns to the standard output.
 *
 * @param	FACTFinder_CampaignIterator		$campaigns	the campaigns object returned by the search adapter
 **/
function renderAdvisorCampaign($campaigns) {
	if ($campaigns->hasAdvisorTree()) {
		echo '<div id="advisorCampaign">';
		echo '<p>Debug-Ausgabe:</p>';
		echo '<div style="margin-left:10px;">';
		foreach($campaigns->getAdvisorTree() AS $question) {
			renderAdvisorQuestion($question);
		}
		echo '</div>';
		echo '</div>';
	}
	
	if ($campaigns->hasActiveQuestions()) {
		echo '<div id="advisorCampaign">';
		foreach($campaigns->getActiveQuestions() AS $question) {
			echo '<p class="question">'.$question->getText().'</p>';
			foreach($question->getAnswers() AS $answer) {
				echo '<a class="answer" href="'.$answer->getParams().'">'.$answer->getText().'</a>';
			}
		}
		echo '</div>';
	}
}

/**
 * This function renders a breadcrumb navigation to the standard output.
 *
 * @param	FACTFinder_Result				$result				the result object returned by the search adapter
 * @param	FACTFinder_Parameters			$ffparams			the params object returned by the params parser
 * @param	i18n							$i18n				internationalization object
 * @param	FACTFinder_BreadCrumbItem[]		$breadCrumbTrail	the breadcrumb array returned by the search adapter
 **/
function renderBreadCrumbs($result, $ffparams, $i18n, $breadCrumbTrail) {
	if (isset($result) && count($result) > 0 && !$ffparams->isNavigation()) {
		$breadCrumbTrailString = '';
		foreach($breadCrumbTrail AS $breadCrumb) {
			if ($breadCrumbTrailString != '') {
				$breadCrumbTrailString .= ' &raquo; ';
			}
			$breadCrumbTrailString .= '<a href="'.$breadCrumb->getUrl().'">'.htmlspecialchars($breadCrumb->getValue()).'</a>';
		}
		echo '<p id="breadCrumbTrail">', $i18n->msg('result_head_searchFor', $breadCrumbTrailString, $result->getFoundRecordsCount()), '</p>';
	}
}

/**
 * This function renders the results of single-word searches to the standard output.
 *
 * @param	FACTFinder_SuggestQuery[]		$singleWordSearch	the suggest-query array for single-word searches returned by the search adapter
 * @param	i18n							$i18n				internationalization object
 **/
function renderSingleWordResults($singleWordSearch, $i18n) {
	echo '<p class="resultSingleQueries">', $i18n->msg('result_badMatchingQueryParts'), '</p>';
	echo '<ul class="resultSingleQueriesList">';
	foreach($singleWordSearch AS $query) {
		echo '<li><a href="'.$query->getUrl().'">'.htmlspecialchars($query->getQuery()).' ('.$query->getHitCount().')</a></li>';
	}
	echo '</ul>';
}

/**
 * This function renders a sort box to the standard output.
 *
 * @param	FACTFinder_SortItem[]			$sorting			the sorting array returned by the search adapter
 * @param	i18n							$i18n				internationalization object
 **/
function renderSortBox($sorting, $i18n) {
	echo '<!-- sort box -->
		<div class="optionBox sortBox">',
			$i18n->msg('result_sort'),
			'  <select onchange="document.location.href=this.value;">', getSortingOptions($sorting, $i18n), '</select>
		</div>
	';
}

/**
 * Auxiliary function to render the sorting options.
 *
 * @param	FACTFinder_SortItem[]			$sorting			the sorting array returned by the search adapter
 * @param	i18n							$i18n				internationalization object
 * @return  string		sorting options
 **/
function getSortingOptions($sorting, $i18n) {
	$output = '';
	foreach ($sorting AS $sortItem) {
		if ($sortItem->isSelected()) {
			$selected = ' selected ';
		} else {
			$selected = '';
		}
		$output .= '<option value="'.$sortItem->getUrl().'"'.$selected.'>'.$sortItem->getValue();
		if ($sortItem->isSelected()) {
			$output .= ' ('.$i18n->msg('sorting_selected').')';
		}
		$output .= '</option>';
	}
	return $output;
}

/**
 * This function renders a page navigation to the standard output.
 *
 * @param	FACTFinder_Paging				$paging				the paging object returned by the search adapter
 * @param	i18n							$i18n				internationalization object
 **/
function renderPaging($paging, $i18n) {
	echo '<div class="paging">';
	
	if (!empty($paging) && $paging->getPageCount() > 1) {
		echo '<!-- paging -->
			<table width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td class="previousPage">', getPreviousPageLink($paging, $i18n), '</td>
					<td class="pageLinks">', getPageLinks($paging), '</td>
					<td class="nextPage">', getNextPageLink($paging, $i18n), '</td>
				</tr>
			</table>
		';
	} else {
		echo $i18n->msg('paging_onePageOnly');
	}
	echo '</div>';
}

/**
 * Auxiliary function to render the previous page link, if applicable.
 *	
 * @param	FACTFinder_Paging				$paging				the paging object returned by the search adapter
 * @param	i18n							$i18n				internationalization object
 * @return  string		previous page link or empty string
 **/
function getPreviousPageLink($paging, $i18n) {
	if ($paging->getPreviousPageLink() != '') {
		return '<a href="'.$paging->getPreviousPageLink().'">'.$i18n->msg('prev_link_text').'</a>';
	}
	return '';
}

/**
 * Auxiliary function to render the next page link, if applicable.
 *
 * @param	FACTFinder_Paging				$paging				the paging object returned by the search adapter
 * @param	i18n							$i18n				internationalization object
 * @return  string		next page link or empty string
 **/
function getNextPageLink($paging, $i18n) {
	if ($paging->getNextPageLink() != '') {
		return '<a href="'.$paging->getNextPageLink().'">'.$i18n->msg('next_link_text').'</a>';
	}
	return '';
}

/**
 * Auxiliary function to render the page links.
 *
 * @param	FACTFinder_Paging		$paging		the paging object returned by the search adapter
 * @return  string		the page links
 **/
function getPageLinks($paging) {
	$output = '';
	foreach ($paging AS $page) {
		if ($page->isSelected()) {
			$output .= '<span class="paging currentPage">'.$page->getValue().'</span>';
		} else {
			$output .= '<a class="paging" href="'.$page->getUrl().'">'.$page->getValue().'</a>';
		}
	}
	return $output;
}

/**
 * This function renders preview images for asn categories (if available) to the standard output.
 *
 * @param	FACTFinder_Asn					$asn				the asn object returned by the search adapter
 * @param	i18n							$i18n				internationalization object
 **/
function renderPreviewImageAsn($asn, $i18n) {
	if (!empty($asn) && $asn->hasPreviewImages()) {
		foreach ($asn AS $group) {
			if ($group->hasPreviewImages()) {
				echo '<div id="asnTop">
						<h3>'.$group->getName().'</h3>';
				$linkCount = 0;
				foreach ($group AS $element) {
					if ($element->isSelected()) {
						continue;
					}
					if ($linkCount < $group->getDetailedLinkCount()) {
						echo '<div class="asnImage">
								<div>
									<a href="'.$element->getUrl().'">
										<img src="'.$element->getPreviewImage().'" alt="" title="'.$element->getValue().'" onerror="imageNotFound(this)" onload="resizePicture(this, 80, 115)" />
									</a>
								</div>
								<p>
									<a href="'.$element->getUrl().'">
										'.$element->getValue().' '.$group->getUnit().' <span>('.$element->getMatchCount().')</span>
									</a>
								</p>
							</div>';
						$linkCount++;
					} else {
						if ($linkCount == $group->getDetailedLinkCount()) {
							echo '<p class="selectLine">
									<select onchange="if (this.value != \'\') {document.location.href=this.value;}">
										<option value="">', $i18n->msg('asn_moreGroupElements', ($group->count() - $linkCount)), '</option>';
						}
						echo '<option value="'.$element->getUrl().'">'.$element->getValue().' '.$group->getUnit().' ('.$element->getMatchCount().')</option>';
						$linkCount++;
					}
				}
				if($linkCount > $group->getDetailedLinkCount()) {
					echo '</select>
					</p>';
				}
				if ($group->count() <= $group->getDetailedLinkCount()){
					echo '<div class="clear"></div>';
				}
				echo '</div>';
				break;
			}
		}
	}
}

/**
 * This function renders a block with all the products that are pushed in the campaign (if any) to the standard output.
 *
 * @param	FACTFinder_Asn					$asn				the asn object returned by the search adapter
 * @param	FACTFinder_Util					$util				a utility object for the current search adapter
 * @param	i18n							$i18n				internationalization object
 * @param	FACTFinder_CampaignIterator		$campaigns			the campaigns object returned by the search adapter
 **/
function renderPushedProducts($asn, $util, $i18n, $campaigns = NULL) {
	if ((empty($asn) || !$asn->hasPreviewImages()) && isset($campaigns) && $campaigns->hasPushedProducts()) {
		echo '<div id="pushedProducts">
				<p>', $i18n->msg('result_pushedProducts'), '</p>';
		foreach ($campaigns->getPushedProducts() AS $record) {
			renderProduct($record, $util, $i18n);
		}
		echo '</div>
			<div class="clear">&nbsp;</div>';
	}
}

/**
 * This function renders the result (list of found products) to the standard output.
 *
 * @param	FACTFinder_Result				$result				the result object returned by the search adapter
 * @param	FACTFinder_Util					$util				a utility object for the current search adapter
 * @param	i18n							$i18n				internationalization object
 **/
function renderResults($result, $util, $i18n) {
	if (!isset($result)) return;
	foreach($result AS $record) {
		renderProduct($record, $util, $i18n);
	}
}

/**
 * This function renders a single product (result) to the standard output.
 *
 * @param	FACTFinder_Record				$record				the product's data record object
 * @param	FACTFinder_Util					$util				a utility object for the current search adapter
 * @param	i18n							$i18n				internationalization object
 **/
function renderProduct($record, $util, $i18n) {
	// Maybe it is ugly using global here? Alternatives: handing it over all the way from index.phtml; initializing it here for every single call
	global $fieldname;
	
	$id          = $record->getValue($fieldname['id']);
	$articleNr   = $record->getValue($fieldname['artNo']);
	$title       = $record->getValue($fieldname['title']);
	$price       = $record->getValue($fieldname['price']);
	$description = $record->getValue($fieldname['description']);
	$imageUrl    = $record->getValue($fieldname['imageUrl']);
	$detailUrl   = $record->getValue($fieldname['detailUrl']);
	$sid         = '';
	
	echo '<!-- product -->
		<div class="productWrap">
			<div class="product">
				<div class="picture">
					<a href="', $detailUrl, '" onclick="', $util->createJavaScriptClickCode($record, $title, $sid, false), '">
						<img valign="top" src="', $imageUrl, '" alt="', $i18n->msg('product_noPicture'), '"
							title="', addslashes($title), '" onload="resizePicture(this, 120, 170)" onerror="imageNotFound(this)"
							onmouseover="TagToTip(\'descr', $articleNr, '\', SHADOW, true)" onmouseout="UnTip()" />
					</a>
				</div>

				<div style="display:none">
					<span id="descr', $articleNr, '" class="hoverDescription">
						<p style="width:200px; font-weight: bold; margin-bottom: 2px;">', $title, '</p>
						<p style="width:200px; text-align: right; margin-top: 0;">', $i18n->msg('product_articlenumber'), ' ', $articleNr, '</p>
						<p style="width:200px;">', mb_strimwidth($description, 0, 160, '..'), '</p>
					</span>
				</div>

				<div class="title">
					<a href="', $detailUrl, '" onclick="', $util->createJavaScriptClickCode($record, $title, $sid, false), '">', mb_strimwidth($title, 0, 23, '..'), '</a>
				</div>
				<div class="price">', $price, $i18n->msg('product_priceUnit'), '</div>
			</div>
		</div>
	';
}

/**
 * This function renders a paging option box to the standard output.
 *
 * @param	FACTFinder_ProductsPerPageOptions	$productsPerPageOptions		the paging options object returned by the search adapter
 * @param	i18n								$i18n						internationalization object
 **/
function renderPagingOptionBox($productsPerPageOptions, $i18n) {
	echo '<div class="optionBox">
		<select onchange="document.location.href=this.value;" />';
	foreach($productsPerPageOptions AS $option) {
		if ($option->isSelected()) {
			$selected = ' selected ';
		} else {
			$selected = '';
		}
		echo '<option value="'.$option->getUrl().'" '.$selected.'>', $i18n->msg('result_productsPerPage', $option->getValue()), '</option>';
	}

	echo '</select>
		</div>';
}

/**
 * This function renders a tag cloud to the standard output.
 *
 * @param	i18n							$i18n				internationalization object
 * @param	list of FACTFinder_TagQuery		$tagCloud			tag query list returned by the tag cloud adapter
 **/
function renderTagCloud($i18n, $tagCloud) {
	echo '<!-- tag cloud -->
		<div id="tagCloud">
			<h3>', $i18n->msg('tagcloud_title'), '</h3>
			<p>';
	foreach($tagCloud AS $tagQuery) {
		echo '<a href="', $tagQuery->getUrl(), '" style="padding-left:0px; font-size:', (100 + $tagQuery->getWeight()*100), '%">', $tagQuery->getValue(), '</a> ';
	}
	echo '
			</p>
		</div>
	';
}

/**
 * This function renders (visual) footer to the standard output.
 *
 * @param	i18n							$i18n				internationalization object
 **/
function renderFooter($i18n) {
	echo '<!-- footer -->
		<div id="footerLogo"><img src="./files/images/Omikron-Logo-small.gif" alt=""/></div>
		<div id="footerText">', $i18n->msg('footer_copyright'), '</div>
		<div id="footerLink"><a href="http://dev1.fact-finder.de/Demoshop6.1/jsp/search/demoshop.info.jsp">', $i18n->msg('footer_contact'), '</a></div>
	';
}