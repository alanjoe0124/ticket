<?php
/**
 * @group comment
 */
class Ticket_CommentTest extends Ticket_Database_TestCase
{
    public function getDataSet()
    {
        return $this->createArrayDataSet(array(
            'ticket' => array(
                array(
                    'id'          => 1,
                    'title'       => 'how to write blog?',
                    'description' => 'RT. how to write blog?',
                    'customer_id' => 1,
                    'domain'      => 'ourblog.dev',
                    'status_id'   => 1,
                    'time'        => '2017-05-24 16:00:00'
                ),
                array(
                    'id'          => 2,
                    'title'       => 'how to reply blog?',
                    'description' => 'RT. how to reply blog?',
                    'customer_id' => 2,
                    'domain'      => 'ourblog.dev',
                    'status_id'   => 1,
                    'time'        => '2017-05-24 16:00:00'
                )
            ),
            'comment' => array()
        ));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage no such ticketId
     */
    public function testTicketIdShouldExist()
    {
        OurTicket_Ticket::customerAddComment(4, 'comment bla bla bla ...', 1);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage invalid ticketId
     */
    public function testTicketIdShouldValid()
    {
        OurTicket_Ticket::customerServiceAddComment('abc', 'comment bla bla bla ...', 1);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage not your ticket
     */
    public function testTicketNotBelongToOperator()
    {
        OurTicket_Ticket::customerAddComment( 2, 'comment bla bla bla ...', 1);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedException comment required and maxlength is 3000
     */
    public function testCommentCannotEmpty()
    {
        OurTicket_Ticket::customerServiceAddComment(1, '', 1);
    }
    
    /**
     * @expectedException InvalidArgumentException
     * @expectedException comment required and maxlength is 3000
     */
    public function testCommentMaxLengthIs3000()
    {
        OurTicket_Ticket::customerServiceAddComment(
            1,
            str_pad('comment bla bla bla ...', 3001, 'A'),
            1
        );
    }

    public function testCustomerServiceAddComment()
    {
        OurTicket_Ticket::customerServiceAddComment(1, 'comment bla bla bla ...', 1);

        $expectedDataSet = $this->createArrayDataSet(array(
            'comment' => array(
                array(
                    'id'        => 1,
                    'content'   => 'comment bla bla bla ...',
                    'user_id'   => 1,
                    'ticket_id' => 1,
                    'user_type' => 2
                )
            )
        ));

        $dataSet = $this->getConnection()->createDataSet(array('comment'));
        $filterDataSet = new PHPUnit_Extensions_Database_DataSet_DataSetFilter($dataSet);
        $filterDataSet->setExcludeColumnsForTable('comment', array('time'));

        $this->assertDataSetsEqual($expectedDataSet, $filterDataSet);
    }

    public function testCustomerAddComment()
    {
        OurTicket_Ticket::customerAddComment(1, 'comment bla bla bla ...', 1);

        $expectedDataSet = $this->createArrayDataSet(array(
            'comment' => array(
                array(
                    'id'        => 1,
                    'content'   => 'comment bla bla bla ...',
                    'user_id'   => 1,
                    'ticket_id' => 1,
                    'user_type' => 1
                )
            )
        ));

        $dataSet = $this->getConnection()->createDataSet(array('comment'));
        $filterDataSet = new PHPUnit_Extensions_Database_DataSet_DataSetFilter($dataSet);
        $filterDataSet->setExcludeColumnsForTable('comment', array('time'));

        $this->assertDataSetsEqual($expectedDataSet, $filterDataSet);
    }
}
