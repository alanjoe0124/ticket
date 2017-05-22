<?php

class MyLib_Csrf
{

    public static function prevent($referer)
    {
        if (!isset($referer)) {
            throw new InvalidArgumentException('Missing HTTP REFERER');
        }
        $referer = preg_match('#^http://([^/]+)#', $referer, $matches);
        $domainName = $matches[1];
        if ($domainName != 'ourblog.dev' && $domainName != 'ticket.dev') {
            throw new InvalidArgumentException('SERVER_NAME and HTTP_REFERER mismatch');
        }
    }

}
