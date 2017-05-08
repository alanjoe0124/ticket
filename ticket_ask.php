<?php
require_once __DIR__ . '/prevent_csrf.php';
session_start();
include __DIR__ . '/lib/Db.php';

if ($_POST) {
    include __DIR__ . '/lib/Ticket.php';
    try {
        if (!isset($_SESSION['customerEmail'])) {
            throw new InvalidArgumentException('Missing required customerEmail');
        }

        $db = Db::getDb();
        $sql = 'SELECT id FROM customer WHERE name = ?';
        $stmt = $db->prepare($sql);
        $stmt->execute(array($_SESSION['customerEmail']));
        $userId = $stmt->fetchColumn();

        $ticket = new Ticket();
        $ticketId = $ticket->commentPost($_POST['ticketId'], $_POST['comment'], $userId, 1);
    } catch (InvalidArgumentException $e) {
        exit('Argument Invalid');
    } catch (Exception $e) {
        exit('Server error');
    }
    header("Location:/ticket_ask.php?ticket=$ticketId");
    exit;
}

try {
    if (!isset($_SESSION['customerEmail'])) {
        throw new InvalidArgumentException('Permission denied');
    }
    if (!isset($_GET['ticket'])) {
        throw new InvalidArgumentException('Missing required ticket id');
    }
    $ticketId = filter_var($_GET['ticket'], FILTER_VALIDATE_INT, array(
        'options' => array('min_range' => 1)
    ));
    if (!$ticketId) {
        throw new InvalidArgumentException('Invalid ticket id');
    }
} catch (Exception $e) {
    exit($e->getMessage());
}
?>
<!--content_head start--> 
<?php include __DIR__ . '/head.php'; ?>
<!--content_head end->

<!--contetn_body start-->
<div class="sidebox"> </div>
<div class="mainbox">
    <?php
    include __DIR__ . '/common/ticket_info.php';
    include __DIR__ . '/common/ticket_comments.php';
    include __DIR__ . '/common/ticket_comment_form.php';
    include __DIR__ . '/footer.php';
    ?>