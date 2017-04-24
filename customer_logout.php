<?php
session_start();
if(!isset($_SESSION['customerEmail'])){
    header('Location:/admin/login.php');
    exit;
}
session_destroy();
header('Location:/admin/login.php');
