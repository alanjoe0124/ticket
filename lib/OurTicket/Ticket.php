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
        $data['email'] = OurTicket_Util::validateEmail($data['email']);
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

    protected static function comment($ticketId, $comment, $userId, $userType)
    {
        $len = mb_strlen($comment, 'UTF-8');
        if ($len == 0 || $len > 3000) {
            throw new InvalidArgumentException();
        }

        $sql  = 'INSERT INTO comment (ticket_id, content, user_id, user_type) VALUES (?, ?, ?, ?)';
        $stmt = OurTicket_Db::getDb()->prepare($sql);
        $stmt->execute(array($ticketId, $comment, $userId, $userType));
    }

    // 调用此方法前要保证ticketId是数字，并且是customerId的ticket
    public static function customerAddComment($ticketId, $comment, $customerId)
    {
        self::comment($ticketId, $comment, $customerId, 1);
    }

    // 调用此方法前要保证ticketId是数字
    public static function customerServiceAddComment($ticketId, $comment, $customerServiceId)
    {
        $sql = "SELECT id FROM ticket WHERE id = $ticketId";
        if (!OurTicket_Db::getDb()->query($sql)->fetchColumn()) {
            throw new InvalidArgumentException('no such ticketId');
        }

        self::comment($ticketId, $comment, $customerServiceId, 2);
    }
}
