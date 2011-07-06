<?php
/**
 * mergy
 *
 * Copyright (c) 2011, Hans-Peter Buniat <hpbuniat@googlemail.com>.
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
 * @copyright 2011 Hans-Peter Buniat <hpbuniat@googlemail.com>
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

/**
 * Represents a revision
 *
 * @author Hans-Peter Buniat <hpbuniat@googlemail.com>
 * @copyright 2011 Hans-Peter Buniat <hpbuniat@googlemail.com>
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @version Release: @package_version@
 * @link https://github.com/hpbuniat/mergy
 */
class mergy_Revision {

    /**
     * Revision repository url
     *
     * @var string
     */
    public $sRepository;

    /**
     * Revision-Number
     *
     * @var int
     */
    public $iRevision;

    /**
     * Commit-Message
     *
     * @var string
     */
    public $sInfo;

    /**
     * Author of this revision
     *
     * @var string
     */
    public $sAuthor;

    /**
     * Creation-Date
     *
     * @var string
     */
    public $sDate;

    /**
     * Create a Revision-Object
     *
     * @param  string $sRepository
     * @param  int $iRevision
     */
    public function __construct($sRepository, $iRevision) {
        $this->iRevision = $iRevision;
        $this->sRepository = $sRepository;
    }

    /**
     * Read additional info to a revision
     *
     * @return mergy_Revision
     */
    public function read() {
        $oInfo = $this->_fetch();
        if ($oInfo instanceof SimpleXMLElement) {
            $this->sInfo = (string) $oInfo->logentry->msg;
            $this->sAuthor = (string) $oInfo->logentry->author;
            $this->sDate = (string) $oInfo->logentry->date;
        }

        return $this;
    }

    /**
     * In String-Context show a formatted output
     *
     * @return string
     */
    public function __toString() {
        return $this->iRevision . "\t" . $this->sAuthor . "\t\t" . $this->sInfo;
    }

    /**
     * Read Information about a revision from a repository
     *
     * @return SimpleXMLElement
     */
    public function _fetch() {
        $oCache = new mergy_Revision_Cache($this->sRepository, $this->iRevision);
        return simplexml_load_string($oCache->get());
    }
}
