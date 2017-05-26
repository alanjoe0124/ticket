<?php

class OurTicket_Controller_CustomerServiceAction extends OurTicket_Controller_Action
{
    protected $session;

    public function init(){
        $this->session = new Zend_Session_Namespace();
    }

    public function getCustomerServiceId()
    {
        return $this->session->customerServiceId;
    }

    public function getCustomerServiceName()
    {
        return $this->session->customerServiceName;
    }

    public function setCustomerServiceId($customerServiceId)
    {
        $this->session->customerServiceId = $customerServiceId;
    }

    public function setCustomerServiceName($customerServiceName)
    {
        $this->session->customerServiceName = $customerServiceName;
    }

    public function preDispatch()
    {
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
            $this->redirect('/login');
        }
    }

    public function logoutAction()
    {
        $this->disableLayoutAndView();
        $session = Zend_Session::destroy();
        $this->redirect('/login');
    }
}
