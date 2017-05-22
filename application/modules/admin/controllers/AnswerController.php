<?php

class Admin_AnswerController extends Zend_Controller_Action {

    public function indexAction() {
        try {
            if (!isset($_GET['ticket'])) {
                throw new InvalidArgumentException('Missing required ticket id');
            }
            $ticketId = filter_var($_GET['ticket'], FILTER_VALIDATE_INT, array(
                'options' => array('min_range' => 1)
            ));
            if (!$ticketId) {
                throw new InvalidArgumentException('Invalid ticket id');
            }
            $db = Zend_Db_Table_Abstract::getDefaultAdapter();
            $select = $db->select()
                    ->from('comment', array('content', 'time', 'user', 'user_type'))
                    ->where("comment.ticket_id = $ticketId")
                    ->order(array('time DESC', 'id DESC'));

            Zend_View_Helper_PaginationControl::setDefaultViewPartial('answer/controls.phtml');
            $data = $db->fetchAll($select);
            $paginator = Zend_Paginator::factory($data);
            $paginator->setCurrentPageNumber($this->_getParam('page', 1));
            $this->view->paginator = $paginator;

            $this->view->ticketId = $ticketId;
        } catch (InvalidArgumentException $e) {
            exit($e->getMessage());
        } catch (Exception $e) {
            exit($e->getMessage());
        }
    }

    public function commentPostAction() {

        if ($_POST) {
            try {
                ZendX_Csrf::prevent($_SERVER['HTTP_REFERER']);
                $session = new Zend_Session_Namespace();
                $ticket = new ZendX_Ticket();
                $ticketId = $ticket->commentPost($_POST['ticketId'], $_POST['comment'], $session->uid, 2);
            } catch (InvalidArgumentException $e) {
                exit('Argument Invalid');
            } catch (Exception $e) {
                exit('Server error');
            }
            $this->redirect("/admin/Answer/index?ticket=" . $ticketId);
        }
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