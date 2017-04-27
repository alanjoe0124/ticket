<?php

class PreventCsrfException extends PHPUnit_Framework_TestCase {

    public function http_referer_isset() {
        $request = new Request();
        $httpReferer = $request->getServer('HTTP_REFERER');
        if (!isset($httpReferer)) {
            throw new InvalidArgumentException('Missing HTTP REFERER');
        }
    }

    public function domain_name() {
        $referer = preg_match('#^http://([^/]+)#', 'http://ourblog.de/index.php', $matches);
        $domainName = $matches[1];
        if ($domainName != 'ourblog.dev' && $domainName != 'ticket.dev') {
            throw new InvalidArgumentException('SERVER_NAME and HTTP_REFERER mismatch');
        }
    }

}
