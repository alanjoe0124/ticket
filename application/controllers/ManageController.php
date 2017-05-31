<?php

class ManageController extends OurTicket_Controller_CustomerServiceAction
{
    public function indexAction()
    {
        $select = Zend_Db_Table_Abstract::getDefaultAdapter()
                  ->select()
                  ->from('ticket', array('id', 'title', 'domain', 'time'))
                  ->join('status', 'ticket.status_id = status.id', array('status' => 'status.name'))
                  ->join('customer', 'ticket.customer_id = customer.id', array('customer' => 'customer.email'))
                  ->order(array('ticket.time DESC', 'ticket.id DESC')); // 1-pending 2-close
                  
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('/page_controls.phtml');
        
        $paginator = Zend_Paginator::factory($select)
                     ->setCurrentPageNumber($this->getQuery('page'));
                     
        $this->view->paginator = $paginator;
    }
    
    public function detailsAction()
    {
        $ticketId = OurTicket_Util::DBAIPK($this->getQuery('id'));
        if (!$ticketId) {
            die('invalid ticket id');
        }

        $sql = 'SELECT  ticket.*,
                        status.name AS status,
                        customer.email AS customer
                FROM    ticket
                        INNER JOIN status ON ticket.status_id = status.id
                        INNER JOIN customer ON ticket.customer_id = customer.id
                WHERE
                        ticket.id = ' . $ticketId;
        $ticketDetails = Zend_Db_Table_Abstract::getDefaultAdapter()->fetchRow($sql);
        if (!$ticketDetails) {
            $this->redirect('/manage/index');
        }

        $this->view->ticketDetails = $ticketDetails;
    }
    
    public function addCommentAction()
    {
        $ticketId = $this->getPost('ticketId');

        try {
            OurTicket_Util::killCSRF();
            OurTicket_Ticket::customerServiceAddComment(
                    $ticketId,
                    $this->getPost('comment'),
                    $this->customerService['id']
            );
        } catch (InvalidArgumentException $e) {
            die('invalid params');
        } catch (Exception $e) {
            die('Server error');
        }

        $this->redirect('/manage/details?id=' . $ticketId);
    }

    public function logoutAction()
    {
        Zend_Session::destroy();
        $this->redirect('/login');
    }

}
