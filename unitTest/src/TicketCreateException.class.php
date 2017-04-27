<?php

require_once __DIR__ .'/../include/AutoLoad.php';

class TicketCreateException extends PHPUnit_Framework_TestCase {

    public function title_isset() {
        $request = new Request();
        $postTitile = $request->getPost('title');
        if (!isset($postTitile)) {
            throw new InvalidArgumentException('Required title is missing');
        }
    }

    public function description_isset() {
        $request = new Request();
        $postDesc = $request->getPost('description');
        if (!isset($postDesc)) {
            throw new InvalidArgumentException("Required description is missing");
        }
    }

    public function email_isset() {
        $request = new Request();
        $postEmail = $request->getPost('email');
        if (!isset($postEmail)) {
            throw new InvalidArgumentException("Required email is missing");
        }
    }

    public function domain_isset() {
        $request = new Request();
        $postDomain = $request->getPost('domain');
        if (!isset($postDomain)) {
            throw new InvalidArgumentException("Required domain is missing");
        }
    }

    public function title_length($title) {
        $titleLength = mb_strlen($title, "utf-8");
        if ($titleLength > 500 || $titleLength < 1) {
            throw new InvalidArgumentException('Title max length 500, min length 1');
        }
    }

    public function description_length($description) {
        if (strlen($description) > 64000) {
            throw new InvalidArgumentException('Max description is 64000');
        }
    }

    public function email_length($email) {
        $emailLength = strlen($email);
        if ($emailLength > 100 || $emailLength < 4) {
            throw new InvalidArgumentException('Email min length 4, max length 100');
        }
    }

    public function email_invalid($email) {
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);
        if (!$email) {
            throw new InvalidArgumentException('Email invalid');
        }
    }

    public function domain_invalid($domain) {
        if ($domain != 'ourblog.dev') {
            throw new InvalidArgumentException('Domain invalid');
        }
    }

}
