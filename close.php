<?php

require_once __DIR__ . '/prevent_csrf.php';
include __DIR__ . '/lib/Db.php';
include __DIR__ . '/lib/Ticket.php';

session_start();
try {
    if (!isset($_SESSION['customerEmail'])) {
        throw new InvalidArgumentException('Missing required customerEmail');
    }
    if (!isset($_GET['ticket'])) {
        throw new InvalidArgumentException('Missing required ticket');
    }
    $ticketId = filter_var($_GET['ticket'], FILTER_VALIDATE_INT, array(
        'options' => array('min_range' => 1)
    ));
    if (!$ticketId) {
        throw new InvalidArgumentException('Ticket id is invalid');
    }
    $ticket = new Ticket();
    $ticket->close($_SESSION['customerEmail'], $ticketId);
} catch (InvalidArgumentException $e) {
    exit($e->getMessage());
} catch (Exception $e) {
    exit($e->getMessage());
}
header("Location:/ticket_ask.php?ticket=$ticketId");
exit;
