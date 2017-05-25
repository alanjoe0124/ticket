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
        $len = mb_strlen($comment, 'UTF-8');
        if ($len == 0 || $len > 3000) {
            throw new InvalidArgumentException();
        }
 
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $db->insert('comment', array(
            'ticket_id' =>  $ticketId, 
            'content'   =>  $comment, 
            'user_id'   =>  $userId, 
            'user_type' =>  $userType));
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
        if (!Zend_Db_Table_Abstract::getDefaultAdapter()->fetchOne($sql)) {
            throw new InvalidArgumentException('no such ticketId');
        }
        self::comment($ticketId, $comment, $customerServiceId, 2);
    }
}