<?php
session_start();
if (!isset($_SESSION['uid'])) {
    header('Location:/admin/login.php');
    exit;
}
?>
<!--content_head start-->
<?php include __DIR__ . '/admin_head.php'; ?>
<!--content_head end->

<!--contetn_body start-->
<div class="sidebox"> </div>
<div class="mainbox">
    <?php
    require_once __DIR__ . '/../db.php';
    $sql = 'SELECT  status.name AS status,
                                ticket.id AS id,
                                ticket.title,
                                ticket.user as customer, 
                                ticket.domain, 
                                ticket.time,
                                customer.name as customer
                        FROM    ticket 
                                INNER JOIN status ON status.id = ticket.status
                                INNER JOIN customer ON ticket.user = customer.id
                        WHERE 
                                status = 1    
                                ORDER BY time DESC, ticket.id DESC';  // ticket status ( 1 => pending, 2 => close ) 
    $stmt = $db->query($sql);
    $ticketRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?> 
    <table>
        <tr>
            <th>title</th>
            <th>customer</th>
            <th>status</th>
            <th>time</th>
            <th>domain</th>
        </tr>
        <?php foreach ($ticketRows as $row): ?>
            <tr>
                <td><a href = "/admin/ticket_answer.php?ticket=<?php echo $row['id']; ?> ">
                        <?php echo htmlspecialchars($row['title']); ?></a></td>
                <td><?php echo htmlspecialchars($row['customer']); ?></td>
                <td><?php echo $row['status']; ?></td>
                <td><?php echo $row['time']; ?></td>
                <td><?php echo htmlspecialchars($row['domain']); ?></td>
            </tr>
        <?php endforeach; ?>
    </table> 
</div> 
<!--contetn_body end-->
<?php include __DIR__ . '/admin_footer.php'; ?>