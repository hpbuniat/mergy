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
 * Simple Output-Wrapper
 *
 * @author Hans-Peter Buniat <hpbuniat@googlemail.com>
 * @copyright 2011 Hans-Peter Buniat <hpbuniat@googlemail.com>
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @version Release: @package_version@
 * @link https://github.com/hpbuniat/mergy
 */
class Mergy_TextUI_Output {

    /**
     * Print an Error
     *
     * @param  string $message
     * @param  boolean $exit
     *
     * @return void
     */
    static public function error($message, $exit = true) {
        print_r('Error: ' . $message . PHP_EOL);
        if ($exit === true) {
            exit(Mergy_TextUI_Command::ERROR_EXIT);
        }
    }

    /**
     * Print an Info
     *
     * @param  string $message
     *
     * @return void
     */
    static public function info($message) {
        print_r($message . PHP_EOL);
    }

    /**
     * Write to ouput
     *
     * @param  string $message
     *
     * @return void
     */
    static public function write($message) {
        print_r($message);
    }

    /**
     * Print a list of revisions
     *
     * @param  array $aRevisions
     *
     * @return void
     */
    static public function printRevisions(array $aRevisions) {
        $bBackground = true;
        foreach ($aRevisions as $oRevision) {
            if ($oRevision instanceof Mergy_Revision) {
                $sPrint = $oRevision->__toString();
                if ($bBackground === true) {
                    $bBackground = false;
                    $sPrint = "\033[0;30m\033[47m" . $sPrint . "\033[0m";
                }
                else {
                    $bBackground = true;
                }

                Mergy_TextUI_Output::info($sPrint);
            }
        }
    }
}