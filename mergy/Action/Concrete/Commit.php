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
 * Action to commit
 *
 * @author Hans-Peter Buniat <hpbuniat@googlemail.com>
 * @copyright 2011 Hans-Peter Buniat <hpbuniat@googlemail.com>
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @version Release: @package_version@
 * @link https://github.com/hpbuniat/mergy
 */
class mergy_Action_Concrete_Commit extends mergy_Action_AbstractAction {

    /**
     * Failure description
     *
     * @var string
     */
    const PROBLEM = 'Commit failed';

    /**
     * (non-PHPdoc)
     * @see mergy_Action_AbstractAction::_execute()
     */
    protected function _execute() {
        if ($this->_oConfig->more !== true) {
            $sMessage = '-- merged with ' . $this->_oConfig->remote . PHP_EOL
                      . '-- done by ' . PHP_EOL . PHP_EOL;

            foreach ($this->_oConfig->tracked as $sTicket) {
                $sMessage .= '-- ' . $this->_oConfig->issues . $sTicket . PHP_EOL;
            }

            $this->_oCommand->execute('svn ci ' . $this->_oConfig->path . ' --message "' . $sMessage . '"');

            $this->_bSuccess = true;
            if ($this->_oCommand->isSuccess() !== true) {
                $this->_bSuccess = false;
            }

            if ((defined('VERBOSE') === true and VERBOSE === true) or $this->_bSuccess === false) {
                mergy_TextUI_Output::info($this->_oCommand->get());
            }
        }

        return $this;
    }
}