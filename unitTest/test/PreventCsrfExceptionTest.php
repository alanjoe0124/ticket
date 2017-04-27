<?php

class PreventCsrfExceptionTest extends PHPUnit_Framework_TestCase {

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Missing HTTP REFERER
     */
    public function test_http_referer_isset() {
        if (!isset($httpReferer)) {
            throw new InvalidArgumentException('Missing HTTP REFERER');
        }
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage SERVER_NAME and HTTP_REFERER mismatch
     */
    public function test_domain_name() {
        $referer = preg_match('#^http://([^/]+)#', 'http://ourblog.de/index.php', $matches);
        $domainName = $matches[1];
        if ($domainName != 'ourblog.dev' && $domainName != 'ticket.dev') {
            throw new InvalidArgumentException('SERVER_NAME and HTTP_REFERER mismatch');
        }
    }

}
