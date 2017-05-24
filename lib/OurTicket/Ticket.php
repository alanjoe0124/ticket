<?php

class OurTicket_Ticket
{
    public function create(array $data) 
    {
        $requiredKeys = array('title', 'description', 'email', 'domain');
        foreach ($requiredKeys as $key) {
            if (!isset($data[$key])) {
                throw new InvalidArgumentException("missing required key $key");
            }
        }

        $len = mb_strlen($data['title'], 'UTF-8');
        if ($len == 0 || $len > 500) {
            throw new InvalidArgumentException('title required and maxlength is 500');
        }
        if (strlen($data['description']) > 64000) {
            throw new InvalidArgumentException('description too long, maxlength is 64000');
        }
        $len = strlen($data['email']);
        if ($len < 4 || $len > 100) {
            throw new InvalidArgumentException('email minlength 4, maxlength 100');
        }
        $data['email'] = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
        if (!$data['email']) {
            throw new InvalidArgumentException('invalid email');
        }
        if ($data['domain'] != 'ourblog.dev') {
            throw new InvalidArgumentException('INVALID_DOMAIN');
        }

        $db   = OurTicket_Db::getDb();
        $stmt = $db->prepare('SELECT id FROM customer WHERE email = ?');
        $stmt->execute(array($data['email']));
        $customerId = $stmt->fetchColumn();

        $db->beginTransaction();
        try {
            if (!$customerId) {
                $stmt = $db->prepare('INSERT INTO customer(email) VALUES(?)');
                $stmt->execute(array($data['email']));
                $customerId = $db->lastInsertId();
            }
            $stmt = $db->prepare('INSERT INTO ticket(title, description, customer_id, domain) VALUES(?, ?, ?, ?)');
            $stmt->execute(array(
                $data['title'],
                $data['description'],
                $customerId,
                $data['domain']
            ));
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public function close($userEmail, $ticketId) {

        $db = Db::getDb();
        $sql = "SELECT ticket.status
                FROM ticket 
                    INNER JOIN customer ON ticket.user = customer.id
                WHERE 
                    customer.name = ? AND ticket.id = $ticketId";
        $stmt = $db->prepare($sql);
        $stmt->execute(array($userEmail));
        $status = $stmt->fetchColumn();

        if (!$status) {
            throw new InvalidArgumentException('Customer Email and ticket id not related');
        }

        if ($status != 2) { // ticket status ( 1 => pending, 2 => close ) 
            $sql = $db->exec("UPDATE ticket SET status = 2 WHERE id = $ticketId");
        }
    }

    public function commentPost($ticketId, $comment, $userId, $userType) {

        if (!isset($userId)) {
            throw new InvalidArgumentException('Missing required userId');
        }
        if (!isset($ticketId)) {
            throw new InvalidArgumentException('Missing required ticketId');
        }
        if (!isset($comment)) {
            throw new InvalidArgumentException('Missing required comment');
        }
        $commentLength = strlen($comment);
        if ($commentLength > 64000 || $commentLength == 0) {
            throw new InvalidArgumentException('Comment max length 64000 and not empty');
        }
        $ticketId = filter_var($ticketId, FILTER_VALIDATE_INT, array(
            'options' => array('min_range' => 1)
        ));
        if (!$ticketId) {
            throw new InvalidArgumentException('Invalid ticket id');
        }

        $db = Db::getDb();
        $sql = 'INSERT INTO comment (content, user, ticket_id, user_type) VALUES (?, ?, ?, ?)';
        $stmt = $db->prepare($sql);
        $stmt->execute(array($comment, $userId, $ticketId, $userType));
        // user_type ( 1 = > table(`customer`) , 2 => table (`user`)
        return $ticketId;
    }

}
