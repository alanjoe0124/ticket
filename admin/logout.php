<?php

session_start();
if (!isset($_SESSION['customerServiceId'])) {
    header('Location: /admin/login.php');
    exit;
}

$_SESSION = array();
header('Location: /admin/login.php');
