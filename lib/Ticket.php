<?php

class Ticket {

    public function create(array $data) {
        $paramArr = array('title', 'description', 'email', 'domain');
        foreach ($paramArr as $param) {
            if (!isset($data[$param])) {
                throw new InvalidArgumentException("Required $param is missing");
            }
        }

        $titleLength = mb_strlen($data['title'], "UTF-8");
        if ($titleLength > 500 || $titleLength < 1) {
            throw new InvalidArgumentException('Title max length 500, min length 1');
        }
        if (strlen($data['description']) > 64000) {
            throw new InvalidArgumentException('Max description is 64000');
        }
        $emailLength = strlen($data['email']);
        if ($emailLength > 100 || $emailLength < 4) {
            throw new InvalidArgumentException('Email min length 4, max length 100');
        }
        $data['email'] = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
        if (!$data['email']) {
            throw new InvalidArgumentException('Email invalid');
        }
        if ($data['domain'] != 'ourblog.dev') {
            throw new InvalidArgumentException('Domain invalid');
        }

        $db = Db::getDb();

        $stmt = $db->prepare('SELECT id FROM customer WHERE name = ?');
        $stmt->execute(array($data['email']));
        $customerId = $stmt->fetchColumn();

        $db->beginTransaction();
        try {
            if (!$customerId) {
                $stmt = $db->prepare('INSERT INTO customer( name ) VALUES(?)');
                $stmt->execute(array($data['email']));
                $customerId = $db->lastInsertId();
            }
            $stmt = $db->prepare('INSERT INTO ticket(title, description, user, domain) VALUES(?, ?, ?, ?)');
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

 

}
