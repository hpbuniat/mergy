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
 * @package Testy
 * @author Hans-Peter Buniat <hpbuniat@googlemail.com>
 * @copyright2011-2012 Hans-Peter Buniat <hpbuniat@googlemail.com>
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

/**
 * Notifier-Handler
 *
 * @author Hans-Peter Buniat <hpbuniat@googlemail.com>
 * @copyright2011-2012 Hans-Peter Buniat <hpbuniat@googlemail.com>
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @version Release: @package_version@
 * @link https://github.com/hpbuniat/mergy
 */
class Mergy_Notifier {

    /**
     * The notifiers configuration
     *
     * @var stdClass
     */
    protected $_oConfig;

    /**
     * The configured notifiers
     *
     * @var array
     */
    protected $_aNotifiers = array();

    /**
     * Init a Notifier
     *
     * @param  stdClass $oConfig
     */
    public function __construct(stdClass $oConfig = null) {
        $this->_oConfig = $oConfig;
        $this->_create();
    }

    /**
     * Send a notification
     *
     * @param  string $sStatus The status
     * @param  string $sText The Text-content
     *
     * @return Mergy_Notifier
     */
    public function notify($sStatus, $sText = '') {
        foreach ($this->_aNotifiers as $oNotifier) {
            $oNotifier->notify($sStatus, $sText);
        }

        return $this;
    }

    /**
     * Build the enabled notifiers
     *
     * @return Mergy_Notifier
     */
    private function _create() {
        if (empty($this->_oConfig->notifiers) !== true) {
            foreach ($this->_oConfig->notifiers as $sNotifier => $oConfig) {
                if (isset($oConfig->enabled) === true and $oConfig->enabled == true) {
                    $sNotifier = 'Mergy_Notifier_' . ucfirst($sNotifier);
                    $this->_aNotifiers[] = new $sNotifier($oConfig);
                }
            }
        }

        return $this;
    }
}