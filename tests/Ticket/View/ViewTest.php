<?php

include_once TICKET_LIB . '/Db.php';
include_once TICKET_LIB . '/Ticket.php';

class Ticket_ViewTest extends Ticket_Database_TestCase {

    protected $data;

    public function setUp() {
        $this->data = array(
            'email' => 'test001@163.com'
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
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Missing required email
     */
    public function testEmailIsRequired() {
        unset($this->data['email']);
        $ticket = new Ticket();
        $ticketRows = $ticket->view($this->data);
    }

    public function emailProvider() {
        $data = '';
        for ($i = 0; $i < 101; $i++) {
            $data .= 'a';
        }
        return array(
            array($data),
            array('abc')
        );
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Email min length 4, max length 100
     * @dataProvider emailProvider
     */
    public function testEmailLength($email) {
        $this->data['email'] = $email;
        $ticket = new Ticket();
        $ticketRows = $ticket->view($this->data);
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Email invalid
     * @dataProvider emailProvider
     */
    public function testEmailInvalid() {
        $this->data['email'] = 'ourblog.com';
        $ticket = new Ticket();
        $ticketRows = $ticket->view($this->data);
    }

    public function testViewTicket() {
        $ticket = new Ticket();
        $ticketRows = $ticket->view($this->data);
        $expectedResult = array('0' => array('id' => 1, 'title' => 'how to write blog?', 'status' => 'pending'));
        $this->assertEquals($expectedResult, $ticketRows);
    }

}
