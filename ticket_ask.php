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
        if (strlen($_POST['comment']) > 64000) {
            throw new InvalidArgumentException("Comment max length 64000");
        }
        $ticketId = filter_var($_POST['ticketId'], FILTER_VALIDATE_INT, array(
            'options' => array('min_range' => 1)
        ));
        if (isset($_SESSION['uid'])) {
            $user = $_SESSION['userName'];
        } else if (isset($_SESSION['customerEmail'])) {
            $user = $_SESSION['customerEmail'];
        } else {
            throw new InvalidArgumentException("Permission denied");
        }
    } catch (Exception $e) {
        exit('Form Param error');
    }
    if (isset($_SESSION['customerEmail'])) {
        $sql = "SELECT * FROM ticket WHERE email = ? AND id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute(array($user, $ticketId));
        if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
            exit('No authority to operate this ticket');
        }
    }
    $sql = "INSERT INTO comment (content, time, user, ticket_id) VALUES (?, ?, ?, ?)";
    $stmt = $db->prepare($sql);
    $stmt->execute(array($_POST['comment'], date("Y-m-d H:i:s"), $user, $ticketId));
    header("Location:/ticket_detail.php?ticket=$ticketId");
    exit();
}

try {
    if (!isset($_SESSION['customerEmail']) && !isset($_SESSION['uid'])) {
        throw new InvalidArgumentException("Permission denied");
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
                        if (!empty($_SESSION['uid'])) { 
                            echo '<a href="/admin/ticket_manage.php"><h1>Ticket</h1></a> <p>user:' . $_SESSION['userName'] . '&nbsp;/&nbsp;<a href="/admin/logout.php">logout</a></p>';
                        } else {
                            $sql = "SELECT * FROM ticket WHERE email = ? AND id = ?";
                            $stmt = $db->prepare($sql);
                            $stmt->execute(array($_SESSION['customerEmail'], $ticketId));
                            if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
                                exit('No authority to operate this ticket');
                            }
                            echo '<a href="/index.php?email='.$_SESSION['customerEmail'].'"><h1>Ticket</h1></a><p>user:' . $_SESSION['customerEmail'] . '</p>';
                        }
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
                $sql = "SELECT ticket.id AS id, title, description, status.name AS status, email, time FROM ticket JOIN status ON ticket.status = status.id WHERE ticket.id = ?";
                $stmt = $db->prepare($sql);
                $stmt->execute(array($ticketId));
                $ticketRow = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($ticketRow) {
                    echo '<div>ticket info:'; 
                    if (isset($_SESSION['customerEmail'])) {
                        if ($ticketRow['status'] == "pending") {
                            echo '<a href="/status_change.php?ticket=' . $ticketId . '&action=close"><button>close</button></a>';
                        } else {
                            echo '<a href="/status_change.php?ticket=' . $ticketId . '&action=reactivate"><button>reactivate</button></a>';
                        }
                    }
                    echo '</div><HR width="100%">';
                    echo '<div><table><tr><th>title</th><th>description</th><th>customer</th><th>status</th><th>time</th></tr><tr>
                                  <td >' . htmlspecialchars($ticketRow['title']) . '</td>
                                  <td>' . htmlspecialchars($ticketRow['description']) . '</td>
                                  <td>' . htmlspecialchars($ticketRow['email']) . '</td>
                                  <td>' . $ticketRow['status'] . '</td>
                                  <td>' . $ticketRow['time'] . '</td>
                         </tr></table></div>';
                }
                $sql = "SELECT * FROM comment WHERE ticket_id = ? ORDER BY time ASC";
                $stmt = $db->prepare($sql);
                $stmt->execute(array($ticketId));
                $commentRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if ($commentRows) {
                    echo '<HR width="100%"> <div>comments:</div>';
                    foreach ($commentRows as $commentRow) {
                        echo '<HR width="100%"><div><table><tr><th>user</th><th>comment</th><th>time</th>
                            <tr><td>' . htmlspecialchars($commentRow['user']) . '</td><td>' . htmlspecialchars($commentRow['content']) . '</td><td>' . $commentRow['time'] . '</td></tr></table>
                            </div>';
                    }
                }
                ?>
                <HR width="100%">
                <form method="POST">
                    <div id="content" class="row-text">
                        text:<textarea name = "comment" rows = "10"  placeholder = "text..."></textarea>
                        <input type = "hidden" name = "ticketId" value = "<?php echo $ticketId; ?>"/> 
                        <button type = "submit">submit</button>
                    </div>
                </form>
            </div>
            <div class="sidebox"></div>
            <!--contetn_body end-->
        </div>
    </body>
</html>

