<?php
try {
    if (!isset($_GET['email'])) {
        throw new InvalidArgumentException('Missing required email');
    }
    $emailLength = strlen($_GET['email']);
    if ($emailLength > 100 || $emailLength < 4) {
        throw new InvalidArgumentException("Email min length 4, max length 100");
    }
    $email = filter_var($_GET['email'], FILTER_VALIDATE_EMAIL);
    if (!$email) {
        throw new InvalidArgumentException("Email invalid");
    }
} catch (Exception $e) {
    exit("Param error");
}
require_once __DIR__ . '/db.php';
session_start();
if (isset($_SESSION['uid'])) {
    exit("Permission denied");
}
$stmt = $db->prepare("SELECT id FROM customer WHERE name = ?");
$stmt->execute(array($email));
$userId = $stmt->fetchColumn();
if (!$userId) {
    exit("You have no ticket");
}
if (!isset($_SESSION['customerEmail'])) {
    session_regenerate_id();
    $_SESSION['customerEmail'] = $email;
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
                        <p><h1>Ticket</h1>
                        <p> <?php echo htmlspecialchars($_SESSION['customerEmail']); ?>/<a href="/customer_logout.php">logout</a></p>
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
                $sql = "SELECT ticket.id,
                               ticket.title, 
                               status.name as status 
                        FROM   ticket 
                               INNER JOIN  status ON ticket.status = status.id 
                        WHERE user = $userId";
                $stmt = $db->query($sql);
                $ticketRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if ($ticketRows):
                    foreach ($ticketRows as $row):
                ?>
                        <div class="row-title">
                            <div class="row-manage-title">
                                <a href="/ticket_ask.php?ticket=<?php echo $row['id']; ?>"> <?php echo htmlspecialchars($row['title']); ?> </a>
                            </div>
                            <div class="row-manage-status">
                                <?php echo $row['status']; ?>
                            </div>
                        </div>
                <?php
                    endforeach;
                endif;
                ?>
            </div>
            <div class="sidebox"></div>
            <!--contetn_body end-->
        </div>
    </body>
</html>

