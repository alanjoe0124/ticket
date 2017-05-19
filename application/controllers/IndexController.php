<?php

class IndexController extends Zend_Controller_Action {

    public function indexAction() {
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
        } catch (Exception $e) {
            exit("Param error");
        }
 
        $session = new Zend_Session_Namespace();
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $select = $db->select()->from('customer', array('id'))->where('name = ?', $email);
        $userId = $db->fetchOne($select);
        if (!isset($session->customerEmail)) {
            Zend_Session::regenerateId();
            $session->customerEmail = $email;
        }
        $this->view->userId = $userId;
    }
 
}