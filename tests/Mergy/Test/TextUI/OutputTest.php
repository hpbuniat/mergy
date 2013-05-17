<?php
/**
 * Test class for \Mergy\TextUI\Output.
 * Generated by PHPUnit on 2011-10-22 at 23:06:51.
 */
namespace Mergy\Test\Mergy\TextUI;

class OutputTest extends \PHPUnit_Framework_TestCase {

    const TEST_STRING = 'Mergy makes testing easier';

    /**
     * Test the error output
     */
    public function testError() {
        $this->expectOutputString('Error: ' . self::TEST_STRING . PHP_EOL);
        \Mergy\TextUI\Output::error(self::TEST_STRING, false);
    }

    /**
     * Test the info output
     */
    public function testInfo() {
        $this->expectOutputString(self::TEST_STRING . PHP_EOL);
        \Mergy\TextUI\Output::info(self::TEST_STRING);
    }

    /**
     * Test the write output
     */
    public function testWrite() {
        $this->expectOutputString(self::TEST_STRING);
        \Mergy\TextUI\Output::write(self::TEST_STRING);
    }
}