<?php

session_start();
if (!isset($_SESSION['customerId'])) {
    exit;
}

include __DIR__ . '/lib/OurTicket/Db.php';
include __DIR__ . '/lib/OurTicket/Util.php';
include __DIR__ . '/lib/OurTicket/Ticket.php';

try {
    OurTicket_Util::killCSRF();
    $ticketId = OurTicket_Util::getQuery('id');
    OurTicket_Ticket::close($ticketId, $_SESSION['customerId']);
} catch (InvalidArgumentException $e) {
    die('invalid params');
} catch (Exception $e) {
    die('server error');
}

header("Location: /ticket_comment.php?id=$ticketId");
