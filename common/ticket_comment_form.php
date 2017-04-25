<?php
if (!isset($ticketId)) {
    exit("Permission denied");
}
?>
<HR width="100%">
<form method="POST">
    <div id="content" class="row-text">
        text:<textarea name = "comment" rows = "10"  placeholder = "text..."></textarea>
        <input type = "hidden" name = "ticketId" value = "<?php echo $ticketId; ?>"/> 
        <button type = "submit">submit</button>
    </div>
</form>
</div>

