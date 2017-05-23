<?php

class IndexController extends Zend_Controller_Action
{

    public function indexAction()
    {
        try {
            if (!isset($_GET['email'])) {
                throw new InvalidArgumentException('Missing required email');
            }
            $emailLength = strlen($_GET['email']);
            if ($emailLength > 100 || $emailLength < 4) {
                throw new InvalidArgumentException('Email min length 4, max length 100');
            }
            $email = filter_var($_GET['email'], FILTER_VALIDATE_EMAIL);
            if (!$email) {
                throw new InvalidArgumentException('Email invalid');
            }
        } catch (InvalidArgumentException $e) {
            exit('Param error');
        }

        $session = new Zend_Session_Namespace();
        if (!isset($session->customerEmail)) {
            Zend_Session::regenerateId();
            $session->customerEmail = $email;
        }

        $customerId = Zend_Db_Table_Abstract::getDefaultAdapter()->fetchOne(
                'SELECT id FROM customer WHERE email = ?', array($email)
        );
        $this->view->customerId = $customerId;
        $layout = Zend_Layout::getMvcInstance();
        $layout->setLayout('layout_index');
    }

}
