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
 * Log Merge-Actions, to track params and create a commit message
 *
 * @author Hans-Peter Buniat <hpbuniat@googlemail.com>
 * @copyright 2011-2012 Hans-Peter Buniat <hpbuniat@googlemail.com>
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @version Release: @package_version@
 * @link https://github.com/hpbuniat/mergy
 */
class Mergy_Util_Merge_Tracker {

    /**
     * Cache-File to track the data
     *
     * @var string
     */
    protected $_sFile;

    /**
     * Merged tickets
     *
     * @var array
     */
    protected $_aTickets = array();

    /**
     * The Configuration
     *
     * @var stdClass
     */
    protected $_oConfig;

    /**
     * Create a new tracker
     *
     * @param stdClass $oConfig
     */
    public function __construct(stdClass $oConfig) {
        $this->_oConfig = $oConfig;
        $this->_sFile = Mergy_Util_Cacheable::DIR . md5($this->_oConfig->remote);
        if (is_dir(Mergy_Util_Cacheable::DIR) !== true) {
            mkdir(Mergy_Util_Cacheable::DIR, 0777, true);
        }

        $this->_read();
        $this->_aTickets = array_merge($this->_aTickets, $this->_oConfig->tickets);
        $this->_write();
    }

    /**
     * Get the data
     *
     * @return array
     */
    public function get() {
        return $this->_aTickets;
    }

    /**
     * Remove saved data
     *
     * @return Mergy_Util_Merge_Tracker
     */
    public function clean() {
        $this->_aTickets = $this->_oConfig->tickets;
        if (file_exists($this->_sFile) === true) {
            unlink($this->_sFile);
        }

        return $this;
    }

    /**
     * Read saved data
     *
     * @return Mergy_Util_Merge_Tracker
     */
    protected function _read() {
        if (file_exists($this->_sFile) === true) {
            $this->_aTickets = unserialize(file_get_contents($this->_sFile));
        }

        return $this;
    }

    /**
     * Write the data
     *
     * @return Mergy_Util_Merge_Tracker
     */
    protected function _write() {
        file_put_contents($this->_sFile, serialize($this->_aTickets));
        if (file_exists($this->_sFile) === true) {
            chmod($this->_sFile, 0666);
        }

        return $this;
    }
}