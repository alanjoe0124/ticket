<?php

class CloseController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $session = new Zend_Session_Namespace();
        if(!isset($session->customerId)){
            exit;
        }
        try {
            OurTicket_Util::killCSRF();
            $ticketId = OurTicket_Util::getQuery('id');
            OurTicket_Ticket::close($ticketId, $session->customerId);
        } catch (InvalidArgumentException $e) {
            die('invalid params');
        } catch (Exception $e) {
            die('server error');
        }
        $this->redirect("/comment/index?id=$ticketId");
    }
}
