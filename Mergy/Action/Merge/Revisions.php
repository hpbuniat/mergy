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
 * Execute the decision-queue
 *
 * @author Hans-Peter Buniat <hpbuniat@googlemail.com>
 * @copyright 2011 Hans-Peter Buniat <hpbuniat@googlemail.com>
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @version Release: @package_version@
 * @link https://github.com/hpbuniat/mergy
 */
class Mergy_Action_Merge_Revisions {

    /**
     * Revisions to merge
     *
     * @var array
     */
    private $_aRevisions = array();

    /**
     * Decision-Queue
     *
     * @var array
     */
    private $_aQueue = array();

    /**
     * Setup
     *
     * @param  array $aRevisions
     * @param  array $aMerge
     *
     * @return Mergy_Action_Merge_Revisions
     */
    public function setup(array $aRevisions, stdClass $oConfig) {
        $this->_aRevisions = $aRevisions;
        foreach ($oConfig->merge as $sMerge) {
            $sDecide = str_replace('Revisions', 'Decision', get_class($this)) . '_' . ucfirst(strtolower($sMerge));
            if (class_exists($sDecide) === true) {
                $this->register(new $sDecide($oConfig));
            }
        }

        return $this;
    }

    /**
     * Run the decission-queue over all revisions
     *
     * @return Mergy_Action_Merge_Revisions
     */
    public function process() {
        $aRevisions = array();
        foreach ($this->_aRevisions as $oRevision) {
            foreach ($this->_aQueue as $oDecider) {
                if ($oDecider->decide($oRevision) === true) {
                    $aRevisions[] = $oRevision;
                    break;
                }
            }
        }

        $this->_aRevisions = $aRevisions;
        return $this;
    }

    /**
     * Add a Decider to the queue
     *
     * @param  Mergy_Action_Merge_AbstractDecision $oDecider
     *
     * @return Mergy_Action_Merge_Revisions
     */
    public function register(Mergy_Action_Merge_AbstractDecision $oDecider) {
        $this->_aQueue[get_class($oDecider)] = $oDecider;
        return $this;
    }

    /**
     * Get the Revisions to merge
     *
     * @return array
     */
    public function get() {
        return $this->process()->_aRevisions;
    }
}