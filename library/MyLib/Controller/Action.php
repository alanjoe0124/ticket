<?php

class MyLib_Controller_Action extends Zend_Controller_Action
{

    public function preDispatch()
    {
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
            $this->redirect('/login');
        }
    }

    public function logoutAction()
    {
        $session = new Zend_Session_Namespace();
        if (!isset($session->uid)) {
            $this->redirect('/login');
            exit;
        }
        Zend_Session::destroy();
        $this->redirect('/login');
        $this->_helper->viewRenderer->setNoRender();
    }

}
