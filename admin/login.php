<?php
    session_start();
    if (isset($_SESSION['customerServiceId'])) {
        header('Location: /admin/ticket_manage.php');
        exit;
    }

    $nameOrPwdWrong = false;

    if ($_POST) {
        include __DIR__ . '/../lib/OurTicket/Util.php';
        include __DIR__ . '/../lib/OurTicket/Db.php';
        include __DIR__ . '/../lib/OurTicket/Login.php';

        try {
            OurTicket_Util::killCSRF();
            $userRow = OurTicket_Login::doLogin($_POST);
        } catch (InvalidArgumentException $e) {
            exit('invalid params');
        }

        if ($userRow) {
            session_regenerate_id();
            $_SESSION['customerServiceId']   = $userRow['id'];
            $_SESSION['customerServiceName'] = $userRow['name'];
            header('Location: /admin/ticket_manage.php');
            exit;
        }

        $nameOrPwdWrong = true;
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
            <h1>Customer Service Login</h1>
        </div>

        <form method="post" class="login-form">
            <?php
                if ($nameOrPwdWrong) {
                    echo '<p style="color:red;padding-left:50px;">用户名或密码不对</p>';
                }
            ?>
            <div class="row">
                user name: <input type="text" name="name">
            </div>
            <div class="row">
                password: <input type="password" name="pwd">
            </div>
            <button type="submit">submit</button>
        </form>
    </div>
</body>
</html>
