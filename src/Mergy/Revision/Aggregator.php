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
namespace Mergy\Revision;


/**
 * Mediator to read revision-information
 *
 * @author Hans-Peter Buniat <hpbuniat@googlemail.com>
 * @copyright 2011-2012 Hans-Peter Buniat <hpbuniat@googlemail.com>
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @version Release: @package_version@
 * @link https://github.com/hpbuniat/mergy
 */
class Aggregator {

    /**
     * Create a diff
     *
     * @var boolean
     */
    const CREATE_DIFF = true;

    /**
     * Do not create a diff
     *
     * @var boolean
     */
    const SKIP_DIFF = false;

    /**
     * Passed cli-arguments
     *
     * @var array
     */
    protected $_aArguments = array();

    /**
     * The Services to use
     *
     * @var array <>
     */
    protected $_aRevisions = array();

    /**
     * The revision-builder instance, which creates revision - including the vcs-aggregator
     *
     * @var Builder
     */
    protected $_oBuilder;

    /**
     * Create the aggregator
     *
     * @param  Builder $oBuilder
     */
    public function __construct(Builder $oBuilder) {
        $this->_oBuilder = $oBuilder;
    }

    /**
     * Set some properties
     *
     * @param  array $aArguments
     *
     * @return Aggregator
     */
    public function set(array $aArguments) {
        $this->_aArguments = $aArguments;
        return $this;
    }

    /**
     * Get all Revision-Details
     *
     * @param  boolean $bCreateDiff [true]
     *
     * @return Aggregator
     */
    public function run($bCreateDiff = true) {
        foreach ($this->_aArguments['revisions'] as $sRevision) {
            $this->_aRevisions[] = $this->_oBuilder->build($this->_aArguments['config']->remote, $sRevision);
        }

        $aActions = array('read');
        if ($bCreateDiff === true) {
            $aActions[] = 'diff';
        }

        $oParallel = \parallely\Builder::build($this->_aRevisions, $this->_aArguments['config']->parallel);
        $this->_aRevisions = $oParallel->run($aActions)->get();
        unset($oParallel);

        return $this;
    }

    /**
     * Get the revisions
     *
     * @return array
     */
    public function get() {
        return $this->_aRevisions;
    }
}
