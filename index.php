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
$stmt = $db->prepare("SELECT id FROM customer WHERE name = ?");
$stmt->execute(array($email));
$userId = $stmt->fetchColumn();
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
            <?php include __DIR__ . '/head.php'; ?>
            <!--content_head end->

            <!--contetn_body start-->
            <div class="sidebox"> </div>
            <div class="mainbox">
                <?php
                $ticketRows = array();
                if ($userId) {
                    $sql = "SELECT ticket.id,
                               ticket.title, 
                               status.name as status 
                        FROM   ticket 
                               INNER JOIN  status ON ticket.status = status.id 
                        WHERE user = $userId";
                    $stmt = $db->query($sql);
                    $ticketRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                }
                ?>
                <?php foreach ($ticketRows as $row): ?>
                            <div class="row-title">
                                <div class="row-manage-title">
                                    <a href="/ticket_ask.php?ticket=<?php echo $row['id']; ?>"> <?php echo htmlspecialchars($row['title']); ?> </a>
                                </div>
                                <div class="row-manage-status">
                                    <?php echo $row['status']; ?>
                                </div>
                            </div>
                <?php endforeach; ?>
                <?php
                if (!$userId) {
                    echo 'You have no ticket';
                }
                ?>
            </div>
            <!--contetn_body end-->
            <?php
            include __DIR__ . '/footer.php';
            ?>