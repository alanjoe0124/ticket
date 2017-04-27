<?php

class TicketCreateDb {

    public function get_user_id($email) {
        $pdo = new PDO(
                'mysql:host=localhost;port=3306;dbname=ticket_test;charset=utf8', 'root', ''
        );
        $sql = 'SELECT id FROM customer WHERE name = ?';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array($email));
        $userId = $stmt->fetchColumn();
        return $userId;
    }

    public function create_new_customer($email) {
        $pdo = new PDO(
                'mysql:host=localhost;port=3306;dbname=ticket_test;charset=utf8', 'root', ''
        );
        $sql = 'INSERT INTO customer( name ) VALUES( ? )';
        $stmt = $pdo->prepare($sql);
        $res = $stmt->execute(array($email));
        return $res; // ture or false
    }

    public function create_new_ticket($title, $desc, $userId) {
        $pdo = new PDO(
                'mysql:host=localhost;port=3306;dbname=ticket_test;charset=utf8', 'root', ''
        );
        $sql = 'INSERT INTO ticket(title, description, user, domain) VALUES(?, ?, ?, ?)';
        $stmt = $pdo->prepare($sql);
        $res = $stmt->execute(array(
            $title,
            $desc,
            $userId,
            'ourblog.dev'
        ));
        return $res; // true or false
    }

}
