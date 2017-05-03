<?php

include_once TICKET_LIB . '/Db.php';
include_once TICKET_LIB . '/Ticket.php';

class Ticket_AskTest extends Ticket_Database_TestCase {

    protected $data;

    public function setUp() {
        $this->data = array(
            'comment' => 'how to write blog?',
            'ticketId' => '1',
            'customerEmail' => 'test001@163.com'
        );

        parent::setUp();
    }

    public function getDataSet() {
        return $this->createArrayDataSet(array(
                    'customer' => array(
                        array('id' => 1, 'name' => 'test001@163.com')
                    ),
                    'ticket' => array(
                        array(
                            'id' => 1,
                            'title' => 'how to write blog?',
                            'description' => 'RT. how to write blog?',
                            'user' => 1,
                            'domain' => 'ourblog.dev',
                            'status' => 1
                        )
                    ),
                    'comment' => array()
        ));
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Missing required comment
     */
    public function testCommentIsRequired() {
        unset($this->data['comment']);

        $ticket = new Ticket();
        $ticket->ask($this->data);
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Missing required ticketId
     */
    public function testTicketIdIsRequired() {
        unset($this->data['ticketId']);

        $ticket = new Ticket();
        $ticket->ask($this->data);
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
        $ticket->ask($this->data);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid ticket id
     */
    public function testTicketIdInvalid(){
        $this->data['ticketId'] = 'ticketId';
        $ticket = new Ticket();
        $ticket->ask($this->data);
    }
    
    public function testPostComment() {
        $ticket = new Ticket();
        $ticket->ask($this->data);

        $expectedDataSet = $this->createArrayDataSet(include __DIR__ . '/expects/comment.php');

        $dataSet = $this->getConnection()->createDataSet(array('comment'));
        $filterDataSet = new PHPUnit_Extensions_Database_DataSet_DataSetFilter($dataSet);
        $filterDataSet->setExcludeColumnsForTable('comment', array('time'));

        $this->assertDataSetsEqual($expectedDataSet, $filterDataSet);
    }

}
