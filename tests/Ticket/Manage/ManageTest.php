<?php

include_once TICKET_LIB . '/Db.php';
include_once TICKET_LIB . '/Ticket.php';

class Ticket_ManageTest extends Ticket_Database_TestCase {

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
    
    public function testManageShow(){
        $ticket = new Ticket();
        $ticketRows = $ticket->manage();
        $expectedResult = array('0' => array(
            'status'    => 'pending',
            'title'     => 'how to write blog?',
            'id'        => 1,
            'customer'  => 1,
            'domain'    => 'ourblog.dev',
            'customer'  => 'test001@163.com'
            ));
        $this->assertEquals($expectedResult, $ticketRows);
    }
}
