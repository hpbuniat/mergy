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

namespace Mergy;

/**
 * Represents a revision
 *
 * @author Hans-Peter Buniat <hpbuniat@googlemail.com>
 * @copyright 2011-2012 Hans-Peter Buniat <hpbuniat@googlemail.com>
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @version Release: @package_version@
 * @link https://github.com/hpbuniat/mergy
 */
class Revision {

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
     * Modified Files
     *
     * @var array
     */
    public $aFiles = null;

    /**
     * Diffs of modified files
     *
     * @var array
     */
    public $aDiffs = null;

    /**
     * The revision builder
     *
     * @var Revision\Builder
     */
    protected $_oBuilder;

    /**
     * Create a Revision-Object
     *
     * @param  string $sRepository
     * @param  int $iRevision
     * @param  Revision\Builder $oBuilder
     */
    public function __construct($sRepository, $iRevision, Revision\Builder $oBuilder) {
        $this->iRevision = $iRevision;
        $this->sRepository = $sRepository;
        $this->_oBuilder = $oBuilder;
    }

    /**
     * Read additional info to a revision
     *
     * @return Revision
     */
    public function read() {
        $this->_fetchInfo();
        return $this;
    }

    /**
     * Get diff-output for modified files
     *
     * @return Revision
     */
    public function diff() {
        $this->aDiffs = array();
        if ($this->aFiles === null) {
            $this->_fetchFiles();
        }

        foreach ($this->aFiles as $aFile) {
            $oAggregate = $this->_oBuilder->getAggregator(Revision\Builder::AGGREGATE_DIFF, array(
                $this->sRepository,
                $this->iRevision,
                $aFile['path'],
                $aFile['type']
            ));
            $this->aDiffs[] = array(
                'file' => $aFile['path'],
                'diff' => (string) $oAggregate->get(),
                'type' => $aFile['type']
            );
            unset($oAggregate);
        }

        return $this;
    }

    /**
     * In String-Context show a formatted output
     *
     * @return string
     */
    public function __toString() {
        return str_pad($this->iRevision, 7) . str_pad($this->sAuthor, 18) . str_replace("\n", ' ', $this->sInfo);
    }

    /**
     * Read Information about a revision from a repository
     *
     * @return Revision
     */
    protected function _fetchInfo() {
        $oAggregate = $this->_oBuilder->getAggregator(Revision\Builder::AGGREGATE_INFO, array(
            $this->sRepository,
            $this->iRevision
        ));

        try {
            $oInfo = $oAggregate->get();
            $this->sInfo = (string) $oInfo->msg;
            $this->sAuthor = (string) $oInfo->author;
            $this->sDate = (string) $oInfo->date;
            unset ($oInfo);
        }
        catch (Revision\Aggregator\Exception $oException) {
            /* currently, there is nothing to do here */
            \Mergy\TextUI\Output::error($oException->getMessage());
        }

        unset($oAggregate);
        return $this;
    }

    /**
     * Read modifications of a revision from a repository
     *
     * @return Revision
     */
    protected function _fetchFiles() {
        $this->aFiles = array();
        $oAggregate = $this->_oBuilder->getAggregator(Revision\Builder::AGGREGATE_FILES, array(
            $this->sRepository,
            $this->iRevision
        ));

        try {
            $this->aFiles = $oAggregate->get();
        }
        catch (Revision\Aggregator\Exception $oException) {
            /* currently, there is nothing to do here */
            \Mergy\TextUI\Output::error($oException->getMessage());
        }

        unset($oAggregate);
        return $this;
    }
}
