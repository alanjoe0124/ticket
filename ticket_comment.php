<?php
    $userIsCustomer        = false;
    $userIsCustomerService = false;

    session_start();
    if (isset($_SESSION['customerId'])) {
        $userIsCustomer = true;
    } elseif (isset($_SESSION['customerServiceId'])) {
        $userIsCustomerService = true;
    } else {
        exit;
    }

    include __DIR__ . '/lib/OurTicket/Util.php';
    $ticketId = OurTicket_Util::DBAIPK(OurTicket_Util::getQuery('id'));
    if (!$ticketId) {
        if ($userIsCustomer) {
            header('Location: /index.php?email=' . urlencode($_SESSION['customerEmail']));
        } else {
            header('Location: /admin/ticket_manage.php');
        }
        exit;
    }

    include __DIR__ . '/lib/OurTicket/Db.php';
    $db = OurTicket_Db::getDb();
    if ($userIsCustomer) {
        $sql = "SELECT id FROM ticket WHERE id = $ticketId AND customer_id = " . $_SESSION['customerId'];
        if (!$db->query($sql)->fetchColumn()) {
            header('Location: /index.php?email=' . urlencode($_SESSION['customerEmail']));
            exit;
        }
    }

    if ($_POST) {
        include __DIR__ . '/lib/OurTicket/Ticket.php';
        try {
            OurTicket_Util::killCSRF();
            if ($userIsCustomer) {
                OurTicket_Ticket::customerAddComment(
                    $ticketId,
                    OurTicket_Util::getPost('comment'),
                    $_SESSION['customerId']
                );
            } else {
                OurTicket_Ticket::customerServiceAddComment(
                    $ticketId,
                    OurTicket_Util::getPost('comment'),
                    $_SESSION['customerServiceId']
                );
            }
        } catch (InvalidArgumentException $e) {
            exit('invalid params');
        } catch (Exception $e) {
            exit('Server error');
        }
    }
?>

<?php
    if ($userIsCustomer) {
        include __DIR__ . '/header.php'; 
    } else {
        include __DIR__ . '/admin/header.php';
    }
?>

<?php
    $sql = "SELECT  ticket.*,
                    customer.email AS customer,
                    status.name AS status
            FROM    ticket
                    INNER JOIN customer ON ticket.customer_id = customer.id
                    INNER JOIN status ON ticket.status_id = status.id
            WHERE
                    ticket.id = $ticketId";
    $ticketRow = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
    
    if ($userIsCustomer) {
        include __DIR__ . '/ticket_info.php';
    } else {
        include __DIR__ . '/admin/ticket_info.php';
    }
?>

<h3>Comments:</h3>
<?php
    $sql = "SELECT  content,
                    user_id,
                    user_type,
                    time
            FROM    comment
            WHERE
                    ticket_id = $ticketId 
                    ORDER BY id ASC";
    $commentRows = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

    $customerServiceIds = array();
    foreach ($commentRows as $row) {
        if ($row['user_type'] == 2) { // 1-customer, 2-customer service
            $customerServiceIds[] = $row['user_id'];
        }
    }
    $customerServiceNames = array();
    if ($customerServiceIds) {
        $rows = $db->query('SELECT id, name FROM user WHERE id IN (' . implode(',', $customerServiceIds) . ')')->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $row) {
            $customerServiceNames[$row['id']] = htmlspecialchars($row['name']);
        }
    }

    foreach ($commentRows as $row) {
        echo '<div class="row">';
        if ($row['user_type'] == 1) {
            if ($userIsCustomer) {
                echo '我';
            } else {
                echo htmlspecialchars($ticketRow['customer']);
            }
        } else {
            echo $customerServiceNames[$row['user_id']];
        }
        echo '<pre>', htmlspecialchars($row['content']), '</pre>';
        echo '<small>', $row['time'], '</small>';
        echo '</div>';
    }
?>

<h3>Add Comment:</h3>
<form method="POST">
    <textarea name="comment" rows="10" cols="80" placeholder="comment here..."></textarea>
    <br>
    <button type="submit">submit</button>
</form>

<?php
    if ($userIsCustomer) {
        include __DIR__ . '/footer.php'; 
    } else {
        include __DIR__ . '/admin/footer.php';
    }
?>
