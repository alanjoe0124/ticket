<?php

class Comment_postTest extends Ticket_Database_TestCase {

    public function getDataSet() {
        return $this->createArrayDataSet(
                        array(
                            'comment' => array()
                        )
        );
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Missing required userId
     */
    public function testSessionUidIsRequired() {
        $ticket = new ZendX_Ticket();
        $ticket->commentPost(1, 'Thanks for your ask', NULL, 2);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Missing required comment
     */
    public function testCommentIsRequired() {
        $ticket = new ZendX_Ticket();
        $ticket->commentPost(1, NULL, 1, 2);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Missing required ticketId
     */
    public function testTicketIdIsRequired() {
        $ticket = new ZendX_Ticket();
        $ticket->commentPost(NULL, 'Thanks for your ask', 1, 2);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Comment max length 64000 and not empty
     */
    public function testCommentMaxLength() {
        $comment = '';
        for ($i = 0; $i < 64001; $i++) {
            $comment .= 'a';
        }

        $ticket = new ZendX_Ticket();
        $ticket->commentPost(1, $comment, 1, 2);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Comment max length 64000 and not empty
     */
    public function testCommentMinLength() {
        $ticket = new ZendX_Ticket();
        $ticket->commentPost(1, '', 1, 2);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid ticket id
     */
    public function testTicketIdInvalid() {
        $ticket = new ZendX_Ticket();
        $ticket->commentPost('abc', 'Thanks for your ask', 1, 2);
    }

    public function testPost() {
        $ticket = new ZendX_Ticket();
        $ticket->commentPost(1, 'Thanks for your ask', 1, 2);
        $expectedDataSet = $this->createArrayDataSet(array(
            'comment' => array(
                array(
                    'id'        => 1,
                    'content'   => 'Thanks for your ask',
                    'user'      => 1,
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

}