<?php

require_once __DIR__ . '/../prevent_csrf.php';
require_once __DIR__ . '/../lib/Db.php';
require_once __DIR__ . '/../lib/Answer.php';
session_start();
if ($_POST) {
    try {
        $answer = new Answer();
        $ticketId = $answer->post($_POST, $_SESSION);
    } catch (InvalidArgumentException $e) {
        exit('Form Param error');
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


