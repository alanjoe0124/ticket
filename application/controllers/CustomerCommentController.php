<?php

class CustomerCommentController extends OurTicket_Controller_CustomerAction
{
    public function indexAction()
    {
        $ticketId = OurTicket_Util::DBAIPK($this->getQuery('id'));
        if ($_POST) {
            try {
                OurTicket_Util::killCSRF();
                OurTicket_Ticket::customerAddComment(
                        $ticketId, $this->getPost('comment'), $this->getCustomerId()
                );
            } catch (InvalidArgumentException $e) {
                exit('invalid params');
            } catch (Exception $e) {
                exit('Server error');
            }
        }
        $this->view->ticketId = $ticketId;
        $this->view->customerEmail = $this->getCustomerEmail();
        $this->view->customerId = $this->getCustomerId();
    }
}
