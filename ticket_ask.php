<?php
session_start();
require_once __DIR__ . '/db.php';
if ($_POST) {
    require_once __DIR__ . '/prevent_csrf.php';
    try {
        $formParam = array('comment', 'ticketId');
        foreach ($formParam as $key) {
            if (!isset($_POST[$key])) {
                throw new InvalidArgumentException("Missing required $key");
            }
        }
        $commentLength = strlen($_POST['comment']);
        if ($commentLength > 64000 || $commentLength == 0) {
            throw new InvalidArgumentException("Comment max length 64000 and not empty");
        }
        $ticketId = filter_var($_POST['ticketId'], FILTER_VALIDATE_INT, array(
            'options' => array('min_range' => 1)
        ));
        if (!$ticketId) {
            throw new InvalidArgumentException("Invalid ticket id");
        }
    } catch (Exception $e) {
        exit('Form Param error');
    }
    $sql = "SELECT id FROM customer WHERE name = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute(array($_SESSION['customerEmail']));
    $userId = $stmt->fetchColumn();

    $sql = "INSERT INTO comment (content, user, ticket_id) VALUES (?, ?, ?)";
    $stmt = $db->prepare($sql);
    $stmt->execute(array($_POST['comment'], $userId, $ticketId));
    header("Location:/ticket_ask.php?ticket=$ticketId");
    exit();
}

try {
    if (!isset($_SESSION['customerEmail'])) {
        throw new InvalidArgumentException("Permission denied");
    }
    if (!isset($_GET['ticket'])) {
        throw new InvalidArgumentException("Missing required ticket id");
    }
    $ticketId = filter_var($_GET['ticket'], FILTER_VALIDATE_INT, array(
        'options' => array('min_range' => 1)
    ));
    if (!$ticketId) {
        throw new InvalidArgumentException("Invalid ticket id");
    }
} catch (Exception $e) {
    exit($e->getMessage());
}
?>
<html>
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" type="text/css" href="/common/css/main.css">
    </head>
    <body>
        <div class="container">
            <!--content_head start-->
            <div class="headbox">
                <div class="head-side-box"></div>
                <div class="head-main-box">
                    <div class="head-title"> 
                        <a href="/index.php?email=<?php echo $_SESSION['customerEmail']; ?>"><h1>Ticket</h1></a>
                        <p>user:<?php echo $_SESSION['customerEmail']; ?> / <a href="/customer_logout.php">logout</a></p>
                    </div>
                    <HR width="100%">
                </div>
                <div class="head-side-box"></div>
            </div> 
            <!--content_head end->
            
            <!--contetn_body start-->
            <div class="sidebox"> </div>
            <div class="mainbox">
                <?php
                include __DIR__ . '/common/ticket_info.php';
                include __DIR__ . '/common/ticket_comments.php';
                include __DIR__ . '/common/ticket_comment_form.php';
                ?>
            </div>
    </body>
</html>
