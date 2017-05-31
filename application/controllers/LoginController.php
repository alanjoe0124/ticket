<?php

class LoginController extends OurTicket_Controller_Action
{
    public function preDispatch()
    {
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $this->redirect('/manage/index');
        }

        $this->setLayout('login_layout');
    }

    public function indexAction()
    {
        $this->view->nameOrPwdWrong = $this->getQuery('nameOrPwdWrong');
    }

    public function doLoginAction()
    {
        OurTicket_Util::killCSRF();
        try {
            $adapter = new OurTicket_Login($this->getPost('name'), $this->getPost('pwd'));
        } catch (InvalidArgumentException $e) {
            die('invalid params');
        }

        $auth = Zend_Auth::getInstance();
        $result = $auth->authenticate($adapter);
        if ($result->isValid()) {
            Zend_Session::regenerateId();
            $this->redirect('/manage/index');
        }

        $this->redirect('/login?nameOrPwdWrong=1');
    }
}
