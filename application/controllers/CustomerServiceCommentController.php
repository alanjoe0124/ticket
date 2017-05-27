<?php

class CustomerServiceCommentController extends OurTicket_Controller_CustomerServiceAction
{
    public function indexAction()
    {
        $this->setLayout('admin_layout');
        $ticketId = OurTicket_Util::DBAIPK($this->getQuery('id'));
        if ($_POST) {
            try {
                OurTicket_Util::killCSRF();
                OurTicket_Ticket::customerServiceAddComment(
                        $ticketId, $this->getPost('comment'), $this->getCustomerServiceId()
                );
            } catch (InvalidArgumentException $e) {
                exit('invalid params');
            } catch (Exception $e) {
                exit('Server error');
            }
        }
        $this->view->ticketId = $ticketId;
        $this->view->customerServiceName = $this->getCustomerServiceName();
        $this->view->customerServiceId = $this->getCustomerServiceId();
    }
}
