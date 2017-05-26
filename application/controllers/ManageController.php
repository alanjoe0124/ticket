<?php

class ManageController extends OurTicket_Controller_CustomerServiceAction
{
    public function indexAction()
    {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $select = $db
                ->select()
                ->from('ticket', array('id', 'title', 'domain', 'time'))
                ->join('status', 'ticket.status_id = status.id', array('status' => 'status.name'))
                ->join('customer', 'ticket.customer_id = customer.id', array('customer' => 'customer.email'))
                ->order(array('ticket.time DESC', 'ticket.id DESC')); // 1-pending 2-close
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('/page_controls.phtml');
        $paginator = Zend_Paginator::factory($select);
        $paginator->setCurrentPageNumber($this->getRequest()->getQuery('page'));
        $this->view->paginator = $paginator;
        $this->setLayout('admin_layout');
    }
}
