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
abstract class Mergy_Action_AbstractAction {

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
     * @var Mergy_Util_Command
     */
    protected $_oCommand;

    /**
     * Was the action executed successful ?
     *
     * @var boolean
     */
    protected $_bSuccess = true;

    /**
     * Default MSG-Text
     *
     * @var string
     */
    const MSG_CONTINUE = 'Target %s was executed. Press Enter to continue!';

    /**
     * Init an Action
     *
     * @param stdClass $oConfig
     * @param stdClass $oProperties
     */
    public function __construct(stdClass $oConfig, stdClass $oProperties) {
        $this->_oConfig = $oConfig;
        $this->_oProperties = $oProperties;

        $this->_oCommand = new Mergy_Util_Command();
    }

    /**
     * Add a value to the properties
     *
     * @param  string $sName
     * @param  mixed $mValue
     *
     * @return Mergy_Action_AbstractAction
     */
    public function property($sName, $mValue) {
        $this->_oProperties->$sName = $mValue;
        return $this;
    }

    /**
     * Was the action successfull?
     *
     * @return boolean
     */
    public function isSuccess() {
        return (bool) $this->_bSuccess;
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
     * @param  string $sMessage The Message to show
     * @param  array $aExpected The expected input - needs keys continue & abort
     *
     * @return Mergy_Action_AbstractAction
     */
    public function execute($sMessage = '', array $aExpected = array()) {
        $bExecute = true;
        if (isset($this->_oProperties->skiponcontinue) === true and $this->_oProperties->skiponcontinue === true and $this->_oConfig->continue === true) {
            $bExecute = false;
        }

        if ($bExecute === true) {
            $this->_execute();
            if (($this->isSuccess() !== true and empty($sMessage) !== true)
                or (isset($this->_oProperties->confirm) === true and $this->_oProperties->confirm === true)) {
                $this->_confirm($sMessage, $aExpected);
            }
        }

        return $this;
    }

    /**
     * Confirm the result of an action
     *
     * @param  string $sMessage The Message to show
     * @param  array $aExpected The expected input - needs keys continue & abort
     *
     * @return Mergy_Action_AbstractAction
     */
    protected function _confirm($sMessage = '', array $aExpected = array()) {
        $sMessage = (empty($sMessage) === true) ? self::MSG_CONTINUE : $sMessage;
        Mergy_Util_Registry::get('notify')->notify(Mergy_AbstractNotifier::INFO, sprintf($sMessage, $this->getName()));
        Mergy_TextUI_Output::info('waiting ...');
        do {
            $rInput = fopen('php://stdin', 'r');
            $sInput = trim(fgets($rInput));
        }
        while (in_array($sInput, $aExpected) === false and empty($aExpected) !== true);

        if (isset($aExpected['abort']) === true and $sInput === $aExpected['abort']) {
            Mergy_TextUI_Output::info('aborting ...');
            throw new Exception($this::PROBLEM);
        }

        Mergy_TextUI_Output::info('continuing ...');

        return $this;
    }

    /**
     * Execute an action - concrete
     *
     * @return Mergy_Action_AbstractAction
     */
    abstract protected function _execute();
}