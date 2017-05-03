<?php

include_once TICKET_LIB . '/Db.php';
include_once TICKET_LIB . '/Ticket.php';

class Ticket_CommentTest extends Ticket_Database_TestCase {

    protected $data;

    public function setUp() {
        $this->data = array(
            'ticket' => 1
        );

        parent::setUp();
    }

    public function getDataSet() {
        return $this->createArrayDataSet(array(
                    'comment' => array(
                        array(
                            'id'        => 1,
                            'content'   => 'how to write blog?',
                            'user'      => 1,
                            'ticket_id' => 1,
                            'user_type' => 1
                        )
                    )
        ));
    }
    
    public function testCommentShow(){
        $ticket = new Ticket();
        $comment = $ticket->comment($this->data);
        $expectedResult = array('0' => array('content' => 'how to write blog?', 'user' => 1, 'user_type' => 1));
        $this->assertEquals($expectedResult, $comment);
    }

}
