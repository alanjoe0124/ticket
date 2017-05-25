<?php

class LoginController extends OurTicket_Action
{

    public function indexAction()
    {
        $session = new Zend_Session_Namespace();
        if (isset($session->customerServiceId)) {
            $this->redirect('/manage/index');
            exit;
        }
        $session->nameOrPwdWrong = false;
        
        if ($_POST) {
            try {
                OurTicket_Util::killCSRF();
                $userRow = OurTicket_Login::doLogin($_POST);
            } catch (InvalidArgumentException $e) {
                exit('invalid params');
            }

            if ($userRow) {
                Zend_Session::regenerateId();
                $session->customerServiceId = $userRow['id'];
                $session->customerServiceName = $userRow['name'];
                $this->redirect('/manage/index');
                exit;
            }

            $session->nameOrPwdWrong = true;
        }
        $this->setLayout('login_layout');
    }

}
