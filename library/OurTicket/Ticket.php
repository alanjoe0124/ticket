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

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $customerId = $db->fetchOne('SELECT id FROM customer WHERE email = ?', array($data['email']));

        $db->beginTransaction();
        try {
            if (!$customerId) {
                $db->insert('customer', array('email'=>$data['email']));
                $customerId = $db->lastInsertId();
            }
            $db->insert('ticket', array(
                'title'         =>  $data['title'], 
                'description'   =>  $data['description'],
                'customer_id'   =>  $customerId,
                'domain'        =>  $data['domain']
                ));
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    protected static function comment($ticketId, $comment, $userId, $userType)
    {
        $ticketId = OurTicket_Util::DBAIPK($ticketId);
        if (!$ticketId) {
            throw new InvalidArgumentException('invalid ticketId');
        }

        $len = mb_strlen($comment, 'UTF-8');
        if ($len == 0 || $len > 3000) {
            throw new InvalidArgumentException();
        }
        
        $sql = "SELECT id, customer_id FROM ticket WHERE id = $ticketId";
        $ticketRow = Zend_Db_Table_Abstract::getDefaultAdapter()->fetchRow($sql);
        if (!$ticketRow) {
            throw new InvalidArgumentException('no such ticketId');
        }
        if ($userType == 1 && $ticketRow['customer_id'] != $userId) {
            throw new InvalidArgumentException('not your ticket');
        }

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $db->insert('comment', array(
            'ticket_id' =>  $ticketId, 
            'content'   =>  $comment, 
            'user_id'   =>  $userId, 
            'user_type' =>  $userType
        ));
    }

    public static function customerAddComment($ticketId, $comment, $customerId)
    {
        self::comment($ticketId, $comment, $customerId, 1);
    }

    public static function customerServiceAddComment($ticketId, $comment, $customerServiceId)
    {
        self::comment($ticketId, $comment, $customerServiceId, 2);
    }

    public static function close($ticketId, $customerId)
    {
        $ticketId = OurTicket_Util::DBAIPK($ticketId);
        if (!$ticketId) {
            throw new InvalidArgumentException('invalid ticketId');
        }

        $sql = "SELECT id, status_id FROM ticket WHERE id = $ticketId AND customer_id = $customerId";
        $db  = Zend_Db_Table_Abstract::getDefaultAdapter();
        $row = $db->fetchRow($sql);
        if (!$row) {
            throw new InvalidArgumentException('ticketId not exists or not your ticket');
        }

        // 1-pending 2-close
        if ($row['status_id'] == '2') {
            return;
        }
        $db->update('ticket', array('status_id'=>2), "id = $ticketId");
    }
}
