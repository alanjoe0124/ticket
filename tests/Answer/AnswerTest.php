<?php

include_once TICKET_LIB . '/Db.php';
include_once TICKET_LIB . '/Answer.php';

class Answer_postTest extends Ticket_Database_TestCase {

    protected $post;
    protected $session;

    public function setUp() {
        $this->post = array(
            'comment'   => 'Thanks for your ask',
            'ticketId'  => 1
        );
        $this->session = array(
            'uid'       => 1
        );
        parent::setUp();
    }

    public function getDataSet() {
        return $this->createArrayDataSet(array(
                    'comment' => array()
        ));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Missing required comment
     */
    public function testCommentIsRequired() {
        unset($this->post['comment']);

        $answer = new Answer();
        $answer->post($this->post, $this->session);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Missing required ticketId
     */
    public function testTicketIdIsRequired() {
        unset($this->post['ticketId']);

        $answer = new Answer();
        $answer->post($this->post, $this->session);
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
        $this->post['comment'] = $comment;

        $answer = new Answer();
        $answer->post($this->post, $this->session);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Comment max length 64000 and not empty
     */
    public function testCommentMinLength() {
        $comment = '';
        $this->post['comment'] = $comment;

        $answer = new Answer();
        $answer->post($this->post, $this->session);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid ticket id
     */
    public function testTicketIdInvalid() {
        $this->post['ticketId'] = 'ticket';

        $answer = new Answer();
        $answer->post($this->post, $this->session);
    }

    public function testPostAnswerComment() {
        $answer = new Answer();
        $answer->post($this->post, $this->session);
        $expectedDataSet = $this->createArrayDataSet(include __DIR__ . '/expects/answer_comment.php');

        $dataSet = $this->getConnection()->createDataSet(array('comment'));
        $filterDataSet = new PHPUnit_Extensions_Database_DataSet_DataSetFilter($dataSet);
        $filterDataSet->setExcludeColumnsForTable('comment', array('time'));

        $this->assertDataSetsEqual($expectedDataSet, $filterDataSet);
    }

}
