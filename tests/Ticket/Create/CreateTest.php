<?php

class Ticket_CreateTest extends Ticket_Database_TestCase {

    protected $data;

    public function setUp() {
        $this->data = array(
            'title'         => 'how to write blog?',
            'description'   => 'RT. how to write blog?',
            'email'         => 'test002@163.com',
            'domain'        => 'ourblog.dev'
        );

        parent::setUp();
    }

    public function getDataSet() {
        return $this->createArrayDataSet(array(
                    'customer'  => array(
                        array('id' => 1, 'name' => 'test001@163.com')
                    ),
                    'ticket'    => array()
        ));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Required title is missing
     */
    public function testTitleIsRequired() {
        unset($this->data['title']);

        $ticket = new ZendX_Ticket();
        $ticket->create($this->data);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Required description is missing
     */
    public function testDescriptionIsRequired() {
        unset($this->data['description']);

        $ticket = new ZendX_Ticket();
        $ticket->create($this->data);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Required email is missing
     */
    public function testEmailIsRequired() {
        unset($this->data['email']);

        $ticket = new ZendX_Ticket();
        $ticket->create($this->data);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Required domain is missing
     */
    public function testDomainIsRequired() {
        unset($this->data['domain']);

        $ticket = new ZendX_Ticket();
        $ticket->create($this->data);
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Title max length 500, min length 1
     */
    public function testTitleMaxLength() {
        $title = '';
        for ($i = 0; $i < 501; $i++) {
            $title .= 'a';
        }
        $this->data['title'] = $title;
        $ticket = new ZendX_Ticket();
        $ticket->create($this->data);
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Title max length 500, min length 1
     */
    public function testTitleMinLength() {
        $title = '';
        $this->data['title'] = $title;
        $ticket = new ZendX_Ticket();
        $ticket->create($this->data);
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Max description is 64000
     */
    public function testDescriptionLength() {
        $description = '';
        for ($i = 0; $i < 64001; $i++) {
            $description .= 'a';
        }
        $this->data['description'] = $description;
        $ticket = new ZendX_Ticket();
        $ticket->create($this->data);
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Email min length 4, max length 100
     */
    public function testEmailMaxLength() {
        $email = '';
        for ($i = 0; $i < 101; $i++) {
            $email .= 'a';
        }
        $this->data['email'] = $email;
        $ticket = new ZendX_Ticket();
        $ticket->create($this->data);
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Email min length 4, max length 100
     */
    public function testEmailMinLength() {
        $email = 'abc';
        $this->data['email'] = $email;
        $ticket = new ZendX_Ticket();
        $ticket->create($this->data);
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Email invalid
     */
    public function testEmailInvalid() {
        $this->data['email'] = 'ourblog.com';
        $ticket = new ZendX_Ticket();
        $ticket->create($this->data);
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Domain invalid
     */
    public function testDomainInvalid() {
        $this->data['domain'] = 'ourblog.com';
        $ticket = new ZendX_Ticket();
        $ticket->create($this->data);
    }

    public function testNewCustomerCreateTicket() {
        $ticket = new ZendX_Ticket();
        $ticket->create($this->data);

        $expectedDataSet = $this->createArrayDataSet(include __DIR__ . '/expects/new-customer.php');

        $dataSet = $this->getConnection()->createDataSet(array('customer', 'ticket'));
        $filterDataSet = new PHPUnit_Extensions_Database_DataSet_DataSetFilter($dataSet);
        $filterDataSet->setExcludeColumnsForTable('ticket', array('time'));

        $this->assertDataSetsEqual($expectedDataSet, $filterDataSet);
    }

    public function testCustomerCreateTicket() {
        $this->data['email'] = 'test001@163.com';

        $ticket = new ZendX_Ticket();
        $ticket->create($this->data);

        $expectedDataSet = $this->createArrayDataSet(include __DIR__ . '/expects/customer.php');

        $dataSet = $this->getConnection()->createDataSet(array('customer', 'ticket'));
        $filterDataSet = new PHPUnit_Extensions_Database_DataSet_DataSetFilter($dataSet);
        $filterDataSet->setExcludeColumnsForTable('ticket', array('time'));

        $this->assertDataSetsEqual($expectedDataSet, $filterDataSet);
    }

}
