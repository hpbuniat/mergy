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
namespace Mergy\Revision\Subversion;

use \Mergy\Revision\AggregatorAbstract;

/**
 * Read modifications of a revision from a repository
 *
 * @author Hans-Peter Buniat <hpbuniat@googlemail.com>
 * @copyright 2011-2012 Hans-Peter Buniat <hpbuniat@googlemail.com>
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @version Release: @package_version@
 * @link https://github.com/hpbuniat/mergy
 */
class Files extends AggregatorAbstract {

    /**
     * (non-PHPdoc)
     * @see \Mergy\Util\Cacheable::_get()
     */
    protected function _get() {
        $sCommand = 'svn diff ' . $this->_sRepository . ' --xml --summarize -c ' . $this->_iRevision;
        $this->_oCommand->command($sCommand)->execute();

        $this->_mCache = $this->_oCommand->get();
        if ($this->_oCommand->isSuccess() !== true) {
            $this->_mCache = '';
            \Mergy\TextUI\Output::info(sprintf(self::ERROR, $sCommand));
        }

        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see \Mergy\Util\Cacheable::get()
     */
    public function get() {
        parent::get();

        $oFiles = simplexml_load_string($this->_mCache);
        if ($oFiles instanceof \SimpleXMLElement) {
            $aFiles = array();
            foreach ($oFiles->paths->children() as $oPath) {
                $oAttributes = $oPath->attributes();
                $sPath = (string) $oPath;
                if (strlen($sPath) > 0 and isset($oAttributes->kind) === true and (string) $oAttributes->kind === 'file') {
                    $aFiles[] = array(
                        'type' => (string) $oAttributes->item,
                        'path' => (string) $sPath
                    );
                }

                unset ($sPath, $oAttributes);
            }

            return $aFiles;
        }

        throw new \Mergy\Revision\Aggregator\Exception(\Mergy\Revision\Aggregator\Exception::ERROR);
    }
}
