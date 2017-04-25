<?php
if (!isset($ticketId)) {
    exit("Missing required ticket");
}

$sql = "SELECT customer.name AS customer, 
                               ticket.id AS id,
                               ticket.title, 
                               ticket.description, 
                               ticket.time,
                               status.name AS status
                        FROM   customer INNER JOIN ticket ON customer.id = ticket.user
                                      INNER JOIN status ON ticket.status = status.id 
                        WHERE 
                               ticket.id = $ticketId";
$stmt = $db->query($sql);
$ticketRow = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<div>ticket info:
    <?php if (isset($_SESSION['customerEmail'])): ?>
        <?php if ($ticketRow['status'] == "pending"): ?>
            <a href="/close.php?ticket=<?php echo $ticketId; ?>"><button>close</button></a>
        <?php endif; ?>
    <?php endif; ?>
</div>
<HR width="100%">
<div>
    <table>
        <tr>
            <th>title</th>
            <th>description</th>
            <th>customer</th>
            <th>status</th>
            <th>time</th>
        </tr>
        <tr>
            <td><?php echo htmlspecialchars($ticketRow['title']); ?></td>
            <td><?php echo htmlspecialchars($ticketRow['description']); ?></td>
            <td><?php echo htmlspecialchars($ticketRow['customer']); ?></td>
            <td><?php echo $ticketRow['status']; ?></td>
            <td><?php echo $ticketRow['time']; ?></td>       
        </tr>
    </table>
</div>
