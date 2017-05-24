<?php
    session_start();
    if (!isset($_SESSION['customerServiceId'])) {
        header('Location: /admin/login.php');
        exit;
    }
?>

<?php include __DIR__ . '/header.php'; ?>

<?php
    include __DIR__ . '/../lib/OurTicket/Db.php';
    $sql = 'SELECT  ticket.id AS id,
                    ticket.title,
                    ticket.domain,
                    ticket.time,
                    status.name AS status,
                    customer.email as customer
            FROM    ticket
                    INNER JOIN status ON ticket.status_id = status.id
                    INNER JOIN customer ON ticket.customer_id = customer.id
                    ORDER BY ticket.time DESC, ticket.id DESC'; // 1-pending 2-close
    $rows = OurTicket_Db::getDb()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>
<table width="100%">
    <tr>
        <th>title</th>
        <th>domain</th>
        <th>customer</th>
        <th>time</th>
        <th>status</th>
    </tr>
    <?php foreach ($rows as $row): ?>
        <tr>
            <td>
                <a href="/ticket_comment.php?id=<?php echo $row['id']; ?>">
                    <?php echo htmlspecialchars($row['title']); ?>
                </a>
            </td>
            <td><?php echo htmlspecialchars($row['domain']); ?></td>
            <td><?php echo htmlspecialchars($row['customer']); ?></td>
            <td><?php echo $row['time']; ?></td>
            <td><?php echo $row['status']; ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<?php include __DIR__ . '/footer.php'; ?>
