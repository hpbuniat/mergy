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
 * Output revisions as list
 *
 * @author Hans-Peter Buniat <hpbuniat@googlemail.com>
 * @copyright 2011-2012 Hans-Peter Buniat <hpbuniat@googlemail.com>
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @version Release: @package_version@
 * @link https://github.com/hpbuniat/mergy
 */

class Mergy_TextUI_Output_Group extends Mergy_TextUI_OutputAbstract {

    /**
     * (non-PHPdoc)
     * @see Mergy_TextUI_OutputAbtract::_process()
     */
    protected function _process() {
        $bBackground = true;
        $this->_sOutput = '';
        $aTickets = array();
        foreach ($this->_aRevisions as $oRevision) {
            if ($oRevision instanceof Mergy_Revision) {
                $sTicket = (int) Mergy_Action_Merge_Decision_Ticket::parseTicket($oRevision);
                if (empty($aTickets[$sTicket]) === true) {
                    $aTickets[$sTicket] = array();
                }

                $aTickets[$sTicket][] = $oRevision;
            }
        }

        ksort($aTickets);
        foreach ($aTickets as $sTicket => $aRevisions) {
            $sTicket = ($sTicket === 0) ? 'unspecified' : $sTicket;
            $this->_sOutput .= "\033[0;30m\033[47mTicket: " . $sTicket  . "\033[0m" . PHP_EOL;
            $aRevisionNumbers = array();
            foreach ($aRevisions as $oRevision) {
                $aRevisionNumbers[] = $oRevision->iRevision;
            }

            sort($aRevisionNumbers);
            $this->_sOutput .= "\t" . implode(',', $aRevisionNumbers) . PHP_EOL;
        }

        return $this;
    }
}