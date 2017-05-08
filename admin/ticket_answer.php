<?php

require_once __DIR__ . '/../prevent_csrf.php';
require_once __DIR__ . '/../lib/Db.php';
require_once __DIR__ . '/../lib/Ticket.php';
session_start();
if ($_POST) {
    try {
        $ticket = new Ticket();
        $ticketId = $ticket->commentPost($_POST['ticketId'], $_POST['comment'], $_SESSION['uid'], 2);
    } catch (InvalidArgumentException $e) {
        exit('Argument Invalid');
    } catch (Exception $e){
        exit('Server error');
    }
 
    header("Location:/admin/ticket_answer.php?ticket=$ticketId");
    exit;
}

try {
    if (!isset($_SESSION['uid'])) {
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
<?php include __DIR__ . '/admin_head.php'; ?>
<!--content_head end->

<!--contetn_body start-->
<div class="sidebox"> </div>
<div class="mainbox">
    <?php
        include __DIR__ . '/../common/ticket_info.php';
        include __DIR__ . '/../common/ticket_comments.php';
        include __DIR__ . '/../common/ticket_comment_form.php';
    ?>
</div>
</body>
</html>


