<?php

class OurTicket_Controller_CustomerServiceAction extends OurTicket_Controller_Action
{
    protected $customerService;

    public function preDispatch()
    {
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
            $this->redirect('/login');
        }
        
        $this->customerService = $auth->getIdentity();
        $this->view->customerService = $this->customerService;
        
        $this->setLayout('admin_layout');
    }
}
