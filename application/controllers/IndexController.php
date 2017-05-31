<?php

class IndexController extends OurTicket_Controller_CustomerAction
{
    public function indexAction()
    {
        $select = Zend_Db_Table_Abstract::getDefaultAdapter()
                  ->select()
                  ->from('ticket', array('id', 'title'))
                  ->join('status', 'ticket.status_id = status.id', array('status' => 'status.name'))
                  ->where('ticket.customer_id = ?', $this->customer['id'])
                  ->order(array('ticket.time DESC', 'ticket.id DESC'));

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

        $sql = "SELECT  ticket.*,
                        status.name AS status
                FROM    ticket
                        INNER JOIN status ON ticket.status_id = status.id
                WHERE
                        ticket.id = $ticketId
                        AND ticket.customer_id = " . $this->customer['id'];
        $ticketDetails = Zend_Db_Table_Abstract::getDefaultAdapter()->fetchRow($sql);
        if (!$ticketDetails) {
            die('invalid ticket id');
        }

        $this->view->ticketDetails = $ticketDetails;
    }
    
    public function addCommentAction()
    {
        $ticketId = $this->getPost('ticketId');

        try {
            OurTicket_Util::killCSRF();
            OurTicket_Ticket::customerAddComment(
                    $ticketId,
                    $this->getPost('comment'),
                    $this->customer['id']
            );
        } catch (InvalidArgumentException $e) {
            die('invalid params');
        } catch (Exception $e) {
            die('Server error');
        }

        $this->redirect('/index/details?id=' . $ticketId);
    }

    public function closeAction()
    {
        $ticketId = $this->getQuery('id');

        try {
            OurTicket_Util::killCSRF();
            OurTicket_Ticket::close($ticketId, $this->customer['id']);
        } catch (InvalidArgumentException $e) {
            die('invalid params');
        } catch (Exception $e) {
            die('server error');
        }

        $this->redirect("/index/details?id=$ticketId");
    }
}
