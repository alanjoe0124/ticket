<?php

include_once TICKET_LIB . '/Db.php';
include_once TICKET_LIB . '/Login.php';

class Login_checkTest extends Ticket_Database_TestCase {

    protected $data;
    protected $dataWithWrongPwd;
    protected $dataWithNoExistUser;

    public function setUp() {
        $this->data = array(
            'userName' => 'cs_user_1',
            'pwd' => '123456'
        );
        $this->dataWithWrongPwd = array(
            'userName' => 'cs_user_1',
            'pwd' => '654321'
        );
        $this->dataWithNoExistUser = array(
            'userName' => 'new_user',
            'pwd' => '123456'
        );
        parent::setUp();
    }

    public function getDataSet() {
        return $this->createArrayDataSet(array(
                    'user' => array(
                        array(
                            'id' => 1,
                            'name' => 'cs_user_1',
                            'pwd' => 'a523683799664602dc130624df7fce8a'
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

        $login = new Login();
        $userInfo = $login->check($this->data);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Missing required pwd
     */
    public function testPwdIsRequired() {
        unset($this->data['pwd']);

        $login = new Login();
        $userInfo = $login->check($this->data);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage User name max length 50, min length 3
     */
    public function testUserNameMaxLength() {
        $name = '';
        for ($i = 0; $i < 51; $i++) {
            $name .= 'a';
        }
        $this->data['userName'] = $name;

        $login = new Login();
        $userInfo = $login->check($this->data);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage User name max length 50, min length 3
     */
    public function testUserNameMinLength() {
        $this->data['userName'] = 'ab';

        $login = new Login();
        $userInfo = $login->check($this->data);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Password max length 40, min length 5
     */
    public function testPwdMaxLength() {
        $pwd = '';
        for ($i = 0; $i < 41; $i++) {
            $pwd .= 'a';
        }
        $this->data['pwd'] = $pwd;

        $login = new Login();
        $userInfo = $login->check($this->data);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Password max length 40, min length 5
     */
    public function testPwdMinLength() {
        $this->data['pwd'] = '1234';

        $login = new Login();
        $userInfo = $login->check($this->data);
    }

    public function testRightPwd() {
        $login = new Login();
        $userInfo = $login->check($this->data);
        $expectedResult = array('id' => 1, 'name' => 'cs_user_1');
        $this->assertEquals($expectedResult, $userInfo);
    }

    public function testWrongPwd() {
        $login = new Login();
        $userInfo = $login->check($this->dataWithWrongPwd);
        $this->assertFalse($userInfo);
    }

    public function testNoExistUser() {
        $login = new Login();
        $userInfo = $login->check($this->dataWithNoExistUser);
        $this->assertFalse($userInfo);
    }
    
}
