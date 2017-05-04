<?php

class Answer {

    public function post(array $post, array $session) {

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
        $sql = 'INSERT INTO comment (content, user, ticket_id, user_type) VALUES (?, ?, ?, ?)';
        $stmt = $db->prepare($sql);
        $stmt->execute(array($post['comment'], $session['uid'], $ticketId, 2));
        // user_type ( 1 = > table(`customer`) , 2 => table (`user`)
        return $ticketId;
    }

}
