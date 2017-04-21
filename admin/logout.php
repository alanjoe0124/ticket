<?php
session_start();
if(!isset($_SESSION['uid'])){
    header('Location:/admin/login.php');
    exit;
}
session_destroy();
header('Location:/admin/login.php');
