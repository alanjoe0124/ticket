<?php

class Comment_postTest extends Ticket_Database_TestCase
{

    public function getDataSet()
    {
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
    public function testSessionUidIsRequired()
    {
        $ticket = new MyLib_Ticket(1, NULL, 2);
        $ticket->commentPost('Thanks for your ask');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Missing required comment
     */
    public function testCommentIsRequired()
    {
        $ticket = new MyLib_Ticket(1, 1, 2);
        $ticket->commentPost(NULL);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Missing required ticketId
     */
    public function testTicketIdIsRequired()
    {
        $ticket = new MyLib_Ticket(NULL, 1, 2);
        $ticket->commentPost('Thanks for your ask');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Comment max length 64000 and not empty
     */
    public function testCommentMaxLength()
    {
        $comment = '';
        for ($i = 0; $i < 64001; $i++) {
            $comment .= 'a';
        }

        $ticket = new MyLib_Ticket(1, 1, 2);
        $ticket->commentPost($comment);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Comment max length 64000 and not empty
     */
    public function testCommentMinLength()
    {
        $ticket = new MyLib_Ticket(1, 1, 2);
        $ticket->commentPost('');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid ticket id
     */
    public function testTicketIdInvalid()
    {
        $ticket = new MyLib_Ticket('abc', 1, 2);
        $ticket->commentPost('Thanks for your ask');
    }

    public function testPost()
    {
        $ticket = new MyLib_Ticket(1, 1, 2);
        $ticket->commentPost('Thanks for your ask');
        $expectedDataSet = $this->createArrayDataSet(array(
            'comment' => array(
                array(
                    'id' => 1,
                    'content' => 'Thanks for your ask',
                    'user' => 1,
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
