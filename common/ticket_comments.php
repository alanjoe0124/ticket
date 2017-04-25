<?php
if (!isset($ticketId)) {
    exit("Permission denied");
}

// user_type ( 1 = > table(`customer`) , 2 => table (`user`)
$sql = "SELECT comment.content,
               comment.time,
               user.name as user
            FROM comment
                    INNER JOIN user ON user.id = comment.user
            WHERE comment.ticket_id = $ticketId AND comment.user_type = 2 ORDER BY time ASC";
$stmt = $db->query($sql);
$csUserCommentRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
$comments = array();
foreach ($csUserCommentRows as $row) {
    $comments[$row['time']] = array('content' => $row['content'], 'user' => $row['user']);
}
$sql = "SELECT comment.content,
               comment.time,
               customer.name as user
            FROM comment
                    INNER JOIN customer ON  comment.user = customer.id
            WHERE comment.ticket_id = $ticketId AND comment.user_type = 1 ORDER BY time ASC";
$stmt = $db->query($sql);
$customerCommentRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($customerCommentRows as $row) {
    $comments[$row['time']] = array('content' => $row['content'], 'user' => $row['user']);
}
ksort($comments);
?>

<HR width="100%">
<div>comments:</div>
<?php
if (!empty($comments)):
    foreach ($comments as $time => $row):
        ?>
        <HR width="100%">
        <div>
            <table>
                <tr>
                    <th>user</th>
                    <th>comment</th>
                    <th>time</th>
                </tr>
                <tr>
                    <td><?php echo htmlspecialchars($row['user']); ?></td>
                    <td><?php echo htmlspecialchars($row['content']); ?></td>
                    <td><?php echo $time; ?></td>
                </tr>
            </table>
        </div>
        <?php
    endforeach;
endif;
?>




