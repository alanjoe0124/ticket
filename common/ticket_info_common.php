<?php
if(!isset($_SESSION)){
    exit("Permission denied");
}
                $sql = "SELECT user.name as user,
                               comment.content,
                               comment.time
                        FROM comment
                                INNER JOIN user ON user.id = comment.user
                        WHERE ticket_id = ? ORDER BY time ASC";
                $stmt = $db->prepare($sql);
                $stmt->execute(array($ticketId));
                $commentRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if ($commentRows) {
                    echo '<HR width="100%"> <div>comments:</div>';
                    foreach ($commentRows as $commentRow) {
                        echo '<HR width="100%">
                            <div>
                                <table>
                                    <tr>
                                        <th>user</th>
                                        <th>comment</th>
                                        <th>time</th>
                                    <tr>
                                        <td>' . htmlspecialchars($commentRow['user']) . '</td>
                                        <td>' . htmlspecialchars($commentRow['content']) . '</td>
                                        <td>' . $commentRow['time'] . '</td>
                                    </tr>
                                </table>
                            </div>';
                    }
                }
                ?>
                <HR width="100%">
                <form method="POST">
                    <div id="content" class="row-text">
                        text:<textarea name = "comment" rows = "10"  placeholder = "text..."></textarea>
                        <input type = "hidden" name = "ticketId" value = "<?php echo $ticketId; ?>"/> 
                        <button type = "submit">submit</button>
                    </div>
                </form>
            </div>
            <div class="sidebox"></div>
            <!--contetn_body end-->
        </div>
    </body>
</html>

