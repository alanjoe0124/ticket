<?php

class Csrf_Test extends PHPUnit_Framework_TestCase {

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Missing HTTP REFERER
     */
    public function testHttpRefererIsRequired() {
        $referer = NULL;
        ZendX_Csrf::prevent($referer);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage SERVER_NAME and HTTP_REFERER mismatch
     */
    public function testServerNameMismatch() {
        $referer = 'http://test.dev';
        ZendX_Csrf::prevent($referer);
    }

}
