<?php

class ManageController extends MyLib_Controller_Action
{

    public function indexAction()
    {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $select = $db->select()
                ->from('status', array('status' => 'name'))
                ->join('ticket', 'status.id = ticket.status_id', array('id', 'title', 'domain', 'time'))
                ->join('customer', 'ticket.customer_id = customer.id', array('customer' => 'email'))
                ->where('status_id = 1')
                ->order(array('time DESC', 'ticket.id DESC'));
        // ticket status ( 1 => pending, 2 => close )
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('manage/controls.phtml');
        $paginator = Zend_Paginator::factory($select);
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));
        $this->view->paginator = $paginator;
    }

}
