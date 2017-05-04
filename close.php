<?php

require_once __DIR__ . '/prevent_csrf.php';
include __DIR__ . '/lib/Db.php';
include __DIR__ . '/lib/Ticket.php';

session_start();
try {
    $ticket = new Ticket();
    $ticketId = $ticket->close($_GET, $_SESSION);
} catch (InvalidArgumentException $e) {
    exit($e->getMessage());
} catch (Exception $e) {
    exit($e->getMessage());
}
header("Location:/ticket_ask.php?ticket=$ticketId");
exit;
