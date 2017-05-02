<?php

include_once TICKET_LIB . '/Db.php';
include_once TICKET_LIB . '/Ticket.php';

class Ticket_CloseTest extends Ticket_Database_TestCase {

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
                    )
        ));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Missing required customerEmail
     */
    public function testCustomerEmailIsRequired() {
        unset($this->data['customerEmail']);

        $ticket = new Ticket();
        $ticket->close($this->data);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Missing required ticket
     */
    public function testTicketIsRequired() {
        unset($this->data['ticket']);

        $ticket = new Ticket();
        $ticket->close($this->data);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Customer Email and ticket id not related
     */
    public function testCloseUnrelatedTicket(){
        $this->data['ticket'] = 2;
        $ticket = new Ticket();
        $ticket->close($this->data);
    }
    
    public function testCloseTicket() {
        $ticket = new Ticket();
        $ticket->close($this->data);

        $expectedDataSet = $this->createArrayDataSet(include __DIR__ . '/expects/ticket.php');

        $dataSet = $this->getConnection()->createDataSet(array('customer', 'ticket'));
        $filterDataSet = new PHPUnit_Extensions_Database_DataSet_DataSetFilter($dataSet);
        $filterDataSet->setExcludeColumnsForTable('ticket', array('time'));

        $this->assertDataSetsEqual($expectedDataSet, $filterDataSet);
    }

}
