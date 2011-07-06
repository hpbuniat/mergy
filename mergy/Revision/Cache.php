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
 * Read the information of a revision
 *
 * @author Hans-Peter Buniat <hpbuniat@googlemail.com>
 * @copyright 2011 Hans-Peter Buniat <hpbuniat@googlemail.com>
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @version Release: @package_version@
 * @link https://github.com/hpbuniat/mergy
 */
class mergy_Revision_Cache extends mergy_Util_Cacheable {

    /**
     * The repository of the revision
     *
     * @var string
     */
    protected $_sRepository;

    /**
     * The revisions number
     *
     * @var int
     */
    protected $_iRevision;

    /**
     * Error string
     *
     * @var string
     */
    const ERROR = 'Error reading: %s';

    /**
     * Init the Revision reader
     *
     * @param  string $sRepository
     * @param  int $iRevision
     */
    public function __construct($sRepository, $iRevision) {
        $this->_iRevision = $iRevision;
        $this->_sRepository = $sRepository;
        $this->_id();
    }

    /**
     * (non-PHPdoc)
     * @see mergy_Util_Cacheable::_get()
     */
    protected function _get() {
        $sCommand = 'svn log ' . $this->_sRepository . ' --xml -v -r ' . $this->_iRevision;
        $oCommand = new mergy_Util_Command($sCommand);
        $oCommand->execute();

        $this->_mCache = $oCommand->get();
        if ($oCommand->isSuccess() !== true) {
            $this->_mCache = '';
            mergy_TextUI_Output::info(sprintf(self::ERROR, $sCommand));
        }

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see mergy_Util_Cacheable::_id()
     */
    protected function _id() {
        $this->_sId = md5($this->_iRevision . $this->_sRepository);
        return $this->_file();
    }
}
