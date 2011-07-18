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
 * Action to execute the merge itself
 *
 * @author Hans-Peter Buniat <hpbuniat@googlemail.com>
 * @copyright 2011 Hans-Peter Buniat <hpbuniat@googlemail.com>
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @version Release: @package_version@
 * @link https://github.com/hpbuniat/mergy
 */
class Mergy_Action_Concrete_Merge extends Mergy_Action_AbstractAction {

    /**
     * Current revision-/merge-string
     *
     * @var string
     */
    protected $_sRevision;

    /**
     * Failure description
     *
     * @var string
     */
    const INVALID_REVISION = 'Invalid Revision';

    /**
     * Failure description
     *
     * @var string
     */
    const PROBLEM = 'A problem (e.g. a conflict) occured while merging. Take a look at the working-copy, press enter when finished';

    /**
     * Toggle revision-mode for merge
     *
     * @param  int $iRevision
     *
     * @return Mergy_Action_AbstractAction
     */
    public function revision($iRevision) {
        $iRevision = (int) $iRevision;
        if (($iRevision > 0) !== true) {
            throw new Exception(self::INVALID_REVISION);
        }

        $this->_sRevision = '-c ' . $iRevision;

        return $this;
    }

    /**
     * Toggle reintegrate-mode for merge
     *
     * @return Mergy_Action_AbstractAction
     */
    public function reintegrate() {
        $this->_sRevision = '--reintegrate';
        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Mergy_Action_AbstractAction::_execute()
     */
    protected function _execute() {
        $sCommand = 'svn merge ' . $this->_oConfig->remote . ' --accept postpone --non-interactive ' . $this->_sRevision . ' ' . $this->_oConfig->path . ' | awk \'{a["A"]=32;a["U"]=34;a["C"]=31;a["M"]=34;a["G"]=37;a["?"]=33;a["D"]=36;printf("\033[1;%sm%s\033[0;00m\n",a[$1],$0)}\'';
        $this->_oCommand->execute($sCommand);

        $sResult = $this->_oCommand->get();
        // @FIXME: Better conflict check!
        if ($this->_oCommand->isSuccess() !== true or strpos($sResult, 'Summary of conflicts') !== false or strpos($sResult, 'Konfliktübersicht') !== false) {
            $this->_bSuccess = false;
        }

        if ((defined('VERBOSE') === true and VERBOSE === true) or $this->_bSuccess === false) {
            Mergy_TextUI_Output::info($sResult);
        }

        return $this;
    }
}