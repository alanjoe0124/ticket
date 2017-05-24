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
        echo '待解决';
    } else {
        echo '已解决';
    }
?>
<pre><?php echo htmlspecialchars($ticketRow['description']); ?></pre>
<div>
    <small>Customer: <?php echo htmlspecialchars($ticketRow['customer']); ?> /</small>
    <small>Domain: <?php echo htmlspecialchars($ticketRow['domain']); ?> /</small>
    <small><?php echo $ticketRow['time']; ?></small>
</div>
