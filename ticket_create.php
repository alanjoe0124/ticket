<?php

header('Access-Control-Allow-Origin:http://ourblog.dev');
header('Access-Control-Allow-Credentials:true');
try {
    $paramArr = array('title', 'description', 'email');
    foreach ($paramArr as $param) {
        if (!isset($_POST[$param])) {
            throw new InvalidArgumentException("Required $param is missing");
        }
    }
    if (!isset($_SERVER['HTTP_REFERER'])) {
        throw new InvalidArgumentException("Required REFERER is missing");
    }
    if (strlen($_SERVER['HTTP_REFERER']) > 200) {
        throw new InvalidArgumentException("REFERFER ");
    }
    $httpReferer = filter_var($_SERVER['HTTP_REFERER'], FILTER_VALIDATE_URL);
    if (!$httpReferer) {
        throw new InvalidArgumentException("Http referer format wrong!");
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
        throw new InvalidArgumentException("Email format wrong!");
    }
} catch (Exception $e) {
    exit("Param error!");
}

require_once __DIR__ . '/db.php';
$sql = "INSERT INTO ticket(title, description, email, http_referer, time) VALUES(?, ?, ?, ?, ?)";
$stmt = $db->prepare($sql);
$stmt->execute(array(
    $_POST['title'],
    $_POST['description'],
    $email,
    $httpReferer,
    date('Y-m-d H:i:s')
));



