<?php

include_once TICKET_LIB . '/Db.php';
include_once TICKET_LIB . '/Ask.php';

class Ask_postTest extends Ticket_Database_TestCase {

    protected $post;
    protected $session;

    public function setUp() {
        $this->post = array(
            'comment'   => 'how to write blog?',
            'ticketId'  => '1'
        );
        $this->session = array(
            'customerEmail' => 'test001@163.com'
        );
        parent::setUp();
    }

    public function getDataSet() {
        return $this->createArrayDataSet(array(
                    'customer'  => array(
                        array('id' => 1, 'name' => 'test001@163.com')
                    ),
                    'ticket'    => array(
                        array(
                            'id'            => 1,
                            'title'         => 'how to write blog?',
                            'description'   => 'RT. how to write blog?',
                            'user'          => 1,
                            'domain'        => 'ourblog.dev',
                            'status'        => 1
                        )
                    ),
                    'comment'   => array()
        ));
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Missing required comment
     */
    public function testCommentIsRequired() {
        unset($this->post['comment']);

        $ask = new Ask();
        $ask->post($this->post, $this->session);
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Missing required ticketId
     */
    public function testTicketIdIsRequired() {
        unset($this->post['ticketId']);

        $ask = new Ask();
        $ask->post($this->post, $this->session);
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

        $ask = new Ask();
        $ask->post($this->post, $this->session);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Comment max length 64000 and not empty
     */
    public function testCommentMinLength() {
        $comment = '';
        $this->post['comment'] = $comment;

        $ask = new Ask();
        $ask->post($this->post, $this->session);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid ticket id
     */
    public function testTicketIdInvalid() {
        $this->post['ticketId'] = 'ticketId';

        $ask = new Ask();
        $ask->post($this->post, $this->session);
    }

    public function testPostComment() {
        $ask = new Ask();
        $ask->post($this->post, $this->session);

        $expectedDataSet = $this->createArrayDataSet(include __DIR__ . '/expects/comment.php');

        $dataSet = $this->getConnection()->createDataSet(array('comment'));
        $filterDataSet = new PHPUnit_Extensions_Database_DataSet_DataSetFilter($dataSet);
        $filterDataSet->setExcludeColumnsForTable('comment', array('time'));

        $this->assertDataSetsEqual($expectedDataSet, $filterDataSet);
    }

}
