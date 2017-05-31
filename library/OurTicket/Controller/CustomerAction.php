<?php

class OurTicket_Controller_CustomerAction extends OurTicket_Controller_Action
{
    protected $customer;
    
    protected function handleCustomerLogin()
    {
        $email = $this->getQuery('email');
        if ($this->customer && $this->customer['email'] == $email) {
            return;
        }

        try {
            $email = OurTicket_Util::validateEmail($email);
        } catch (InvalidArgumentException $e) {
            exit('invalid email');
        }

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

        $customerId = $db->fetchOne('SELECT id FROM customer WHERE email = ?', array($email));
        if (!$customerId) {
            $db->insert('customer', array('email' => $email));
            $customerId = $db->lastInsertId();
        }

        Zend_Session::regenerateId();
        $ns = new Zend_Session_Namespace();
        $ns->customerId    = $customerId;
        $ns->customerEmail = $email;

        $this->customer = array(
            'id'    => $customerId,
            'email' => $email
        );
    }

    public function preDispatch()
    {
        $ns = new Zend_Session_Namespace();
        if ($ns->customerId) {
            $this->customer = array(
                'id'    => $ns->customerId,
                'email' => $ns->customerEmail
            );
        }

        if (   $this->getRequest()->getControllerName() == 'index'
            && $this->getRequest()->getActionName() == 'index'
        ) {
            $this->handleCustomerLogin();
        }

        if (!$this->customer) {
            die('you should not be here!');
        }

        $this->view->customer = $this->customer;
    }
}
