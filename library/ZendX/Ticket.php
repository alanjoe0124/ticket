<?php

class ZendX_Ticket {

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

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

        $select = $db->select()->from('customer', array('id'))->where('name = ?');
        $stmt = $select->query(PDO::FETCH_ASSOC, array($data['email']));
        $customerId = $stmt->fetchColumn();
        $db->beginTransaction();
        try {
            if (!$customerId) {
                $db->insert('customer', array('name' => $data['email']));
                $customerId = $db->lastInsertId();
            }
            $stmt = $db->insert('ticket', array(
                'title' => $data['title'],
                'description' => $data['description'],
                'user' => $customerId,
                'domain' => $data['domain']));
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

}
