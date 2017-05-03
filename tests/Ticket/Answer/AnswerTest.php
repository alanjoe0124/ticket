<?php

include_once TICKET_LIB . '/Db.php';
include_once TICKET_LIB . '/Ticket.php';

class Ticket_AnswerTest extends Ticket_Database_TestCase {

    protected $data;

    public function setUp() {
        $this->data = array(
            'comment' => 'Thanks for your ask',
            'ticketId' => 1,
            'uid' => 1
        );
        parent::setUp();
    }

    public function getDataSet() {
        return $this->createArrayDataSet(array(
                    'comment' => array()
        ));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Missing required comment
     */
    public function testCommentIsRequired() {
        unset($this->data['comment']);

        $ticket = new Ticket();
        $ticket->answer($this->data);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Missing required ticketId
     */
    public function testTicketIdIsRequired() {
        unset($this->data['ticketId']);

        $ticket = new Ticket();
        $ticket->answer($this->data);
    }

    public function commentProvider() {
        $data = '';
        for ($i = 0; $i < 64001; $i++) {
            $data .= 'a';
        }
        return array(
            array($data),
            array('')
        );
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Comment max length 64000 and not empty
     * @dataProvider commentProvider
     */
    public function testCommentLength($comment) {
        $this->data['comment'] = $comment;

        $ticket = new Ticket();
        $ticket->answer($this->data);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid ticket id
     */
    public function testTicketIdInvalid() {
        $this->data['ticketId'] = 'ticket';

        $ticket = new Ticket();
        $ticket->answer($this->data);
    }

    public function testPostAnswerComment() {
        $ticket = new Ticket();
        $ticket->answer($this->data);
        $expectedDataSet = $this->createArrayDataSet(include __DIR__ . '/expects/answer_comment.php');

        $dataSet = $this->getConnection()->createDataSet(array('comment'));
        $filterDataSet = new PHPUnit_Extensions_Database_DataSet_DataSetFilter($dataSet);
        $filterDataSet->setExcludeColumnsForTable('comment', array('time'));

        $this->assertDataSetsEqual($expectedDataSet, $filterDataSet);
    }

}
