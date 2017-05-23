<?php

class Auth_Test extends Ticket_Database_TestCase {

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

        $auth = new MyLib_Auth(NULL, $this->data['pwd']);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Missing required password
     */
    public function testPwdIsRequired() {
        unset($this->data['pwd']);
        $auth = new MyLib_Auth($this->data['userName'], NULL);
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
        $auth = new MyLib_Auth($this->data['userName'], $this->data['pwd']);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage User name max length 50, min length 3
     */
    public function testUserNameMinLength() {
        $this->data['userName'] = 'ab';

        $auth = new MyLib_Auth($this->data['userName'], $this->data['pwd']);
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

        $auth = new MyLib_Auth($this->data['userName'], $this->data['pwd']);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Password max length 40, min length 5
     */
    public function testPwdMinLength() {
        $this->data['pwd'] = '1234';

        $auth = new MyLib_Auth($this->data['userName'], $this->data['pwd']);
    }

    public function testRightPwd() {
        $auth = new MyLib_Auth($this->data['userName'], $this->data['pwd']);
        $authResult = $auth->authenticate();
        $this->assertTrue($authResult->isValid());
    }

    public function testWrongPwd() {
        $auth = new MyLib_Auth($this->dataWithWrongPwd['userName'], $this->dataWithWrongPwd['pwd']);
        $authResult = $auth->authenticate();
        $this->assertFalse($authResult->isValid());
    }

    public function testNoExistUser() {
        $auth = new MyLib_Auth($this->dataWithNoExistUser['userName'], $this->dataWithNoExistUser['pwd']);
        $authResult = $auth->authenticate();
        $this->assertFalse($authResult->isValid());
    }
    
    public function testGetUserName(){
        $auth = new MyLib_Auth($this->data['userName'], $this->data['pwd']);
        $this->assertEquals($this->data['userName'], $auth->getUserName());
    }

}
