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
 * @copyright2011-2012 Hans-Peter Buniat <hpbuniat@googlemail.com>
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

/**
 * Base class for cacheable SVN-Operations
 *
 * @author Hans-Peter Buniat <hpbuniat@googlemail.com>
 * @copyright2011-2012 Hans-Peter Buniat <hpbuniat@googlemail.com>
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @version Release: @package_version@
 * @link https://github.com/hpbuniat/mergy
 */
abstract class Mergy_Revision_SvnAbstract extends Mergy_Util_Cacheable {

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
     * Concrete file Path
     *
     * @var string
     */
    protected $_sPath;

    /**
     * Concrete file action type
     *
     * @var string
     */
    protected $_sType;

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
     * @param  string $sPath
     * @param  string $sType
     */
    public function __construct($sRepository, $iRevision, $sPath = null, $sType = null) {
        $this->_iRevision = $iRevision;
        $this->_sRepository = $sRepository;
        $this->_sPath = $sPath;
        $this->_sType = strtolower(trim((string) $sType));
        $this->_id();
    }

    /**
     * (non-PHPdoc)
     * @see Mergy_Util_Cacheable::_id()
     */
    protected function _id() {
        $this->_sId = md5($this->_iRevision . $this->_sRepository . $this->_sPath . $this->_sType . get_class($this));
        return $this->_file();
    }
}