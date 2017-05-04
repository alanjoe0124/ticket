<?php

require_once __DIR__ . '/prevent_csrf.php';
header('Access-Control-Allow-Origin:http://ourblog.dev');

include __DIR__ . '/lib/Db.php';
include __DIR__ . '/lib/Ticket.php';

try {
    $ticket = new Ticket();
    $ticket->create($_POST);
    echo "success";
} catch (InvalidArgumentException $e) {
    echo "invalid argument";
} catch (Exception $e) {
    echo "server error";
}
