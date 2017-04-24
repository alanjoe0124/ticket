<?php
session_start();
try {
    require_once __DIR__ . '/prevent_csrf.php';
    if (!isset($_SESSION['customerEmail'])) {
        throw new InvalidArgumentException("Permission denied");
    }
    if(!isset($_GET['action'])){
        throw new InvalidArgumentException("Missing required action");
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
            INNER JOIN user ON ticket.user = user.id
        WHERE 
            user.name = ? AND ticket.id = ?";
$stmt = $db->prepare($sql);
$stmt->execute( array($_SESSION['customerEmail'], $ticketId) );
$status = $stmt->fetchColumn();
if (!$status) {
    exit('No authority to operate this ticket');
}
if ($_GET['action'] == 'close'){
    $updateStatus = 2; // 2 => 'close'
}else{
    $updateStatus = 1; // 1 => 'reactivate'
}
if($status != $updateStatus){
    $sql = $db->exec("UPDATE ticket SET status = $updateStatus WHERE id = $ticketId");  
}
header("Location:/ticket_ask.php?ticket=$ticketId");
