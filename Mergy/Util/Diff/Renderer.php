<?php
/**
 * mergy
 *
 * Copyright (c)2011-2012, Hans-Peter Buniat <hpbuniat@googlemail.com>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 * * Redistributions of source code must retain the above copyright
 * notice, this list of conditions and the following disclaimer.
 *
 * * Redistributions in binary form must reproduce the above copyright
 * notice, this list of conditions and the following disclaimer in
 * the documentation and/or other materials provided with the
 * distribution.
 *
 * * Neither the name of Hans-Peter Buniat nor the names of his
 * contributors may be used to endorse or promote products derived
 * from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package mergy
 * @author Hans-Peter Buniat <hpbuniat@googlemail.com>
 * @copyright 2011-2012 Hans-Peter Buniat <hpbuniat@googlemail.com>
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

/**
 * Create a diff overview-page
 *
 * @author Hans-Peter Buniat <hpbuniat@googlemail.com>
 * @copyright 2011-2012 Hans-Peter Buniat <hpbuniat@googlemail.com>
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @version Release: @package_version@
 * @link https://github.com/hpbuniat/mergy
 */
class Mergy_Util_Diff_Renderer {

    /**
     * The HTML-Object
     *
     * @var DOMDocument
     */
    protected $_oHtml = null;

    /**
     * The Revisions with all modified paths and diffs
     *
     * @var array
     */
    protected $_aRevisions;

    /**
     * The Div-Element as Container for all Diff-Containers
     *
     * @var DOMElement
     */
    protected $_oDiffDiv = null;

    /**
     * Number of diffs for the current revision
     *
     * @var int
     */
    protected $_iCount;

    /**
     * Normal revision head-text
     *
     * @var string
     */
    const REVISION_TEXT = 'Showing %d change(s) in revision %d (by %s @ %s)';

    /**
     * Head-text for diffs
     *
     * @var string
     */
    const FILE_TEXT = 'Changes for %s in revision %d';

    /**
     * Text, if there are no diffs
     *
     * @var string
     */
    const NO_DIFF = 'No diff for %s at revision %d (File was deleted)';

    /**
     * Init the Diff-Renderer
     */
    public function __construct() {
        $this->_oHtml = new DOMDocument('1.0');
        $this->_oHtml->loadHTMLFile(MERGY_PATH . 'Mergy' . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR . 'diff.html');
        $this->_oDiffDiv = $this->_oHtml->getElementById('diffs');
    }

    /**
     * Set the revisions
     *
     * @param  array $aRevisions
     *
     * @return Mergy_Util_Diff_Creator
     */
    public function revisions(array $aRevisions) {
        $this->_aRevisions = $aRevisions;
        return $this;
    }

    /**
     * Start rendering
     *
     * @return Mergy_Util_Diff_Creator
     */
    public function render() {
        foreach ($this->_aRevisions as $oRevision) {
            $aDiffs = $oRevision->aDiffs;
            $this->_iCount = count($aDiffs);
            if ($this->_iCount > 0) {
                $oContainer = $this->_addContainer($oRevision);
                foreach ($aDiffs as $aDiff) {
                    $this->_addDiff($oContainer, $aDiff, $oRevision);
                }
            }
        }

        $sFile = getcwd() . DIRECTORY_SEPARATOR . 'Mergy_diff_' . time() . '.html';
        $this->_oHtml->saveHTMLFile($sFile);
        Mergy_Util_Registry::get('notify')->notify(Mergy_AbstractNotifier::INFO, $sFile);

        return $this;
    }

    /**
     * Add a container for a revision
     *
     * @param Mergy_Revision $oRevision
     *
     * @return DOMElement
     */
    protected function _addContainer(Mergy_Revision $oRevision) {
        $oDiv = $this->_oHtml->createElement('div');
        $oDiv->setAttributeNode(new DOMAttr('class', 'revision'));
        $oH2 = $this->_oHtml->createElement('h3', sprintf(self::REVISION_TEXT, $this->_iCount, $oRevision->iRevision, $oRevision->sAuthor, $oRevision->sDate));
        $oSpan = $this->_oHtml->createElement('span', htmlspecialchars($oRevision->sInfo));
        $this->_oDiffDiv->appendChild($oDiv);
        $oDiv->appendChild($oH2);
        $oDiv->appendChild($oSpan);

        return $oDiv;
    }

    /**
     * Add a files diff to the revision container
     *
     * @param  DOMElement $oContainer
     * @param  array $aDiff
     * @param  Mergy_Revision $oRevision
     *
     * @return Mergy_Util_Diff_Creator
     */
    protected function _addDiff(DOMElement $oContainer, $aDiff, Mergy_Revision $oRevision) {
        $bDiff = (strlen($aDiff['diff']) > 0 or $aDiff['type'] === 'added') ? true : false;

        $oDiv = $this->_oHtml->createElement('div');
        $oH4 = $this->_oHtml->createElement('h4', sprintf((($bDiff === true) ? self::FILE_TEXT : self::NO_DIFF), $aDiff['file'], $oRevision->iRevision));
        $oDiv->appendChild($oH4);

        if ($bDiff === true) {
            $oPre = $this->_oHtml->createElement('pre', htmlspecialchars($aDiff['diff']));
            $aFile = explode('.', $aDiff['file']);
            $oPre->setAttributeNode(new DOMAttr('class', 'brush: ' . (($aDiff['type'] === 'added') ? end($aFile) : 'diff')));
            $oDiv->appendChild($oPre);
        }

        $oContainer->appendChild($oDiv);

        return $this;
    }
}