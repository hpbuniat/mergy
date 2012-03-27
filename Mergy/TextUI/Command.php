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
 * Base-Command class to handle arguments and start processing
 *
 * @author Hans-Peter Buniat <hpbuniat@googlemail.com>
 * @copyright 2011-2012 Hans-Peter Buniat <hpbuniat@googlemail.com>
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @version Release: @package_version@
 * @link https://github.com/hpbuniat/mergy
 */
class Mergy_TextUI_Command {

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
     * Error when there is no config-file found
     *
     * @var string
     */
    const CONFIG_ERROR = 'Error while reading the configuration!';

    /**
     * Message to indicate a finished run
     *
     * @var string
     */
    const FINISHED = 'All jobs done';

    /**
     * The applications name
     *
     * @var string
     */
    const NAME = 'mergy';

    /**
     * The version
     *
     * @var string
     */
    const VERSION = 'mergy - a svn cherry-pick-assistant (Version: @package_version@)';

    /**
     * Usage info
     *
     * @var string
     */
    const USAGE = <<<'EOT'
Usage: mergy [switches]
      [--remote=[repository|branch]]     // remote repository, might be only a branch-name
      [--rev=revision[,revision]]        // revisions to merge (might have been merged before)
      [--ticket=ticket-id[,ticket-id]]   // find all revisions of a ticket
      [--continue]                       // continue skips the pre-merge-actions (e.g. after conflict)
      [--reintegrate]                    // reintegrate a whole branch - without specific revisions
      [--list]                           // list unmerged revisions from repository
      [--list-group]                     // list unmerged revisions from repository and group by comment
      [--diff]                           // create a diff, based on the revisions to merge
      [--all]                            // use all unmerged revisions
      [--diff-all]                       // equals --diff --all
      [--strict]                         // only merge, what was given - no force via config
      [--commit]                         // commit changes in the wc - with tracked log, if present
      [--more]                           // skip commit

      // further parameters
      [--verbose]                        // verbose
      [--force=keyword[,keyword]]        // keywords to force merge of this revisons, if unmerged
      [--config=mergy.json]              // use this config-file
      [--path=[PATH_TO_WC]]              // use this working copy (instead of .)

EOT;

    /**
     * Default-Arguments
     *
     * @var array
     */
    protected $_aArguments = array();

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
        'more' => 'more',
        'force-comment' => 'force'
    );

    /**
     * Track more single mergy-calls to create a commit message
     *
     * @var Mergy_Util_Merge_Tracker
     */
    protected $_oMergeTracker = null;

    /**
     * The notifier-wrapper
     *
     * @var Mergy_Notifier
     */
    protected $_oNotifier;

    /**
     * Main entry
     */
    public static function main() {
        $oCommand = new Mergy_TextUI_Command();
        $oCommand->run($_SERVER['argv']);
    }

    /**
     * Run mergy
     *
     * @param  array   $argv
     *
     * @return void
     *
     * @TODO Cleanup !
     */
    public function run(array $argv) {
        try {
            if ($this->handleArguments($argv) === false) {
                return self::SUCCESS_EXIT;
            }

            $sVersionControl = (isset($this->_aArguments['config']->vcs) === true) ? $this->_aArguments['config']->vcs : Mergy_Revision_Builder::SUBVERSION;
            $oBuilder = new Mergy_Revision_Builder($sVersionControl);

            $oAggregator = new Mergy_Revision_Aggregator($oBuilder);
            $oAggregator->set($this->_aArguments);

            if ($this->_aArguments['list'] === true) {
                $aRevisions =  $oAggregator->run($oAggregator::SKIP_DIFF)->get();

                $sPrinter = 'Mergy_TextUI_Output_' . (($this->_aArguments['group'] === true) ? 'Group' : 'List');
                $oPrinter = new $sPrinter();
                Mergy_TextUI_Output::info($oPrinter->setRevisions($aRevisions)->get());
            }
            else {
                $aRevisions =  $oAggregator->run()->get();

                if ($this->_aArguments['all'] !== true) {
                    $oRevisions = new Mergy_Action_Merge_Revisions();
                    $aRevisions = $oRevisions->setup($aRevisions, $this->_aArguments['config'])->get();
                    unset($oRevisions);
                }

                $this->_aArguments['config']->mergeRevisions = $aRevisions;

                $oAction = new Mergy_Action($this->_aArguments['config'], $this->_oNotifier);
                $oAction->setup();

                if ($this->_aArguments['diff'] === true) {
                    $oAction->command('Diff');
                }
                else {
                    $oAction->pre()->merge()->post();
                }

                unset($oAction);
                if ($this->_aArguments['config']->more !== true) {
                    $this->_oMergeTracker->clean();
                }
            }

            $this->_oNotifier->notify(Mergy_AbstractNotifier::INFO, self::FINISHED);
            unset($oAggregator, $this->_oNotifier);
        }
        catch (RuntimeException $e) {
            Mergy_TextUI_Output::error($e->getMessage());
        }
    }

    /**
     * Handle passed arguments
     *
     * @param array $argv
     *
     * @return Mergy_TextUI_Command
     *
     * @TODO Cleanup!
     */
    public function handleArguments(array $argv = array()) {
        try {
            $this->_aArguments = Mergy_TextUI_Parameter::parse($argv, $this);
        }
        catch (Mergy_TextUI_Parameter_Exception $oException) {
            return false;
        }

        if (defined('VERBOSE') === false) {
            define('VERBOSE', $this->_aArguments['verbose']);
        }

        $sConfig = self::CONFIG_FILE;
        if (isset($this->_aArguments['config']) === true) {
            $sConfig = $this->_aArguments['config'];
        }

        if (file_exists($sConfig) === true) {
            $this->_aArguments['config'] = json_decode(file_get_contents($sConfig));
        }

        if (empty($this->_aArguments['config']) === true) {
            Mergy_TextUI_Output::error(self::CONFIG_ERROR);
            return false;
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

        $this->_aArguments['config']->tickets = explode(',', $this->_aArguments['config']->tickets);
        $this->_aArguments['config']->force = explode(',', $this->_aArguments['config']->force);
        if ($this->_aArguments['strict'] === true) {
            $this->_aArguments['config']->force = false;
        }

        $this->_oNotifier = new Mergy_Notifier($this->_aArguments['config']);
        $this->_oMergeTracker = new Mergy_Util_Merge_Tracker($this->_aArguments['config']);

        $oAction = new Mergy_Action($this->_aArguments['config'], $this->_oNotifier);
        if ($this->_aArguments['config']->continue !== true) {
            $oAction->setup()->init();
            $this->_oMergeTracker->clean();
        }

        try {
            $this->_aArguments = Mergy_TextUI_Parameter::revisions($this->_aArguments, $oAction->command('Unmerged'));
        }
        catch (Exception $oException) {
            Mergy_TextUI_Output::error($oException->getMessage());
            return false;
        }

        $aTrackedTickets = $this->_oMergeTracker->get();
        sort($aTrackedTickets);
        $this->_aArguments['config']->tracked = $aTrackedTickets;

        unset($oAction);

        return $this;
    }

    /**
     * Show the help message
     *
     * @return void
     */
    public static function showHelp() {
        Mergy_TextUI_Output::info(self::USAGE);
    }

    /**
     * Print the version string
     *
     * @return void
     */
    public static function printVersionString() {
        Mergy_TextUI_Output::info(self::VERSION);
    }
}