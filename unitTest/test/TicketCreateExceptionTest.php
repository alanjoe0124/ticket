<?php

class TicketCreateExceptionTest extends PHPUnit_Framework_TestCase {

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Required title is missing
     */
    public function test_title_isset() {
        $ticketException = new TicketCreateException();
        $ticketException->title_isset();
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Required description is missing
     */
    public function test_description_isset() {
        $ticketException = new TicketCreateException();
        $ticketException->description_isset();
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Required email is missing
     */
    public function test_email_isset() {
        $ticketException = new TicketCreateException();
        $ticketException->email_isset();
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Required domain is missing
     */
    public function test_domain_isset() {
        $ticketException = new TicketCreateException();
        $ticketException->domain_isset();
    }

    public function titleProvider() {
        $data = '';
        for ($i = 0; $i < 501; $i++) {
            $data .= 'a';
        }
        return array(
            array($data),
            array('')
        );
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Title max length 500, min length 1
     * @dataProvider titleProvider
     */
    public function test_title_length($title) {
        $ticketException = new TicketCreateException();
        $ticketException->title_length($title);
    }

    public function descLengthProvider() {
        $data = '';
        for ($i = 0; $i < 64001; $i++) {
            $data .= 'a';
        }
        return array(
            array($data)
        );
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Max description is 64000
     * @dataProvider descLengthProvider
     */
    public function test_description_length($description) {
        $ticketException = new TicketCreateException();
        $ticketException->description_length($description);
    }

    public function emailLengthProvider() {
        $data = '';
        for ($i = 0; $i < 101; $i++) {
            $data .= 'a';
        }
        return array(
            array($data),
            array('abc')
        );
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Email min length 4, max length 100
     * @dataProvider emailLengthProvider
     */
    public function test_email_length($email) {
        $ticketException = new TicketCreateException();
        $ticketException->email_length($email);
    }

    public function invalidEmailProvider() {
        return array(
            array('test.com'),
            array('abc')
        );
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Email invalid
     * @dataProvider invalidEmailProvider
     */
    public function test_email_invalid($email) {
        $ticketException = new TicketCreateException();
        $ticketException->email_invalid($email);
    }
 
    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Domain invalid
     */
    public function test_domain_invalid() {
        $ticketException = new TicketCreateException();
        $ticketException->domain_invalid('ourblog.com');
    }

}
