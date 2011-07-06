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
 * Abstract for actions
 *
 * @author Hans-Peter Buniat <hpbuniat@googlemail.com>
 * @copyright 2011 Hans-Peter Buniat <hpbuniat@googlemail.com>
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @version Release: @package_version@
 * @link https://github.com/hpbuniat/mergy
 */
abstract class mergy_Action_AbstractAction {

    /**
     * Config
     *
     * @var stdClass
     */
    protected $_oConfig;

    /**
     * Action-Properties
     *
     * @var stdClass
     */
    protected $_oProperties;

    /**
     * Command Interface
     *
     * @var mergy_Util_Command
     */
    protected $_oCommand;

    /**
     * Continue-Decision
     *
     * @var boolean
     */
    protected $_bContinue = true;

    /**
     * Init an Action
     *
     * @param stdClass $oConfig
     * @param stdClass $oProperties
     */
    public function __construct(stdClass $oConfig, stdClass $oProperties) {
        $this->_oConfig = $oConfig;
        $this->_oProperties = $oProperties;

        $this->_oCommand = new mergy_Util_Command();
    }

    /**
     * Was the action successfull?
     *
     * @return boolean
     */
    public function isSuccess() {
        return (bool) $this->_bContinue;
    }

    /**
     * Get an actions name
     *
     * @return string
     */
    public function getName() {
        $aName = explode('_', get_class($this));
        return end($aName);
    }

    /**
     * Get an actions output
     *
     * @return string
     */
    public function get() {
        return $this->_oCommand->get();
    }

    /**
     * Execute an action
     *
     * @return mergy_Action_AbstractAction
     */
    abstract public function execute();
}