<?php 
session_start();
try {
    require_once __DIR__ . '/prevent_csrf.php';
    if (!isset($_SESSION['customerEmail'])) {
        throw new InvalidArgumentException("Permission denied");
    }
    if (!isset($_GET['ticket'])) {
        throw new InvalidArgumentException("Missing required ticket");
    }
    $ticketId = filter_var($_GET['ticket'], FILTER_VALIDATE_INT, array(
        'options' => array('min_range' => 1)
    ));
} catch (Exception $e) {
    exit($e->getMessage());
}

require_once __DIR__ . '/db.php';
$sql = "SELECT ticket.status
        FROM ticket 
            INNER JOIN customer ON ticket.user = customer.id
        WHERE 
            customer.name = ? AND ticket.id = $ticketId";
$stmt = $db->prepare($sql);
$stmt->execute(array($_SESSION['customerEmail']));
$status = $stmt->fetchColumn();
if (!$status) {
    exit('No authority to operate this ticket');
}
if ($status != 2) { // ticket status ( 1 => pending, 2 => close ) 
    $sql = $db->exec("UPDATE ticket SET status = 2 WHERE id = $ticketId");
}
header("Location:/ticket_ask.php?ticket=$ticketId");
exit;