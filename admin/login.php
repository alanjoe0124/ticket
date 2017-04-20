<?php
session_start();
if (isset($_SESSION['uid'])) {
    header("Location:/admin/ticket_manage.php");
    exit;
}
if ($_POST) {
    try {
        require_once __DIR__. '/../prevent_csrf.php';
        if (!isset($_POST['userName'])) {
            throw new InvalidArgumentException("User name invalid");
        }
        if (!isset($_POST['pwd'])) {
            throw new InvalidArgumentException("Password invalid");
        }
        if (mb_strlen($_POST['userName'],'UTF-8') > 50) {
            throw new InvalidArgumentException("User name max length 50");
        }
        if (strlen($_POST['pwd']) > 40) {
            throw new InvalidArgumentException("Password max length 40");
        }
    } catch (Exception $e) {
        exit("Param error!");
    }
    require_once __DIR__ . '/../db.php';
    $sql = "SELECT * FROM user WHERE name = ? and pwd = ?";
    $stmt = $db->prepare($sql);
    $salt = 'acd806b0-d563-4824-907f-852f8f1003a5'; 
    $stmt->execute(array($_POST['userName'], md5($_POST['pwd'].$salt)));
    $userRow = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($userRow) {
        session_regenerate_id();
        $_SESSION['uid'] = $userRow['id'];
        $_SESSION['userName'] = $userRow['name'];
        header("Location:/admin/ticket_manage.php");
        exit();
    } else {
        header("Location:/admin/login.php?error=password_failed");
        exit();
    }
}
?>
<html>
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" type="text/css" href="/common/css/main.css">
    </head>
    <body>
        <div class="container">
            <!--content_head start-->
            <div class="headbox">
                <div class="head-side-box"></div>

                <div class="head-main-box">
                    <p><h1><a href="/index.php">ticket</a>/Login</h1>
                    <HR width="100%">
                </div>
                <div class="head-side-box"></div>
            </div>
            <!--content_head end->
            
            <!--contetn_body start-->
            <div class="sidebox"></div>
            <div class="mainbox">
                <?php
                if (isset($_GET['error'])) {
                    if ($_GET['error'] == 'password_failed') {
                        echo '<p style="color:red">密码不对</p>';
                    }
                }
                ?>
                <form  method="post"> 
                    <div class="row-title">
                        user name:<input type="text"  id="userName" name="userName"  value=""> 
                    </div>
                    <div class="row-title">
                        password:<input type="password"  id="pwd" name="pwd"  value="">
                    </div>
                    <div class="row-title">
                        <button type="submit">submit</button>
                    </div>   
                </form>
            </div>
            <div class="sidebox"></div>
            <!--contetn_body end-->
        </div>
    </body>
</html>

