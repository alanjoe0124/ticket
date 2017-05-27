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

    public function closeAction()
    {
        if(!isset($this->session->customerId)){
            exit;
        }
        try {
            OurTicket_Util::killCSRF();
            $ticketId = $this->getQuery('id');
            OurTicket_Ticket::close($ticketId, $this->session->customerId);
        } catch (InvalidArgumentException $e) {
            die('invalid params');
        } catch (Exception $e) {
            die('server error');
        }
        $this->redirect("/customer-comment/index?id=$ticketId");
    }
}
