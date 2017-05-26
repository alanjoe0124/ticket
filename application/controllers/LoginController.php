<?php

class LoginController extends OurTicket_Controller_Action
{
    public function indexAction()
    {
        $session = new Zend_Session_Namespace();
        if (isset($session->customerServiceId)) {
            $this->redirect('/manage');
            exit;
        }

        $this->view->nameOrPwdWrong = false;

        if ($_POST) {
            try {
                OurTicket_Util::killCSRF();
                $auth = Zend_Auth::getInstance();
                $adapter = new OurTicket_Login($this->getPost('name'), $this->getPost('pwd'));
                $result = $auth->authenticate($adapter);
            } catch (InvalidArgumentException $e) {
                exit($e->getMessage());
            }

            if ($result->isValid()) {
                Zend_Session::regenerateId();
                $session->customerServiceId = $result->getIdentity();
                $session->customerServiceName = $adapter->getUserName();
                $this->redirect('/manage/index');
                exit;
            }

            $this->view->nameOrPwdWrong = true;
        }
        $this->setLayout('login_layout');
    }
}
