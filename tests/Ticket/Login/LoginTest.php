<?php

include_once TICKET_LIB . '/Db.php';
include_once TICKET_LIB . '/Ticket.php';

class Ticket_LoginTest extends Ticket_Database_TestCase {

    protected $data;

    public function setUp() {
        $this->data = array(
            'userName'  => 'cs_user_1',
            'pwd'       => '123456'
        );

        parent::setUp();
    }

    public function getDataSet() {
        return $this->createArrayDataSet(array(
                    'user' => array(
                        array(
                            'id'    => 1,
                            'name'  => 'cs_user_1',
                            'pwd'   => 'a523683799664602dc130624df7fce8a'
                        )
                    )
        ));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Missing required userName
     */
    public function testUserNameIsRequired() {
        unset($this->data['userName']);

        $ticket = new Ticket();
        $userInfo = $ticket->login($this->data);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Missing required pwd
     */
    public function testPwdIsRequired() {
        unset($this->data['pwd']);

        $ticket = new Ticket();
        $userInfo = $ticket->login($this->data);
    }

    public function userNameProvider() {
        $data = '';
        for ($i = 0; $i < 51; $i++) {
            $data .= 'a';
        }
        return array(
            array($data),
            array('ab')
        );
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage User name max length 50, min length 3
     * @dataProvider userNameProvider
     */
    public function testUserNameLength($name) {
        $this->data['userName'] = $name;

        $ticket = new Ticket();
        $userInfo = $ticket->login($this->data);
    }

    public function pwdProvider(){
        $data = '';
        for($i = 0; $i < 41; $i++){
            $data .= 'a';
        }
        return array(
            array($data),
            array('1234')
        );
    }
    
    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Password max length 40, min length 5
     * @dataProvider pwdProvider
     */
    public function testPwdLength($pwd) {
        $this->data['pwd'] = $pwd;

        $ticket = new Ticket();
        $userInfo = $ticket->login($this->data);
    }

    public function testLogin() {
        $ticket = new Ticket();
        $userInfo = $ticket->login($this->data);
        $expectedResult = array('id' => 1, 'name' => 'cs_user_1');
        $this->assertEquals($expectedResult, $userInfo);
    }

}
