<?php

include_once TICKET_LIB . '/Db.php';
include_once TICKET_LIB . '/Ticket.php';

class Ticket_CreateTest extends Ticket_Database_TestCase {

    protected $data;

    public function setUp() {
        $this->data = array(
            'title' => 'how to write blog?',
            'description' => 'RT. how to write blog?',
            'email' => 'test002@163.com',
            'domain' => 'ourblog.dev'
        );

        parent::setUp();
    }

    public function getDataSet() {
        return $this->createArrayDataSet(array(
                    'customer' => array(
                        array('id' => 1, 'name' => 'test001@163.com')
                    ),
                    'ticket' => array()
        ));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Required title is missing
     */
    public function testTitleIsRequired() {
        unset($this->data['title']);

        $ticket = new Ticket();
        $ticket->create($this->data);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Required description is missing
     */
    public function testDescriptionIsRequired() {
        unset($this->data['description']);

        $ticket = new Ticket();
        $ticket->create($this->data);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Required email is missing
     */
    public function testEmailIsRequired() {
        unset($this->data['email']);

        $ticket = new Ticket();
        $ticket->create($this->data);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Required domain is missing
     */
    public function testDomainIsRequired() {
        unset($this->data['domain']);

        $ticket = new Ticket();
        $ticket->create($this->data);
    }

    public function titleProvider() {
        $data = '';
        for ($i = 0; $i < 501; $i++) {
            $data .= 'a';
        }
        return array(
            array($data),
            array('')
        );
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Title max length 500, min length 1
     * @dataProvider titleProvider
     */
    public function testTitleLength($title) {
        $this->data['title'] = $title;
        $ticket = new Ticket();
        $ticket->create($this->data);
    }

    public function descriptionProvider() {
        $data = '';
        for ($i = 0; $i < 64001; $i++) {
            $data .= 'a';
        }
        return array(
            array($data)
        );
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Max description is 64000
     * @dataProvider descriptionProvider
     */
    public function testDescriptionLength($description) {
        $this->data['description'] = $description;
        $ticket = new Ticket();
        $ticket->create($this->data);
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
        $ticket->create($this->data);
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Email invalid
     * @dataProvider emailProvider
     */
    public function testEmailInvalid() {
        $this->data['email'] = 'ourblog.com';
        $ticket = new Ticket();
        $ticket->create($this->data);
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Domain invalid
     */
    public function testDomainInvalid(){
        $this->data['domain'] = 'ourblog.com';
        $ticket = new Ticket();
        $ticket->create($this->data);
    }
    
    public function testNewCustomerCreateTicket() {
        $ticket = new Ticket();
        $ticket->create($this->data);

        $expectedDataSet = $this->createArrayDataSet(include __DIR__ . '/expects/new-customer.php');

        $dataSet = $this->getConnection()->createDataSet(array('customer', 'ticket'));
        $filterDataSet = new PHPUnit_Extensions_Database_DataSet_DataSetFilter($dataSet);
        $filterDataSet->setExcludeColumnsForTable('ticket', array('time'));

        $this->assertDataSetsEqual($expectedDataSet, $filterDataSet);
    }

    public function testCustomerCreateTicket() {
        $this->data['email'] = 'test001@163.com';

        $ticket = new Ticket();
        $ticket->create($this->data);

        $expectedDataSet = $this->createArrayDataSet(include __DIR__ . '/expects/customer.php');

        $dataSet = $this->getConnection()->createDataSet(array('customer', 'ticket'));
        $filterDataSet = new PHPUnit_Extensions_Database_DataSet_DataSetFilter($dataSet);
        $filterDataSet->setExcludeColumnsForTable('ticket', array('time'));

        $this->assertDataSetsEqual($expectedDataSet, $filterDataSet);
    }

}
