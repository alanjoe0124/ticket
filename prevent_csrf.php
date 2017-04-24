<?php
try {
    if (!isset($_SERVER['HTTP_REFERER'])) {
        throw new InvalidArgumentException('Missing HTTP REFERER');
    }
    $referer = preg_match('#^http://([^/]+)#', $_SERVER['HTTP_REFERER'], $matches);
    $domainName = $matches[1]; 
    if ($domainName != 'ourblog.dev' && $domainName != 'ticket.dev') {
        throw new InvalidArgumentException('SERVER_NAME and HTTP_REFERER mismatch');
    }
} catch (Exception $e) {
    exit;
}
 
