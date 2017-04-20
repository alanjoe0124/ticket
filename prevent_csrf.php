<?php
try {
    if (!isset($_SERVER['HTTP_REFERER'])) {
        throw new InvalidArgumentException('Missing HTTP REFERER');
    }
    $referer = preg_match('#^http://([^/]+)#', $_SERVER['HTTP_REFERER'], $match);
    $domainName = $match[1];
    if (strlen($domainName) > 70) {
        throw new InvalidArgumentException('Domain name invalid');
    }
    if ($domainName != 'ourblog.dev' && $domainName != 'ticket.dev') {
        throw new InvalidArgumentException('SERVER_NAME and HTTP_REFERER mismatch');
    }
} catch (Exception $e) {
    exit();
}
 
