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
     * @expectedExceptionMessage missing required key name
     */
    public function testNameIsRequired() 
    {
        OurTicket_Login::doLogin(array('pwd' => '123456'));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage missing required key pwd
     */
    public function testPwdIsRequired()
    {
        OurTicket_Login::doLogin(array('name' => 'cs_user_1'));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage name required and maxlength is 100
     */
    public function testNameCannotEmpty()
    {
        OurTicket_Login::doLogin(array(
            'name' => '',
            'pwd'  => '123456'
        ));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage name required and maxlength is 100
     */
    public function testNameMaxlengthIs100()
    {
        OurTicket_Login::doLogin(array(
            'name' => str_pad('cs_user_1', 101, 'A'),
            'pwd'  => '123456'
        ));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage pwd minlength is 5, maxlength is 40
     */
    public function testPwdMinLengthIs5()
    {
        OurTicket_Login::doLogin(array(
            'name' => 'cs_user_1',
            'pwd'  => '1234'
        ));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage pwd minlength is 5, maxlength is 40
     */
    public function testPwdMaxLengthIs40()
    {
        OurTicket_Login::doLogin(array(
            'name' => 'cs_user_1',
            'pwd'  => str_pad('123456', 41, 'A')
        ));
    }

    public function testLoginWithWrongName()
    {
        $row = OurTicket_Login::doLogin(array(
            'name' => 'cs_user_2',
            'pwd'  => '123456'
        ));
        $this->assertEquals(false, $row);
    }

    public function testLoginWithWrongPwd() 
    {
        $row = OurTicket_Login::doLogin(array(
            'name' => 'cs_user_1',
            'pwd'  => '1234567'
        ));
        $this->assertEquals(false, $row);
    }

    public function testLogin()
    {
        $row = OurTicket_Login::doLogin(array(
            'name' => 'cs_user_1',
            'pwd'  => '123456'
        ));

        $expected = array(
            'id'   => 1, 
            'name' => 'cs_user_1'
        );

        $this->assertEquals($expected, $row);
    }
}