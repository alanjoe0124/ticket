<?php

class LoginTest extends Ticket_Database_TestCase
{
    public function getDataSet()
    {
        return $this->createArrayDataSet(array(
            'user' => array(
                array(
                    'id'   => 1,
                    'name' => 'cs_user_1',
                    'pwd'  => md5('123456' . OurTicket_Login::SALT)
                )
            )
        ));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage missing required userName
     */
    public function testNameIsRequired() 
    {
        $login = new OurTicket_Login(NULL, '123456');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage missing required password
     */
    public function testPwdIsRequired()
    {
        $login = new OurTicket_Login('cs_user_1', NULL);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage name required and maxlength is 100
     */
    public function testNameCannotEmpty()
    {
        $login = new OurTicket_Login('', '123456');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage name required and maxlength is 100
     */
    public function testNameMaxlengthIs100()
    {
        $login = new OurTicket_Login(str_pad('cs_user_1', 101, 'A'), '123456');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage pwd minlength is 5, maxlength is 40
     */
    public function testPwdMinLengthIs5()
    {
        $login = new OurTicket_Login('cs_user_1', '1234');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage pwd minlength is 5, maxlength is 40
     */
    public function testPwdMaxLengthIs40()
    {
        $login = new OurTicket_Login('cs_user_1', str_pad('123456', 41, 'A'));
    }

    public function testLoginWithWrongName()
    {
        $login = new OurTicket_Login('cs_user_2','123456');
        $auth = Zend_Auth::getInstance();
        $result = $auth->authenticate($login);
        $this->assertFalse($result->isValid());
    }

    public function testLoginWithWrongPwd()
    {
        $login = new OurTicket_Login('cs_user_1','1234567');
        $auth = Zend_Auth::getInstance();
        $result = $auth->authenticate($login);
        $this->assertFalse($result->isValid());
    }

    public function testLogin()
    {
        $login = new OurTicket_Login('cs_user_1','123456');
        $auth = Zend_Auth::getInstance();
        $result = $auth->authenticate($login);
        $this->assertTrue($result->isValid());
    }
}