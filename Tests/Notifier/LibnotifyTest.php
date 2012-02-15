<?php
/**
 * Test class for Mergy_Notifier_Libnotify.
 * Generated by PHPUnit on2011-2012-10-22 at 23:00:50.
 */
class Mergy_Notifier_LibnotifyTest extends PHPUnit_Framework_TestCase {

    /**
     * @var Mergy_Notifier_Libnotify
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->object = new Mergy_Notifier_Libnotify();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
    }

    /**
     * Test simple notify call
     */
    public function testNotify() {
        $this->assertInstanceOf('Mergy_AbstractNotifier', $this->object->notify(Mergy_AbstractNotifier::SUCCESS, ''));
    }
}
