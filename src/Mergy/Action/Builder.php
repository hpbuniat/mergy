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
namespace Mergy\Action;


/**
 * Build an action-object
 *
 * @author Hans-Peter Buniat <hpbuniat@googlemail.com>
 * @copyright 2011-2012 Hans-Peter Buniat <hpbuniat@googlemail.com>
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @version Release: @package_version@
 * @link https://github.com/hpbuniat/mergy
 */
class Builder {

    /**
     * The Action-Handler
     *
     * @var Handler
     */
    protected $_oHandler;

    /**
     * The notifier wrapper
     *
     * @var \notifyy\Collection
     */
    protected $_oNotifier;

    /**
     * Mergy-Config
     *
     * @var \stdClass
     */
    protected $_oConfig;

    /**
     * Create the Action-Builder
     *
     * @param Handler $oHandler
     * @param \stdClass $oConfig
     * @param \notifyy\Collection $oNotifier
     */
    public function __construct(Handler $oHandler, \stdClass $oConfig, \notifyy\Collection $oNotifier) {
        $this->_oHandler = $oHandler;
        $this->_oConfig = $oConfig;
        $this->_oNotifier = $oNotifier;
    }

    /**
     * Build a Action and add it to the handler
     *
     * @param  string $sType
     * @param  \stdClass $oConfig
     * @param  string $sStep
     *
     * @return Builder
     */
    public function build($sType, \stdClass $oConfig, $sStep = null) {
        $sType = ucfirst(strtolower((isset($oConfig->type) === true) ? $oConfig->type : $sType));
        $sClass = str_replace('Builder', 'Concrete', get_class($this)) . '\\' . $sType;
        if (class_exists($sClass) === true) {
            $oAction = new $sClass($this->_oConfig, $oConfig, $this->_oNotifier);
            $this->_oHandler->add($oAction, strtolower($sStep));
        }

        return $this;
    }
}