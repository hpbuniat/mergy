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
 * Provide a stack of actions
 *
 * @author Hans-Peter Buniat <hpbuniat@googlemail.com>
 * @copyright 2011-2012 Hans-Peter Buniat <hpbuniat@googlemail.com>
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @version Release: @package_version@
 * @link https://github.com/hpbuniat/mergy
 */
class Mergy_Action_Handler {

    /**
     * Handler-Stacks
     *
     * @var array <Mergy_Action_AbstractAction>
     */
    protected $_aStacks = array();

    /**
     * The actions return/output
     *
     * @var array <string>
     */
    protected $_aReturn = array();

    /**
     * The globale configuration
     *
     * @var stdClass
     */
    protected $_oConfig;

    /**
     * The notifier
     *
     * @var Mergy_Notifier
     */
    protected $_oNotifier;

    /**
     * Const for unique-actions
     *
     * @var string
     */
    const SINGLE = 'instant';

    /**
     * Init the handler
     */
    public function __construct(stdClass $oConfig, Mergy_Notifier $oNotifier) {
        $this->reset();
        $this->_oConfig = $oConfig;
        $this->_oNotifier = $oNotifier;
    }

    /**
     * Reset the handler to a initial state
     *
     * @return Mergy_Action_Handler
     */
    public function reset() {
        $this->_aStacks = array(
            'init' => array(),
            'pre' => array(),
            'post' => array(),
            self::SINGLE => array()
        );
        $this->_aReturn = array();
        return $this;
    }

    /**
     * Add an action to a specific stack
     *
     * @param  Mergy_Action_AbstractAction $oProcess
     * @param  string $sStack
     *
     * @return Mergy_Action_Handler
     */
    public function add(Mergy_Action_AbstractAction $oProcess, $sStack = null) {
        if (empty($sStack) === true) {
            $sStack = self::SINGLE;
        }

        $sProcess = $oProcess->getName();
        if (defined('VERBOSE') === true and VERBOSE === true) {
            Mergy_TextUI_Output::info(sprintf('Adding action "%s" to the %s-stack', $sProcess, $sStack));
        }

        $this->_aStacks[$sStack][$sProcess] = $oProcess;
        return $this;
    }

    /**
     * Get an actions return/output
     *
     * @param  string $sProcess
     *
     * @return mixed
     */
    public function get($sProcess) {
        if (isset($this->_aReturn[$sProcess]) === true) {
            return $this->_aReturn[$sProcess];
        }

        return null;
    }

    /**
     * Execute all actions of a stack
     *
     * @param  string $sMethod
     * @param  array $aArgs
     *
     * @return Mergy_Action_Handler
     *
     * @throws Exception
     */
    public function __call($sMethod, $aArgs) {
        if (empty($this->_aStacks[$sMethod]) !== true) {
            foreach ($this->_aStacks[$sMethod] as $oProcess) {
                if (defined('VERBOSE') === true and VERBOSE === true) {
                    Mergy_TextUI_Output::info('Executing ' . $oProcess->getName() . ' in ' . $sMethod);
                }

                $this->_aReturn[$oProcess->getName()] = $oProcess->execute()->get();
                if ($oProcess->isSuccess() !== true) {
                    throw new Mergy_Exception($oProcess::PROBLEM);
                }
            }
        }

        return $this;
    }

    /**
     * Execute the merge-action
     *
     * @param  array $aRevisions
     *
     * @return Mergy_Action_Handler
     */
    public function merge(array $aRevisions) {
        foreach ($aRevisions as $oRevision) {
            $oMerge = new Mergy_Action_Concrete_Merge($this->_oConfig, new stdClass(), $this->_oNotifier);
            if ($this->_oConfig->reintegrate === true) {
                $oMerge->reintegrate();
            }
            else {
                $oMerge->revision($oRevision->iRevision);
            }

            $oMerge->execute();
            if ($oMerge->isSuccess() !== true) {
                throw new Mergy_Exception($oMerge::PROBLEM);
            }
        }

        return $this;
    }
}