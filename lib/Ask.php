<?php

class Ask {

    public function post(array $post, array $session) {
        if (!isset($session['customerEmail'])) {
            throw new InvalidArgumentException('Missing required customerEmail');
        }
        $formParam = array('comment', 'ticketId');
        foreach ($formParam as $key) {
            if (!isset($post[$key])) {
                throw new InvalidArgumentException("Missing required $key");
            }
        }
        $commentLength = strlen($post['comment']);
        if ($commentLength > 64000 || $commentLength == 0) {
            throw new InvalidArgumentException('Comment max length 64000 and not empty');
        }
        $ticketId = filter_var($post['ticketId'], FILTER_VALIDATE_INT, array(
            'options' => array('min_range' => 1)
        ));
        if (!$ticketId) {
            throw new InvalidArgumentException('Invalid ticket id');
        }
        $db = Db::getDb();
        $sql = 'SELECT id FROM customer WHERE name = ?';
        $stmt = $db->prepare($sql);
        $stmt->execute(array($session['customerEmail']));
        $userId = $stmt->fetchColumn();

        $sql = 'INSERT INTO comment (content, user, ticket_id) VALUES (?, ?, ?)';
        $stmt = $db->prepare($sql);
        $stmt->execute(array($post['comment'], $userId, $ticketId));
        return $ticketId;
    }

}
