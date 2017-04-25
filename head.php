<?php
if (!isset($_SESSION)) {
    exit('Missing required session');
}
?>
<div class="headbox">
    <div class="head-side-box"></div>
    <div class="head-main-box">
        <div class="head-title">
            <p><h1>Ticket</h1>
            <p>customer: <?php echo htmlspecialchars($_SESSION['customerEmail']); ?></p>
        </div>
        <HR width="100%">
    </div>
    <div class="head-side-box"></div>
</div> 