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

    public function close(array $data) {
        try {
            if (!isset($data['customerEmail'])) {
                throw new InvalidArgumentException('Missing required customerEmail');
            }
            if (!isset($data['ticket'])) {
                throw new InvalidArgumentException('Missing required ticket');
            }
            $ticketId = filter_var($data['ticket'], FILTER_VALIDATE_INT, array(
                'options' => array('min_range' => 1)
            ));
        } catch (Exception $e) {
            throw $e;
        }
        $db = Db::getDb();
        $sql = "SELECT ticket.status
                FROM ticket 
                    INNER JOIN customer ON ticket.user = customer.id
                WHERE 
                    customer.name = ? AND ticket.id = $ticketId";
        $stmt = $db->prepare($sql);
        $stmt->execute(array($data['customerEmail']));
        $status = $stmt->fetchColumn();
        if (!$status) {
            throw new InvalidArgumentException('Customer Email and ticket id not related');
        }
        if ($status != 2) { // ticket status ( 1 => pending, 2 => close ) 
            $sql = $db->exec("UPDATE ticket SET status = 2 WHERE id = $ticketId");
        }
    }

    public function view(array $data) {
        try {
            if (!isset($data['email'])) {
                throw new InvalidArgumentException('Missing required email');
            }
            $emailLength = strlen($data['email']);
            if ($emailLength > 100 || $emailLength < 4) {
                throw new InvalidArgumentException('Email min length 4, max length 100');
            }
            $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
            if (!$email) {
                throw new InvalidArgumentException('Email invalid');
            }
        } catch (Exception $e) {
            throw $e;
        }
        $db = Db::getDb();
        $stmt = $db->prepare('SELECT id FROM customer WHERE name = ?');
        $stmt->execute(array($email));
        $customerId = $stmt->fetchColumn();

        $sql = "SELECT ticket.id,
                               ticket.title, 
                               status.name as status 
                        FROM   ticket 
                               INNER JOIN  status ON ticket.status = status.id 
                        WHERE user = $customerId
                        ORDER BY ticket.time DESC, ticket.id DESC";
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function ask(array $data) {
        try {
            $formParam = array('comment', 'ticketId');
            foreach ($formParam as $key) {
                if (!isset($data[$key])) {
                    throw new InvalidArgumentException("Missing required $key");
                }
            }
            $commentLength = strlen($data['comment']);
            if ($commentLength > 64000 || $commentLength == 0) {
                throw new InvalidArgumentException('Comment max length 64000 and not empty');
            }
            $ticketId = filter_var($data['ticketId'], FILTER_VALIDATE_INT, array(
                'options' => array('min_range' => 1)
            ));
            if (!$ticketId) {
                throw new InvalidArgumentException('Invalid ticket id');
            }
        } catch (Exception $e) {
            throw $e;
        }
        $db = Db::getDb();
        $sql = 'SELECT id FROM customer WHERE name = ?';
        $stmt = $db->prepare($sql);
        $stmt->execute(array($data['customerEmail']));
        $userId = $stmt->fetchColumn();

        $sql = 'INSERT INTO comment (content, user, ticket_id) VALUES (?, ?, ?)';
        $stmt = $db->prepare($sql);
        $stmt->execute(array($data['comment'], $userId, $ticketId));
    }

    public function info(array $data) {
        try {
            if (!isset($data['customerEmail'])) {
                throw new InvalidArgumentException('Missing required customer email');
            }
            $ticketId = filter_var($data['ticket'], FILTER_VALIDATE_INT, array(
                'options' => array('min_range' => 1)
            ));
            if (!$ticketId) {
                throw new InvalidArgumentException('Invalid ticket id');
            }
        } catch (Exception $e) {
            throw $e;
        }
        $db = Db::getDb();
        $sql = "SELECT customer.name AS customer,
                        ticket.id AS id,
                        ticket.title,
                        ticket.description,
                        status.name AS status
                    FROM customer INNER JOIN ticket ON customer.id = ticket.user
                        INNER JOIN status ON ticket.status = status.id
                    WHERE
                        ticket.id = $ticketId";
        $stmt = $db->query($sql);
        $ticketRow = $stmt->fetch(PDO::FETCH_ASSOC);
        return $ticketRow;
    }

    public function comment(array $data) {
        $db = Db::getDb();
        $sql = 'SELECT  comment.content,
                        user,
                        user_type
                    FROM comment
                    WHERE comment.ticket_id = ' . $data['ticket'] . ' ORDER BY time DESC, id DESC';
        $stmt = $db->query($sql);
        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $comments;
    }

}
