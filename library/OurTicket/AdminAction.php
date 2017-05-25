<?php

class OurTicket_AdminAction extends OurTicket_Action
{
    public function preDispatch()
    {
        $session = new Zend_Session_Namespace();
        if (!isset($session->customerServiceId)) {
            $this->redirect('/login/index');
            exit;
        }
    }
}
