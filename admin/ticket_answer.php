<?php
session_start();
if (!isset($_SESSION['uid'])) {
    throw new InvalidArgumentException("Permission denied");
}
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
    $sql = "INSERT INTO comment (content, user, ticket_id) VALUES (?, ?, ?)";
    $stmt = $db->prepare($sql);
    $stmt->execute(array($_POST['comment'], $_SESSION['uid'], $ticketId));
    header("Location:/admin/ticket_answer.php?ticket=$ticketId");
    exit();
}

try {
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
                        <p>    
                            <?php
                            echo '<a href="/admin/ticket_manage.php"><h1>Ticket</h1></a>
                                  <p>user:' . $_SESSION['userName'] . '&nbsp;/&nbsp;
                                   <a href="/admin/logout.php">logout</a></p>';
                            ?> 
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
                $sql = "SELECT user.name AS user, 
                               ticket.id AS id,
                               ticket.title, 
                               ticket.description, 
                               ticket.time,
                               status.name AS status
                        FROM   user INNER JOIN ticket ON user.id = ticket.user
                                      INNER JOIN status ON ticket.status = status.id 
                        WHERE 
                               ticket.id = ?";
                $stmt = $db->prepare($sql);
                $stmt->execute(array($ticketId));
                $ticketRow = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($ticketRow) {
                    echo '<div>ticket info:</div><HR width="100%">';
                    echo '<div>
                            <table>
                                <tr>
                                    <th>title</th>
                                    <th>description</th>
                                    <th>customer</th>
                                    <th>status</th>
                                    <th>time</th>
                                </tr>
                                <tr>
                                    <td >' . htmlspecialchars($ticketRow['title']) . '</td>
                                    <td>' . htmlspecialchars($ticketRow['description']) . '</td>
                                    <td>' . htmlspecialchars($ticketRow['user']) . '</td>
                                    <td>' . $ticketRow['status'] . '</td>
                                    <td>' . $ticketRow['time'] . '</td>       
                                </tr>
                            </table>
                         </div>';
                }
require_once __DIR__ . '/../common/ticket_info_common.php';

