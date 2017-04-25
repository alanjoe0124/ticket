<?php
session_start();
require_once __DIR__ . '/../db.php';
if ($_POST) {
    require_once __DIR__ . '/../prevent_csrf.php';
    try {
        $formParam = array('comment', 'ticketId');
        foreach ($formParam as $key) {
            if (!isset($_POST[$key])) {
                throw new InvalidArgumentException("Missing required $key");
            }
        }
        $commentLength = strlen($_POST['comment']);
        if ($commentLength > 64000 || $commentLength == 0) {
            throw new InvalidArgumentException('Comment max length 64000 and not empty');
        }
        $ticketId = filter_var($_POST['ticketId'], FILTER_VALIDATE_INT, array(
            'options' => array('min_range' => 1)
        ));
        if (!$ticketId) {
            throw new InvalidArgumentException('Invalid ticket id');
        }
    } catch (Exception $e) {
        exit('Form Param error');
    }
    $sql = 'INSERT INTO comment (content, user, ticket_id, user_type) VALUES (?, ?, ?, ?)';
    $stmt = $db->prepare($sql);
    $stmt->execute(array($_POST['comment'], $_SESSION['uid'], $ticketId, 2)); // user_type ( 1 = > table(`customer`) , 2 => table (`user`)
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


