<?php
/**
 * Test class for Mergy_Revision.
 * Generated by PHPUnit on 2012-03-27 at 22:12:54.
 */
class Mergy_RevisionTest extends PHPUnit_Framework_TestCase {

    /**
     * @var Mergy_Revision
     */
    protected $_object;

    /**
     * Test reading the info
     */
    public function testRead() {
        $sXml = include 'Tests/_files/Response/Revision/Info.php';
        $oCommand = $this->getMockBuilder('Mergy_Util_Command')->getMock();
        $oCommand->expects($this->any())->method('isSuccess')->will($this->returnValue(true));
        $oCommand->expects($this->any())->method('command')->will($this->returnSelf());
        $oCommand->expects($this->any())->method('execute')->will($this->returnSelf());
        $oCommand->expects($this->any())->method('get')->will($this->returnValue(
            $sXml
        ));

        $oBuilder = new Mergy_Revision_Builder(Mergy_Revision_Builder::SUBVERSION, $oCommand);
        $this->_object = $oBuilder->build('test', 6874);
        $this->_object->read();

        $this->assertEquals(6874, $this->_object->iRevision);
        $this->assertEquals('test', $this->_object->sRepository);
        $this->assertEquals('AuthorM', $this->_object->sAuthor);
        $this->assertEquals('2012-03-14T10:16:13.683462Z', $this->_object->sDate);
        $this->assertEquals('Bug 123 - https://bugzilla/show_bug.cgi?id=123', $this->_object->sInfo);

        $this->assertEquals('6874   AuthorM           Bug 123 - https://bugzilla/show_bug.cgi?id=123', $this->_object->__toString());
    }

    /**
     * @todo Implement testDiff().
     */
    public function testDiff() {
        $sXml = include 'Tests/_files/Response/Revision/Files.php';
        $oCommand = $this->getMockBuilder('Mergy_Util_Command')->getMock();
        $oCommand->expects($this->any())->method('isSuccess')->will($this->returnValue(true));
        $oCommand->expects($this->any())->method('command')->will($this->returnSelf());
        $oCommand->expects($this->any())->method('execute')->will($this->returnSelf());
        $oCommand->expects($this->any())->method('get')->will($this->returnValue(
            $sXml
        ));

        $oBuilder = new Mergy_Revision_Builder(Mergy_Revision_Builder::SUBVERSION, $oCommand);
        $this->_object = $oBuilder->build('test', 6874);
        $this->_object->diff();

        $this->assertEquals(6874, $this->_object->iRevision);
        $this->assertEquals('test', $this->_object->sRepository);
        $this->assertCount(2, $this->_object->aFiles);
    }

}
