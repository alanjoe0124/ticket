<?php

class Admin_ManageController extends Zend_Controller_Action {

    public function indexAction() {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $select = $db->select()
                ->from('status', array('status' => 'name'))
                ->joinInner('ticket', 'status.id = ticket.status', array('id', 'title', 'domain', 'time'))
                ->joinInner('customer', 'ticket.user = customer.id', array('customer' => 'name'))
                ->where('status = 1')
                ->order(array('time DESC', 'ticket.id DESC'));
        // ticket status ( 1 => pending, 2 => close )

        Zend_View_Helper_PaginationControl::setDefaultViewPartial('manage/controls.phtml');
        $data = $db->fetchAll($select);
        $paginator = Zend_Paginator::factory($data);
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));
        $this->view->paginator = $paginator;
    }

    public function logoutAction() {
        $session = new Zend_Session_Namespace();
        if (!isset($session->uid)) {
            $this->redirect('/admin/Login');
            exit;
        }
        Zend_Session::destroy();
        $this->redirect('/admin/Login');
        $this->_helper->viewRenderer->setNoRender();
    }

    public function preDispatch() {
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
            $this->redirect('/admin/Login');
        }
    }

}