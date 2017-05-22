<?php

class LoginController extends Zend_Controller_Action
{

    public function indexAction()
    {
        if ($_POST) {
            try {
                MyLib_Csrf::prevent($_SERVER['HTTP_REFERER']);
                $auth = Zend_Auth::getInstance();
                $adapter = new MyLib_Auth($_POST['userName'], $_POST['pwd']);
                $result = $auth->authenticate($adapter);
            } catch (InvalidArgumentException $e) {
                exit('Param error');
            } catch (Exception $e) {
                exit('Server error');
            }
            if ($result->isValid()) {
                $session = new Zend_Session_Namespace();
                Zend_Session::regenerateId();
                $session->uid = $result->getIdentity();
                $session->userName = $adapter->getUserName();
                $this->redirect('/manage');
                exit;
            } else {
                $this->redirect('/login/index?error=password_failed');
                exit;
            }
        }
        $layout = Zend_Layout::getMvcInstance();
        $layout->setLayout('layout_login');
    }

    public function preDispatch()
    {
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $this->redirect('/manage');
        }
    }

}
