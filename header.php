<?php
    if (!isset($_SESSION['customerEmail'])) {
        exit('this header need customerEmail');
    }
?>

<html>
<head>
<meta charset="utf-8">
<link rel="stylesheet" type="text/css" href="/common/css/main.css">
</head>
<body>
    <div class="container">
        <div class="headbox">
            <h1>Ticket</h1>
            <p>customer: <?php echo htmlspecialchars($_SESSION['customerEmail']); ?></p>
        </div>
