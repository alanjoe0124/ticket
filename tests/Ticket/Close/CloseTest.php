<?php

include_once TICKET_LIB . '/Db.php';
include_once TICKET_LIB . '/Ticket.php';

class Ticket_CloseTest extends Ticket_Database_TestCase {

    protected $get = array();
    protected $session = array();

    public function setUp() {
        $this->get = array( 'ticket' => 1 );
        $this->session = array( 'customerEmail' => 'test001@163.com' );
        parent::setUp();
    }

    public function getDataSet() {
        return $this->createArrayDataSet(array(
                    'customer' => array(
                        array('id' => 1, 'name' => 'test001@163.com')
                    ),
                    'ticket' => array(
                        array(
                            'id'            => 1,
                            'title'         => 'how to write blog?',
                            'description'   => 'RT. how to write blog?',
                            'user'          => 1,
                            'domain'        => 'ourblog.dev',
                            'status'        => 1
                        )
                    )
        ));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Missing required customerEmail
     */
    public function testCustomerEmailIsRequired() {
        unset($this->session['customerEmail']);

        $ticket = new Ticket();
        $ticket->close($this->get, $this->session);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Missing required ticket
     */
    public function testTicketIsRequired() {
        unset($this->get['ticket']);

        $ticket = new Ticket();
        $ticket->close($this->get, $this->session);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Customer Email and ticket id not related
     */
    public function testCloseUnrelatedTicket() {
        $this->get['ticket'] = 2;
        $ticket = new Ticket();
        $ticket->close($this->get, $this->session);
    }
    
    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Ticket id is invalid
     */
    public function testTicketIdInvalid(){
        $this->get['ticket'] = 'abc';
        $ticket = new Ticket();
        $ticket->close($this->get, $this->session);
    }
    
    public function testCloseTicket() {
        $ticket = new Ticket();
        $ticket->close($this->get, $this->session);

        $expectedDataSet = $this->createArrayDataSet(include __DIR__ . '/expects/ticket.php');

        $dataSet = $this->getConnection()->createDataSet(array('customer', 'ticket'));
        $filterDataSet = new PHPUnit_Extensions_Database_DataSet_DataSetFilter($dataSet);
        $filterDataSet->setExcludeColumnsForTable('ticket', array('time'));

        $this->assertDataSetsEqual($expectedDataSet, $filterDataSet);
    }

}
