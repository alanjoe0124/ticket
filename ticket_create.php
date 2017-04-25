<?php
require_once __DIR__.'/prevent_csrf.php';
header('Access-Control-Allow-Origin:http://ourblog.dev');

try {
    $paramArr = array('title', 'description', 'email', 'domain');
    foreach ($paramArr as $param) {
        if (!isset($_POST[$param])) {
            throw new InvalidArgumentException("Required $param is missing");
        }
    }
    $titleLength = mb_strlen($_POST['title'], "utf-8");
    if ($titleLength > 500 || $titleLength < 1) {
        throw new InvalidArgumentException("Title max length 500, min length 1");
    }
    if (strlen($_POST['description']) > 64000) {
        throw new InvalidArgumentException("Max description is 64000");
    }
    $emailLength = strlen($_POST['email']);
    if ($emailLength > 100 || $emailLength < 4) {
        throw new InvalidArgumentException("Email min length 4, max length 100");
    }
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    if (!$email) {
        throw new InvalidArgumentException("Email invalid");
    } 
    if($_POST['domain'] != "ourblog.dev"){
        throw new InvalidArgumentException("Domain invalid");
    }
} catch (Exception $e) {
    exit("Param error!");
}
require_once __DIR__ . '/db.php';
$sql = "SELECT id FROM customer WHERE name = ?";
$stmt = $db->prepare($sql);
$stmt->execute(array($email));
$userId = $stmt->fetchColumn();
if(!$userId){
    $sql = "INSERT INTO customer( name ) VALUES( ? )";
    $stmt = $db->prepare($sql);
    $stmt->execute(array($email));
    $userId = $db->lastInsertId();
}
$sql = "INSERT INTO ticket(title, description, user, domain) VALUES(?, ?, ?, ?)";
$stmt = $db->prepare($sql);
$stmt->execute(array(
    $_POST['title'],
    $_POST['description'],
    $userId,
    $_POST['domain']
));