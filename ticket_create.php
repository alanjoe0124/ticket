<?php

include __DIR__ . '/lib/OurTicket/Db.php';
include __DIR__ . '/lib/OurTicket/Util.php';
include __DIR__ . '/lib/OurTicket/Ticket.php';

try {
    $ticket = new OurTicket_Ticket();
    $ticket->create($_POST);
    $res = 'success';
} catch (InvalidArgumentException $e) {
    $res = $e->getMessage();
} catch (Exception $e) {
    $res = 'server error';
}

if ($res == 'INVALID_DOMAIN') {
    exit;
}

header('Access-Control-Allow-Origin: http://ourblog.dev');
echo $res;
