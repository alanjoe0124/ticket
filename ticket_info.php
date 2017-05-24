<?php
    if (!isset($ticketRow)) {
        exit;
    }
?>

<?php
    echo '<h4 style="margin: 0 20px 5px 0;display:inline-block;">', 
            htmlspecialchars($ticketRow['title']), 
         '</h4>';
    if ($ticketRow['status_id'] == 1) { // 1-pending, 2-close
        echo '<a href="/ticket_close.php?id=', $ticketRow['id'], '">Close</a>';
    } else {
        echo '<span>已解决</span>';
    }
?>
<pre><?php echo htmlspecialchars($ticketRow['description']); ?></pre>
<small><?php echo $ticketRow['time']; ?></small>
