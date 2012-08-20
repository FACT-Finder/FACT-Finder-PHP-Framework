<?php
/**
 * This script contains functions regarding the demoshops internationalization
 *
 * @author    Rudolf Batt <rb@omikron.net>, Martin Buettner <martin.buettner@omikron.net>
 * @revision  $Rev: -1 $
 * @update    $LastChangedDate: $
 **/

    /**
     * returns am i18n object
     *
     * @return i18n
    **/
    function getI18n($config, $paramsParser)
    {
		$params = $paramsParser->getRequestParams();

		//init i18n
		$defaultLang = $config->getLanguage();
		if ($defaultLang == null || $defaultLang == '') {
			$defaultLang = 'de_de';
		}
		if (isset($params['lang'])) {
			$lang = $params['lang'];
		}
		return new i18n($defaultLang, I18N_DIR, $defaultLang, FF::getInstance('encodingHandler', $config));
    }