<?php
require_once __DIR__ . '/prevent_csrf.php';
session_start();
include __DIR__ . '/lib/Db.php';

if ($_POST) {
    include __DIR__ . '/lib/Ask.php';
    try {
        $ask = new Ask();
        $ticketId = $ask->post($_POST, $_SESSION);
    } catch (InvalidArgumentException $e) {
        exit('Form Param error');
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