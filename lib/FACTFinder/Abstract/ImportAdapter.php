<?php
/**
 * FACT-Finder PHP Framework
 *
 * @category  Library
 * @package   FACTFinder\Abstract
 * @copyright Copyright (c) 2012 Omikron Data Quality GmbH (www.omikron.net)
 */

/**
 * adapter to trigger an import in factfinder
 *
 * @author    Rudolf Batt <rb@omikron.net>
 * @version   $Id: TagCloudAdapter.php 25893 2010-06-29 08:19:43Z rb $
 * @package   FACTFinder\Abstract
 */
abstract class FACTFinder_Abstract_ImportAdapter extends FACTFinder_Abstract_Adapter
{
    /**
     * trigger a data import
     *
	 * @param  bool   $download   import files will also be updated if true
     * @return object $report     import report in xml format
     */
    public function triggerDataImport($download = false) {
        return $this->triggerImport($download, false);
    }
	
	/**
     * trigger a suggest import
     *
	 * @param  bool   $download   import files will also be updated if true
     * @return object $report     import report in xml format
     */
    public function triggerSuggestImport($download = false) {
        return $this->triggerImport($download, true);
    }

    /**
	 * @param  bool   $download        import files will also be updated if true
	 * @param  bool   $suggestImport   do suggest import if true, data import otherwise
     * @return object $report          import report in xml format
     */
    abstract protected function triggerImport($download, $suggestImport);
}