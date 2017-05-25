<?php

class IndexController extends OurTicket_Action
{
    public function indexAction()
    {
        try {
            $email = OurTicket_Util::validateEmail(OurTicket_Util::getQuery('email'));
        } catch (InvalidArgumentException $e) {
            exit('invalid email');
        }

        $session = new Zend_Session_Namespace();
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        if (!isset($session->customerId) || $session->customerEmail != $email) {
            $customerId = $db->fetchOne('SELECT id FROM customer WHERE email = ?', array($email));
            if (!$customerId) {
                $db->insert('customer', array('email'=>$email));
                $customerId = $db->lastInsertId();
            }
            Zend_Session::regenerateId();
            $session->customerId = $customerId;
            $session->customerEmail = $email;
        }
    }
}
