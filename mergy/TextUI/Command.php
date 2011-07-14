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
 * Base-Command class to handle arguments and start processing
 *
 * @author Hans-Peter Buniat <hpbuniat@googlemail.com>
 * @copyright 2011 Hans-Peter Buniat <hpbuniat@googlemail.com>
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @version Release: @package_version@
 * @link https://github.com/hpbuniat/mergy
 */
class mergy_TextUI_Command {

    /**
     * Exit code on success
     *
     * @var int
     */
    const SUCCESS_EXIT = 0;

    /**
     * Exit code on failure
     *
     * @var int
     */
    const ERROR_EXIT = 1;

    /**
     * Default config-file name
     *
     * @var string
     */
    const CONFIG_FILE = 'mergy.json';

    /**
     * Error when there are no revisions to merge
     *
     * @var string
     */
    const NO_REVISIONS_TO_MERGE = 'There are no more revisions to merge!';

    /**
     * Error when there is no config-file found
     *
     * @var string
     */
    const CONFIG_ERROR = 'Error while reading the configuration!';

    /**
     * Default-Arguments
     *
     * @var array
     */
    protected $_aArguments = array(
        'verbose' => false,
        'list' => false,
        'diff' => false,
        'all' => false,
        'strict' => false,
        'remote' => ''
    );

    /**
     * Short-Options
     *
     * @var array
     */
    protected $_aOptions = array();

    /**
     * Long-Options
     *
     * @var array
     */
    protected $_aLongOptions = array(
        'remote=' => null,
        'ticket=' => null,
        'force=' => null,
        'rev=' => null,
        'path=' => null,
        'continue' => null,
        'reintegrate' => null,
        'strict' => null,
        'help' => null,
        'verbose' => null,
        'list' => null,
        'diff' => null,
        'all' => null,
        'diff-all' => null,
        'version' => null
    );

    /**
     * Argument to config mapping
     *
     * @var array
     */
    protected $_aConfig = array(
        'remote' => 'remote',
        'path' => 'path',
        'tickets' => 'tickets',
        'reintegrate' => 'reintegrate',
        'continue' => 'continue',
        'force-comment' => 'force'
    );

    /**
     * Main entry
     *
     * @param boolean $exit
     */
    public static function main($exit = true) {
        $command = new mergy_TextUI_Command();
        $command->run($_SERVER['argv'], $exit);
    }

    /**
     * Run mergy
     *
     * @param  array   $argv
     * @param  boolean $exit
     *
     * @return mergy_TextUI_Command
     *
     * @TODO Cleanup !
     */
    public function run(array $argv, $exit = true) {
        $this->handleArguments($argv);

        $oAggregator = new mergy_Revision_Aggregator();
        $aRevisions = $oAggregator->set($this->_aArguments)->run()->get();
        if ($this->_aArguments['list'] === true) {
            foreach ($aRevisions as $oRevision) {
                if ($oRevision instanceof mergy_Revision) {
                    mergy_TextUI_Output::info($oRevision->__toString());
                }
            }
        }

        if ($this->_aArguments['list'] !== true and $this->_aArguments['all'] !== true) {
            $oRevisions = new mergy_Action_Merge_Revisions();
            $aRevisions = $oRevisions->setup($aRevisions, $this->_aArguments['config'])->get();
        }

        $this->_aArguments['config']->mergeRevisions = $aRevisions;

        $oAction = new mergy_Action($this->_aArguments['config']);
        $oAction->setup();
        if ($this->_aArguments['diff'] === true) {
            $oAction->command('Diff');
        }

        if ($this->_aArguments['list'] !== true and $this->_aArguments['diff'] !== true) {
            $oAction->pre()->merge()->post();
        }

        return $this;
    }

    /**
     * Handle passed arguments
     *
     * @param array $argv
     *
     * @return mergy_TextUI_Command
     *
     * @TODO Cleanup!
     */
    protected function handleArguments(array $argv) {
        self::printVersionString();

        $oConsole = new Console_Getopt();
        try {
            $this->_aOptions = @$oConsole->getopt($argv, '', array_keys($this->_aLongOptions));
        }
        catch (RuntimeException $e) {
            mergy_TextUI_Output::info($e->getMessage());
        }

        if ($this->_aOptions instanceof PEAR_Error) {
            mergy_TextUI_Output::error($this->_aOptions->getMessage());
        }

        foreach ($this->_aOptions[0] as $option) {
            switch ($option[0]) {
                case '--rev':
                    $this->_aArguments['cliRevisions'] = $option[1];
                    break;

                case '--ticket':
                    $this->_aArguments['tickets'] = $option[1];
                    break;

                case '--force':
                    $this->_aArguments['force-comment'] = $option[1];
                    break;

                case '--strict':
                    $this->_aArguments['strict'] = true;
                    break;

                case '--remote':
                    $this->_aArguments['remote'] = $option[1];
                    break;

                case '--config':
                    $this->_aArguments['config'] = $option[1];
                    break;

                case '--path':
                    $this->_aArguments['path'] = $option[1];
                    break;

                case '--continue':
                    $this->_aArguments['continue'] = true;
                    break;

                case '--reintegrate':
                    $this->_aArguments['reintegrate'] = true;
                    break;

                case '--verbose':
                    $this->_aArguments['verbose'] = true;
                    break;

                case '--list':
                    $this->_aArguments['list'] = true;
                    break;

                case '--diff':
                    $this->_aArguments['diff'] = true;
                    break;

                case '--all':
                    $this->_aArguments['all'] = true;
                    break;

                case '--diff-all':
                    $this->_aArguments['diff'] = true;
                    $this->_aArguments['all'] = true;
                    break;

                case '--help':
                case '--version':
                    $this->showHelp();
                    exit(self::SUCCESS_EXIT);
                    break;
            }
        }

        if (defined('VERBOSE') === false) {
            define('VERBOSE', $this->_aArguments['verbose']);
        }

        $sConfig = self::CONFIG_FILE;
        if (isset($this->_aArguments['config']) === true) {
            $sConfig = $this->_aArguments['config'];
        }

        $this->_aArguments['config'] = json_decode(file_get_contents($sConfig));
        if (empty($this->_aArguments['config']) === true) {
            mergy_TextUI_Output::error(self::CONFIG_ERROR);
            exit();
        }

        if (empty($this->_aArguments['remote']) !== true and preg_match('!http(s)?://!i', $this->_aArguments['remote']) === 0) {
            if (isset($this->_aArguments['config']->remote) === true) {
                $aRemote = explode('/', $this->_aArguments['config']->remote);
                $aRemote[count($aRemote) - 1] = $this->_aArguments['remote'];
                $this->_aArguments['remote'] = implode('/', $aRemote);
            }
        }

        foreach ($this->_aConfig as $sArg => $sConfig) {
            if (empty($this->_aArguments[$sArg]) !== true) {
                $this->_aArguments['config']->$sConfig = $this->_aArguments[$sArg];
                unset($this->_aArguments[$sArg]);
            }
            elseif (isset($this->_aArguments['config']->$sConfig) === false) {
                $this->_aArguments['config']->$sConfig = null;
            }
        }

        $this->_aArguments['config']->tickets = explode(",", $this->_aArguments['config']->tickets);
        $this->_aArguments['config']->force = explode(",", $this->_aArguments['config']->force);
        if ($this->_aArguments['strict'] === true) {
            $this->_aArguments['config']->force = false;
        }

        mergy_Util_Registry::set('_CONFIG', $this->_aArguments['config']);
        $oAction = new mergy_Action($this->_aArguments['config']);
        if ($this->_aArguments['config']->continue !== true and $this->_aArguments['list'] !== true) {
            $oAction->setup()->init();
        }

        try {
            $sRevisions = $oAction->command('Unmerged');
            $this->_handleRevisions($sRevisions);
        }
        catch (Exception $e) {
            mergy_TextUI_Output::error(self::NO_REVISIONS_TO_MERGE);
            exit();
        }

        unset($oAction, $oConsole);

        return $this;
    }

    /**
     * Handle revisions (from repo and cli)
     *
     * @param  string $sRevisions
     *
     * @return mergy_TextUI_Command
     *
     * @throws Exception
     *
     * @TODO Move this to a class
     */
    protected function _handleRevisions($sRevisions = '') {
        $this->_aArguments['revisions'] = array();

        $aRevisions = explode("\n", $sRevisions);
        if (empty($this->_aArguments['cliRevisions']) !== true) {
            $aCliRevisions = explode(',', $this->_aArguments['cliRevisions']);
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
            $this->_aArguments['config']->revisions = $aCliRevisions;
            unset($this->_aArguments['cliRevisions']);
        }

        foreach ($aRevisions as $sRevision) {
            $iRevision = (int) trim((substr($sRevision, 0, 1) === 'r') ? substr($sRevision, 1) : $sRevision);
            if ($iRevision > 0) {
                $this->_aArguments['revisions'][] = $iRevision;
            }
        }

        if ($this->_aArguments['config']->reintegrate === true) {
            $this->_aArguments['config']->revisions = $this->_aArguments['revisions'] = array(
                'HEAD'
            );
        }

        if (empty($this->_aArguments['revisions']) === true) {
            throw new Exception(self::NO_REVISIONS_TO_MERGE);
        }

        $this->_aArguments['revisions'] = array_unique($this->_aArguments['revisions']);
        sort($this->_aArguments['revisions']);

        return $this;
    }

    /**
     * Show the help message
     *
     * @return void
     */
    protected function showHelp() {
        echo <<<EOT
Usage: mergy [switches]

  --remote=[repository|branch]     // remote repository, might be only a branch-name
  --rev=revision[,revision]        // revisions to merge (might have been merged before)
  --force=keyword[,keyword]        // keywords to force merge of this revisons, if unmerged
  --ticket=ticket-id[,ticket-id]   // find all revisions of a ticket
  --config=mergy.json              // use this config-file
  --path=[PATH_TO_WC]              // use this working copy (instead of .)
  --continue                       // continue skips the pre-merge-actions (e.g. after conflict)
  --reintegrate                    // reintegrate a whole branch - without specific revisions
  --list                           // list unmerged revisions from repository
  --verbose                        // verbose
EOT;
    }

    /**
     * Print the version string
     *
     * @return void
     */
    public static function printVersionString() {
        mergy_TextUI_Output::info('mergy - a svn cherry-pick-assisant (Version: @package_version@)');
    }
}