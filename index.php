<?php
    include __DIR__ . '/lib/OurTicket/Db.php';
    include __DIR__ . '/lib/OurTicket/Util.php';

    try {
        $email = OurTicket_Util::validateEmail(OurTicket_Util::getQuery('email'));
    } catch (InvalidArgumentException $e) {
        exit('invalid email');
    }

    session_start();
    $db = OurTicket_Db::getDb();
    if (!isset($_SESSION['customerId']) || $_SESSION['customerEmail'] != $email) {
        $stmt = $db->prepare('SELECT id FROM customer WHERE email = ?');
        $stmt->execute(array($email));
        $customerId = $stmt->fetchColumn();
        if (!$customerId) {
            $stmt = $db->prepare('INSERT INTO customer(email) VALUES(?)');
            $stmt->execute(array($email));
            $customerId = $stmt->lastInsertId();
        }
        session_regenerate_id();
        $_SESSION['customerId']    = $customerId;
        $_SESSION['customerEmail'] = $email;
    }
?>

<?php include __DIR__ . '/header.php'; ?>

<div class="mainbox">
    <?php
        $sql = 'SELECT  ticket.id,
                        ticket.title, 
                        status.name as status
                FROM    ticket 
                        INNER JOIN status ON ticket.status_id = status.id
                        WHERE ticket.customer_id = ? 
                        ORDER BY ticket.time DESC, ticket.id DESC';
        $stmt = $db->prepare($sql);
        $stmt->execute(array($_SESSION['customerId']));
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $row) {
    ?>
        <div class="row">
            <a href="/ticket_comment.php?id=<?php echo $row['id']; ?>">
                <?php echo htmlspecialchars($row['title']); ?>
            </a>
            <span class="status"><?php echo $row['status']; ?></span>
        </div>
    <?php } ?>
</div>

<?php include __DIR__ . '/footer.php'; ?>
