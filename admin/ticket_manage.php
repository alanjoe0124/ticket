<?php
session_start();
if (!isset($_SESSION['uid'])) {
    header('Location:/admin/login.php');
}
?>
<html>
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" type="text/css" href="/../common/css/main.css">
        <script   src="https://code.jquery.com/jquery-3.2.1.min.js"></script>

    </head>
    <body>
        <div class="container">
            <!--content_head start-->
            <div class="headbox">
                <div class="head-side-box"></div>
                <div class="head-main-box">
                    <div class="head-title">
                        <p><h1>Ticket</h1>
                        <p>user: <?php echo $_SESSION['userName']; ?>&nbsp;/&nbsp;<a href="/admin/logout.php">logout</a></p>
                    </div>
                    <HR width="100%">
                </div>
                <div class="head-side-box"></div>
            </div> 
            <!--content_head end->
            
            <!--contetn_body start-->
            <div class="sidebox"> </div>
            <div class="mainbox">
                <?php
                require_once __DIR__ . '/../db.php';
                $sql = "SELECT ticket.id AS id, title, status.name AS status, email, http_referer, time FROM ticket JOIN status ON ticket.status = status.id WHERE status = 1 ORDER BY time ASC";
                $stmt = $db->prepare($sql);
                $stmt->execute();
                $ticketRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if ($ticketRows) {
                    echo '<table><tr><th>title</th><th>customer</th><th>status</th><th>time</th><th>httpreferer</th></tr>';
                    foreach ($ticketRows as $ticketRow) {
                        if (mb_strlen($ticketRow['title'], 'UTF-8') > 20) {
                            $ticketRow['title'] = substr($ticketRow['title'], 0, 20) . "...";
                        }
                        echo '<tr>
                                  <td><a href = "/ticket_detail.php?ticket=' . $ticketRow['id'] . '">' . htmlspecialchars($ticketRow['title']) . '</a></td>
                                  <td>' . htmlspecialchars($ticketRow['email']) . '</td>
                                  <td>' . $ticketRow['status'] . '</td>
                                  <td>' . $ticketRow['time'] . '</td>
                                  <td>' . $ticketRow['http_referer'] . '</td>
                              </tr>';
                    }
                    echo "</table>";
                }
                ?>
            </div>
            <div class="sidebox"></div>
            <!--contetn_body end-->
        </div>
    </body>
</html>