<?php

class Ticket_CreateTest extends Ticket_Database_TestCase
{
    protected $data;

    public function setUp()
    {
        $this->data = array(
            'title'         => 'how to write blog?',
            'description'   => 'RT. how to write blog?',
            'email'         => 'test002@163.com',
            'domain'        => 'ourblog.dev'
        );

        parent::setUp();
    }

    public function getDataSet()
    {
        return $this->createArrayDataSet(array(
            'customer' => array(
                array('id' => 1, 'email' => 'test001@163.com')
            ),
            'ticket'   => array()
        ));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage missing required key title
     */
    public function testTitleIsRequired()
    {
        unset($this->data['title']);

        $ticket = new OurTicket_Ticket();
        $ticket->create($this->data);
    }
    
    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage missing required key description
     */
    public function testDescriptionIsRequired()
    {
        unset($this->data['description']);

        $ticket = new OurTicket_Ticket();
        $ticket->create($this->data);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage missing required key email
     */
    public function testEmailIsRequired()
    {
        unset($this->data['email']);

        $ticket = new OurTicket_Ticket();
        $ticket->create($this->data);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage missing required key domain
     */
    public function testDomainIsRequired()
    {
        unset($this->data['domain']);

        $ticket = new OurTicket_Ticket();
        $ticket->create($this->data);
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage title required and maxlength is 500
     */
    public function testTitleCannotEmpty()
    {
        $this->data['title'] = '';
        $ticket = new OurTicket_Ticket();
        $ticket->create($this->data);
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage title required and maxlength is 500
     */
    public function testTitleMaxLength()
    {
        $this->data['title'] = str_pad('a', 501, 'A');
        $ticket = new OurTicket_Ticket();
        $ticket->create($this->data);
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage description too long, maxlength is 64000
     */
    public function testDescriptionLength()
    {
        $this->data['description'] = str_pad('a', 64001, 'A');
        $ticket = new OurTicket_Ticket();
        $ticket->create($this->data);
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage email minlength 4, maxlength 100
     */
    public function testEmailMinlengthIs4()
    {
        $this->data['email'] = 'aaa';
        $ticket = new OurTicket_Ticket();
        $ticket->create($this->data);
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage email minlength 4, maxlength 100
     */
    public function testEmailMaxLengthIs100()
    {
        $this->data['email'] = str_pad('a', 101, 'A');
        $ticket = new OurTicket_Ticket();
        $ticket->create($this->data);
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage invalid email
     */
    public function testInvalidEmail()
    {
        $this->data['email'] = 'ourblog.com';
        $ticket = new OurTicket_Ticket();
        $ticket->create($this->data);
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage INVALID_DOMAIN
     */
    public function testDomainInvalid()
    {
        $this->data['domain'] = 'ourblog.com';
        $ticket = new OurTicket_Ticket();
        $ticket->create($this->data);
    }
    
    public function testNewCustomerCreateTicket()
    {
        $ticket = new OurTicket_Ticket();
        $ticket->create($this->data);

        $expectedDataSet = $this->createArrayDataSet(include __DIR__ . '/expects/new-customer.php');

        $dataSet = $this->getConnection()->createDataSet(array('customer', 'ticket'));
        $filterDataSet = new PHPUnit_Extensions_Database_DataSet_DataSetFilter($dataSet);
        $filterDataSet->setExcludeColumnsForTable('ticket', array('time'));

        $this->assertDataSetsEqual($expectedDataSet, $filterDataSet);
    }

    public function testCustomerCreateTicket()
    {
        $this->data['email'] = 'test001@163.com';

        $ticket = new OurTicket_Ticket();
        $ticket->create($this->data);

        $expectedDataSet = $this->createArrayDataSet(include __DIR__ . '/expects/customer.php');

        $dataSet = $this->getConnection()->createDataSet(array('customer', 'ticket'));
        $filterDataSet = new PHPUnit_Extensions_Database_DataSet_DataSetFilter($dataSet);
        $filterDataSet->setExcludeColumnsForTable('ticket', array('time'));

        $this->assertDataSetsEqual($expectedDataSet, $filterDataSet);
    }
}
