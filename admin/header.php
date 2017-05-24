<?php
    if (!isset($_SESSION['customerServiceId'])) {
        exit('need customerServiceId');
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
            <h1>Ticket</h1>
            <p>
                user: <?php echo htmlspecialchars($_SESSION['customerServiceName']); ?>
                /
                <a href="/admin/logout.php">logout</a>
            </p>
        </div>
