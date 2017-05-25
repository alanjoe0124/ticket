<?php
/**
 * @group close
 */
class Ticket_CloseTest extends Ticket_Database_TestCase
{
    public function getDataSet()
    {
        return $this->createArrayDataSet(include __DIR__ . '/fixtures.php');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage invalid ticketId
     */
    public function testInvalidTicketId()
    {
        OurTicket_Ticket::close('xxx', 1);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage ticketId not exists or not your ticket
     */
    public function testNotExistTicketId()
    {
        OurTicket_Ticket::close(3000, 1);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage ticketId not exists or not your ticket
     */
    public function testCloseOthersTicket()
    {
        OurTicket_Ticket::close(2, 1);
    }

    public function testCloseAlreadyClosedTicket()
    {
        OurTicket_Ticket::close(3, 1);

        $expectedDataSet = $this->createArrayDataSet(include __DIR__ . '/fixtures.php');
        $dataSet = $this->getConnection()->createDataSet(array('ticket'));

        $this->assertDataSetsEqual($expectedDataSet, $dataSet);
    }

    public function testCloseTicket()
    {
        OurTicket_Ticket::close(1, 1);

        $expectedDataSet = $this->createArrayDataSet(include __DIR__ . '/expects.php');
        $dataSet = $this->getConnection()->createDataSet(array('ticket'));

        $this->assertDataSetsEqual($expectedDataSet, $dataSet);
    }
}
