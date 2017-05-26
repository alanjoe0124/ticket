<?php

class IndexController extends OurTicket_Controller_CustomerAction
{
    public function indexAction()
    {
        try {
            $email = OurTicket_Util::validateEmail($this->getQuery('email'));
        } catch (InvalidArgumentException $e) {
            exit('invalid email');
        }

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $customerId = $this->getCustomerId();
        if (!isset($customerId) || $this->getCustomerEmail() != $email) {
            $customerId = $db->fetchOne('SELECT id FROM customer WHERE email = ?', array($email));
            if (!$customerId) {
                $db->insert('customer', array('email' => $email));
                $customerId = $db->lastInsertId();
            }
            Zend_Session::regenerateId();
            $this->setCustomerId($customerId);
            $this->setCustomerEmail($email);
        }
        $select = $db->select()
                     ->from('ticket', array('id', 'title'))
                     ->join('status', 'ticket.status_id = status.id', array('status' => 'status.name'))
                     ->where('ticket.customer_id = ?', $customerId)
                     ->order(array('ticket.time DESC', 'ticket.id DESC'));
            Zend_View_Helper_PaginationControl::setDefaultViewPartial('/page_controls.phtml');
            $paginator = Zend_Paginator::factory($select);
            $paginator->setCurrentPageNumber($this->getRequest()->getQuery('page'));
        $this->view->paginator = $paginator;
    }
}
