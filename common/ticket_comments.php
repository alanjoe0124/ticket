<?php
if (!isset($ticketId)) {
    exit('Missing required ticket');
}
// user_type ( 1 = > table(`customer`) , 2 => table (`user`)

$sql = "SELECT comment.content,
               comment.time,
               user,
               user_type
            FROM comment 
            WHERE comment.ticket_id = $ticketId ORDER BY time DESC, id DESC";
$stmt = Db::getDb()->query($sql);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?> 
<HR width="100%">
<div>comments:</div> 
<?php foreach ($comments as $row): ?>
    <HR width="100%">
    <div>
        <table>
            <tr>
                <th>user</th>
                <th>comment</th>
                <th>time</th>
            </tr>
            <tr>
                <td>
                    <?php
                    if ($row['user_type'] == 1) {
                        $table = 'customer';
                    } else {                      // user_type = 2
                        $table = 'user';
                    }
                    $sql = 'SELECT name FROM ' . $table . ' WHERE id = ' . $row['user'];
                    $stmt = Db::getDb()->query($sql);
                    $name = $stmt->fetchColumn();
                    echo htmlspecialchars($name);
                    ?>
                </td>
                <td><?php echo htmlspecialchars($row['content']); ?></td>
                <td><?php echo $row['time']; ?></td>
            </tr>
        </table>
    </div>
<?php endforeach; ?>
    






