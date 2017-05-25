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
}