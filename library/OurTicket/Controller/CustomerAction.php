<?php

class OurTicket_Controller_CustomerAction extends OurTicket_Controller_Action
{
    protected $session;

    public function init(){
        $this->session = new Zend_Session_Namespace();
    }

    public function getCustomerId()
    {
        return $this->session->customerId;
    }

    public function getCustomerEmail()
    {
        return $this->session->customerEmail;
    }

    public function setCustomerId($customerId)
    {
        $this->session->customerId = $customerId;
    }

    public function setCustomerEmail($customerEmail)
    {
        $this->session->customerEmail = $customerEmail;
    }
}
