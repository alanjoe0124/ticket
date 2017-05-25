<?php

class LogoutController extends OurTicket_AdminAction
{
    public function indexAction()
    {
        $this->disableLayoutAndView();
        $session = Zend_Session::destroy();
        $this->redirect('/login/index');
    }
}
