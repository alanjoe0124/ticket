<?php

include_once TICKET_LIB . '/Db.php';
include_once TICKET_LIB . '/Ticket.php';

class Ticket_InfoTest extends Ticket_Database_TestCase {

    protected $data;

    public function setUp() {
        $this->data = array(
            'customerEmail' => 'test001@163.com',
            'ticket' => 1
        );

        parent::setUp();
    }

    public function getDataSet() {
        return $this->createArrayDataSet(array(
                    'customer' => array(
                        array('id' => 1, 'name' => 'test001@163.com')
                    ),
                    'ticket' => array(
                        array('id' => 1,
                            'title' => 'how to write blog?',
                            'description' => 'RT. how to write blog?',
                            'user' => 1,
                            'domain' => 'ourblog.dev',
                            'status' => 1
                        )
                    ),
                    'status' => array(
                        array('id' => 1, 'name' => 'pending'),
                        array('id' => 2, 'name' => 'done')
                    )
        ));
    }
    
    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Missing required customer email
     */
    public function testCustomerEmailIsRequired(){
        unset($this->data['customerEmail']);
        $ticket = new Ticket();
        $ticket->info($this->data);
    }
    
    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid ticket id
     */
    public function testTicketIdInvalid(){
        $this->data['ticket'] = 'ticketId';
        
        $ticket = new Ticket();
        $ticket->info($this->data);
    }
    
    public function testInfoShow() {
        $ticket = new Ticket();
        $ticketRows = $ticket->info($this->data);
        $expectedResult = array(
            'id' => 1,
            'customer' => 'test001@163.com',
            'title' => 'how to write blog?',
            'description' => 'RT. how to write blog?',
            'status' => 'pending'
        );
        $this->assertEquals($expectedResult, $ticketRows);
    }

}
