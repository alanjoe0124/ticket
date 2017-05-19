<?php

class Admin_LoginController extends Zend_Controller_Action {

    public function indexAction() {
        if ($_POST) {
            try {
                $session = new Zend_Session_Namespace();
                ZendX_Csrf::prevent($_SERVER['HTTP_REFERER']);
                $auth = Zend_Auth::getInstance();
                $adapter = new ZendX_Auth($_POST['userName'], $_POST['pwd']);
                $result = $auth->authenticate($adapter);
            } catch (InvalidArgumentException $e) {
                exit('Param error');
            } catch (Exception $e) {
                exit('Server error');
            }
            if ($result->isValid()) {
                Zend_Session::regenerateId();
                $session->uid = $result->getIdentity();
                $session->userName = $adapter->getUserName();
                $this->redirect('/admin/Manage');
                exit;
            } else {
                $this->redirect('/admin/Login/index?error=password_failed');
                exit;
            }
        }
    }

    public function preDispatch() {
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $this->redirect('/admin/Manage');
        }
    }

}
