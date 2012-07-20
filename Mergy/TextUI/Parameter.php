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
 * Parse Command-Line arguments
 *
 * @author Hans-Peter Buniat <hpbuniat@googlemail.com>
 * @copyright 2011-2012 Hans-Peter Buniat <hpbuniat@googlemail.com>
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @version Release: @package_version@
 * @link https://github.com/hpbuniat/mergy
 */
class Mergy_TextUI_Parameter {

    /**
     * Error when there are no revisions to merge
     *
     * @var string
     */
    const NO_REVISIONS_TO_MERGE = 'There are no more revisions to merge!';

    /**
     * Default-Arguments
     *
     * @var array
     */
    protected static $_aArguments = array(
        'verbose' => false,
        'list' => false,
        'group' => false,
        'diff' => false,
        'all' => false,
        'strict' => true,
        'more' => false,
        'unattended' => false,
        'config' => Mergy_TextUI_Command::CONFIG_FILE,
        'formatter' => Mergy_TextUI_Printer_Builder::FORMAT_TEXT,
        'remote' => ''
    );

    /**
     * Short-Options
     *
     * @var array
     */
    protected static $_aOptions = array();

    /**
     * Long-Options
     *
     * @var array
     */
    protected static $_aLongOptions = array(
        'remote=' => null,
        'ticket=' => null,
        'force=' => null,
        'rev=' => null,
        'formatter=' => null,
        'path=' => null,
        'config=' => null,
        'continue' => null,
        'more' => null,
        'unattended' => null,
        'reintegrate' => null,
        'strict' => null,
        'help' => null,
        'verbose' => null,
        'list' => null,
        'list-group' => null,
        'diff' => null,
        'all' => null,
        'diff-all' => null,
        'version' => null
    );

    /**
     * Parse the cli-arguments
     *
     * @param  array $aParameters
     * @param  Mergy_TextUI_Command $oMergy
     *
     * @return array
     */
    public static function parse(array $aParameters = array(), Mergy_TextUI_Command $oMergy) {
        $oConsole = new Console_Getopt();
        try {
            self::$_aOptions = @$oConsole->getopt($aParameters, '', array_keys(self::$_aLongOptions));
        }
        catch (RuntimeException $e) {
            Mergy_TextUI_Output::info($e->getMessage());
        }

        if (self::$_aOptions instanceof PEAR_Error) {
            Mergy_TextUI_Output::error(self::$_aOptions->getMessage());
        }
        elseif (empty(self::$_aOptions[0]) === true) {
            self::$_aArguments['list'] = true;
            self::$_aArguments['continue'] = true;
        }

        foreach (self::$_aOptions[0] as $option) {
            switch ($option[0]) {
                case '--rev':
                    self::$_aArguments['cliRevisions'] = $option[1];
                    break;

                case '--ticket':
                    self::$_aArguments['tickets'] = $option[1];
                    break;

                case '--force':
                    self::$_aArguments['strict'] = false;
                    if (empty($option) !== true) {
                        self::$_aArguments['force-comment'] = $option[1];
                    }

                    break;

                case '--strict':
                    self::$_aArguments['strict'] = true;
                    break;

                case '--remote':
                    self::$_aArguments['remote'] = $option[1];
                    break;

                case '--config':
                    self::$_aArguments['config'] = $option[1];
                    break;

                case '--path':
                    self::$_aArguments['path'] = $option[1];
                    break;

                case '--formatter':
                    self::$_aArguments['formatter'] = $option[1];
                    break;

                case '--more':
                    self::$_aArguments['more'] = true;
                    break;

                case '--continue':
                    self::$_aArguments['continue'] = true;
                    break;

                case '--commit':
                    self::$_aArguments['continue'] = true;
                    break;

                case '--unattended':
                    self::$_aArguments['unattended'] = true;
                    break;

                case '--reintegrate':
                    self::$_aArguments['reintegrate'] = true;
                    break;

                case '--verbose':
                    self::$_aArguments['verbose'] = true;
                    break;

                case '--list':
                    self::$_aArguments['list'] = true;
                    break;
                case '--list-group':
                    self::$_aArguments['list'] = true;
                    self::$_aArguments['group'] = true;
                    break;

                case '--diff':
                    self::$_aArguments['diff'] = true;
                    break;

                case '--all':
                    self::$_aArguments['all'] = true;
                    break;

                case '--diff-all':
                    self::$_aArguments['diff'] = true;
                    self::$_aArguments['all'] = true;
                    break;

                case '--help':
                    $oMergy::showHelp();
                    throw new Mergy_TextUI_Parameter_Exception(Mergy_TextUI_Parameter_Exception::NO_PARSING);

                case '--version':
                    $oMergy::printVersionString();
                    throw new Mergy_TextUI_Parameter_Exception(Mergy_TextUI_Parameter_Exception::NO_PARSING);
            }
        }

        unset($oConsole);
        return self::$_aArguments;
    }

    /**
     * Parse the revisions (from repo and cli)
     *
     * @param  array $aArguments
     * @param  string $sRevisions
     *
     * @return array
     *
     * @throws Exception
     */
    public static function revisions(array $aArguments = array(), $sRevisions = '') {
        self::$_aArguments = $aArguments;
        self::$_aArguments['revisions'] = array();

        $aRevisions = explode("\n", $sRevisions);
        if (empty(self::$_aArguments['cliRevisions']) !== true) {
            $aCliRevisions = explode(',', self::$_aArguments['cliRevisions']);
            foreach ($aCliRevisions as $iIndex => $sRevision) {
                if (stripos($sRevision, '-') !== false) {
                    $aRange = explode('-', $sRevision);
                    $iStart = reset($aRange);
                    $iEnd = end($aRange);
                    do {
                        $aCliRevisions[] = $iStart++;
                    }
                    while ($iStart <= $iEnd);
                    unset($aCliRevisions[$iIndex]);
                }
            }

            $aRevisions = array_merge($aRevisions, $aCliRevisions);
            self::$_aArguments['config']->revisions = $aCliRevisions;
            unset(self::$_aArguments['cliRevisions']);
        }

        foreach ($aRevisions as $sRevision) {
            $iRevision = (int) trim((substr($sRevision, 0, 1) === 'r') ? substr($sRevision, 1) : $sRevision);
            if ($iRevision > 0) {
                self::$_aArguments['revisions'][] = $iRevision;
            }
        }

        if (self::$_aArguments['config']->reintegrate === true) {
            self::$_aArguments['config']->revisions = self::$_aArguments['revisions'] = array(
                'HEAD'
            );
        }

        if (empty(self::$_aArguments['revisions']) === true) {
            throw new Exception(self::NO_REVISIONS_TO_MERGE);
        }

        self::$_aArguments['revisions'] = array_unique(self::$_aArguments['revisions']);
        sort(self::$_aArguments['revisions']);

        return self::$_aArguments;
    }
 }