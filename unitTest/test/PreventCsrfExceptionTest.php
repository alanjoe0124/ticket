<?php
require_once __DIR__ .'/../include/AutoLoad.php';

class PreventCsrfExceptionTest extends PHPUnit_Framework_TestCase {

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Missing HTTP REFERER
     */
    public function test_http_referer_isset() {
        $preventCsrfException = new PreventCsrfException();
        $preventCsrfException->http_referer_isset();
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage SERVER_NAME and HTTP_REFERER mismatch
     */
    public function test_domain_name() {
        $preventCsrfException = new PreventCsrfException();
        $preventCsrfException->domain_name();
    }

}
