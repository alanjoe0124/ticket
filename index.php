<?php
try {
    if (!isset($_GET['email'])) {
        throw new InvalidArgumentException('Email invalid');
    }
    $emailLength = strlen($_GET['email']);
    if ($emailLength > 100 || $emailLength < 4) {
        throw new InvalidArgumentException("Email min length 4, max length 100");
    }
    $email = filter_var($_GET['email'], FILTER_VALIDATE_EMAIL);
    if (!$email) {
        throw new InvalidArgumentException("Email format wrong!");
    }
} catch (Exception $e) {
    exit("Param error");
}
session_start();
if (!isset($_SESSION['cusomterEmail'])) {
    session_regenerate_id();
    $_SESSION['customerEmail'] = $email;
}
?>
<html>
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" type="text/css" href="/common/css/main.css">
        <script   src="https://code.jquery.com/jquery-3.2.1.min.js"></script>

    </head>
    <body>
        <div class="container">
            <!--content_head start-->
            <div class="headbox">
                <div class="head-side-box"></div>
                <div class="head-main-box">
                    <div class="head-title">
                        <p><h1>Ticket</h1>
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
                require_once __DIR__ . '/db.php';
                $sql = "SELECT ticket.id as id,title,status.name as status FROM ticket JOIN status ON ticket.status = status.id WHERE email = ?";
                $stmt = $db->prepare($sql);
                $stmt->execute(array($email));
                $ticketRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if ($ticketRows) {
                    foreach ($ticketRows as $ticketRow) {         
                        echo '<div class="row-title">
                                        <div class="row-manage-title">
                                            <a href="/ticket_detail.php?ticket=' . $ticketRow['id'] . '">' . htmlspecialchars($ticketRow['title']) . '</a>
                                        </div>
                                        <div class="row-manage-status">
                                            <p>'.$ticketRow['status'].'</p> 
                                        </div>
                              </div>';
                    }
                }
                ?>
            </div>
            <div class="sidebox"></div>
            <!--contetn_body end-->
        </div>
    </body>
</html>

