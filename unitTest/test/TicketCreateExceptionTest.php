<?php

class TicketCreateExceptionTest extends PHPUnit_Framework_TestCase {

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Required title is missing
     */
    public function test_title_isset() {
        if (!isset($title)) {
            throw new InvalidArgumentException('Required title is missing');
        }
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Required description is missing
     */
    public function test_description_isset() {
        if (!isset($description)) {
            throw new InvalidArgumentException("Required description is missing");
        }
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Required email is missing
     */
    public function test_email_isset() {
        if (!isset($description)) {
            throw new InvalidArgumentException("Required email is missing");
        }
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Required domain is missing
     */
    public function test_domain_isset() {
        if (!isset($domain)) {
            throw new InvalidArgumentException("Required domain is missing");
        }
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
        $titleLength = mb_strlen($title, "utf-8");
        if ($titleLength > 500 || $titleLength < 1) {
            throw new InvalidArgumentException('Title max length 500, min length 1');
        }
    }

    public function descpritionProvider() {
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
     * @dataProvider descpritionProvider
     */
    public function test_description_length($description) {
        if (strlen($description) > 64000) {
            throw new InvalidArgumentException('Max description is 64000');
        }
    }

    public function variableLengthEmailProvider() {
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
     * @dataProvider variableLengthEmailProvider
     */
    public function test_email_length($email) {
        $emailLength = strlen($email);
        if ($emailLength > 100 || $emailLength < 4) {
            throw new InvalidArgumentException('Email min length 4, max length 100');
        }
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
    public function test_email_format($email) {
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);
        if (!$email) {
            throw new InvalidArgumentException('Email invalid');
        }
    }

    public function domainProvider() {
        return array(
            array('ourblog.com')
        );
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Domain invalid
     * @dataProvider domainProvider
     */
    public function test_domain($domain) {
        if ($domain != 'ourblog.dev') {
            throw new InvalidArgumentException('Domain invalid');
        }
    }

}
