<?php
if (!isset($_SESSION)) {
    exit('Missing required session');
}
?>
<html>
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" type="text/css" href="/../common/css/main.css">
    </head>
    <body>
        <div class="container">
            <div class="headbox">
                <div class="head-side-box"></div>
                <div class="head-main-box">
                    <div class="head-title">
                        <p><h1>Ticket</h1>
                        <p>user: <?php echo htmlspecialchars($_SESSION['userName']); ?> / <a href="/admin/logout.php">logout</a></p>
                    </div>
                    <HR width="100%">
                </div>
                <div class="head-side-box"></div>
            </div> 
