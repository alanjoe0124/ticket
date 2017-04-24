<?php
session_start();
if (!isset($_SESSION['uid'])) {
    header('Location:/admin/login.php');
    exit;
}
?>
<html>
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" type="text/css" href="/../common/css/main.css">
    </head>
    <body>
        <div class="container">
            <!--content_head start-->
            <div class="headbox">
                <div class="head-side-box"></div>
                <div class="head-main-box">
                    <div class="head-title">
                        <p><h1>Ticket</h1>
                        <p>user: <?php echo htmlspecialchars($_SESSION['userName']); ?>&nbsp;/&nbsp;<a href="/admin/logout.php">logout</a></p>
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
                $sql = "SELECT  ticket.id AS id,
                                ticket.title, 
                                status.name AS status, 
                                ticket.user as customer, 
                                ticket.domain, 
                                ticket.time 
                        FROM    ticket 
                                INNER JOIN status ON ticket.status = status.id 
                        WHERE 
                                status = 1    
                                ORDER BY time ASC";  // ticket status ( 1 => pending, 2 => close ) 
                $stmt = $db->query($sql);
                $ticketRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if ($ticketRows) :
                    ?>
                    <table>
                        <tr>
                            <th>title</th>
                            <th>customer</th>
                            <th>status</th>
                            <th>time</th>
                            <th>httpreferer</th>
                        </tr>
                        <?php
                        foreach ($ticketRows as $row):
                            ?>
                            <tr>
                                <td><a href = "/admin/ticket_answer.php?ticket=<?php echo $row['id']; ?> ">
                                        <?php echo htmlspecialchars($row['title']); ?></a></td>
                                <td><?php echo htmlspecialchars($row['customer']); ?></td>
                                <td><?php echo $row['status']; ?></td>
                                <td><?php echo $row['time']; ?></td>
                                <td><?php echo htmlspecialchars($row['domain']); ?></td>
                            </tr>
                            <?php
                        endforeach;
                        ?>
                    </table>
                    <?php
                endif;
                ?>
            </div>
            <div class="sidebox"></div>
            <!--contetn_body end-->
        </div>
    </body>
</html>