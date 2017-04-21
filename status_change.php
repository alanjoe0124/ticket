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
    if(strlen($_GET['action']) > 10){
        throw new InvalidArgumentException("Action code max length 10");
    }
    if($_GET['action'] != 'close' && $_GET['action'] !='reactivate'){
        throw new InvalidArgumentException("Undefined action");
    }
    if (!isset($_GET['ticket'])) {
        throw new InvalidArgumentException("Ticket invalid");
    }
    $ticketId = filter_var($_GET['ticket'], FILTER_VALIDATE_INT, array(
        'options' => array('min_range' => 1)
    ));
} catch (Exception $e) {
    exit($e->getMessage());
}

require_once __DIR__ . '/db.php';
$sql = "SELECT * FROM ticket WHERE email = ? AND id = ?";
$stmt = $db->prepare($sql);
$stmt->execute(array($_SESSION['customerEmail'], $ticketId));
if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
    exit('No authority to operate this ticket');
}
if($_GET['action'] == 'close'){
    $status = 2;
}else{
    $status = 1;
}
$sql = "UPDATE ticket SET status = ".$status." WHERE id = $ticketId";
$stmt = $db->prepare($sql);
$stmt->execute();
header("Location:/ticket_detail.php?ticket=$ticketId");
