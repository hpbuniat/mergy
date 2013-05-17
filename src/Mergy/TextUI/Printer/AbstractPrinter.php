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

namespace Mergy\TextUI\Printer;

/**
 * Base class for revision-output
 *
 * @author Hans-Peter Buniat <hpbuniat@googlemail.com>
 * @copyright 2011-2012 Hans-Peter Buniat <hpbuniat@googlemail.com>
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @version Release: @package_version@
 * @link https://github.com/hpbuniat/mergy
 */
abstract class AbstractPrinter {

    /**
     * A list of revisions
     *
     * @var array<\Mergy\Revision>
     */
    protected $_aRevisions = array();

    /**
     * The output
     *
     * @var string
     */
    protected $_sOutput = '';

    /**
     * The formatter
     *
     * @var \Mergy\TextUI\Printer\FormatterInterface
     */
    protected $_oFormatter;

    /**
     * Exception-Text
     *
     * @var string
     */
    const NO_FORMATTER = 'No formatter was set';

    /**
     * Set revisions
     *
     * @param  array $aRevisions
     *
     * @return AbstractPrinter
     */
    public function setRevisions(array $aRevisions) {
        $this->_aRevisions = $aRevisions;
        return $this;
    }

    /**
     * Get the revision-output
     *
     * @return string
     *
     * @throws \Mergy\Exception
     */
    public function get() {
        if (($this->_oFormatter instanceof \Mergy\TextUI\Printer\FormatterInterface) !== true) {
            throw new \Mergy\Exception(self::NO_FORMATTER);
        }

        $this->_process();
        $this->_sOutput = trim($this->_sOutput);
        return $this->_sOutput;
    }

    /**
     * Set the formatter
     *
     * @param  \Mergy\TextUI\Printer\FormatterInterface $oFormatter
     *
     * @return AbstractPrinter
     */
    public function setFormatter(\Mergy\TextUI\Printer\FormatterInterface $oFormatter) {
        $this->_oFormatter = $oFormatter;
        return $this;
    }

    /**
     * Process the revisions and create the output
     *
     * @return AbstractPrinter
     */
    abstract protected function _process();
}
