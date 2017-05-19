<?php

class AskController extends Zend_Controller_Action {

    public function indexAction() {
        try {
            ZendX_Csrf::prevent($_SERVER['HTTP_REFERER']);
            $session = new Zend_Session_Namespace();
            if (!isset($session->customerEmail)) {
                throw new InvalidArgumentException('Permission denied');
            }
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
            $this->view->addScriptPath(APPLICATION_PATH.'/admin/views/scripts');
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
                $session = new Zend_Session_Namespace;
                if (!isset($session->customerEmail)) {
                    throw new InvalidArgumentException('Missing required customerEmail');
                }
                $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                $select = $db->select()->from('customer', array('id'))->where('name = ?', $session->customerEmail);
                $userId = $db->fetchOne($select);
                $ticket = new ZendX_Ticket();
                $ticketId = $ticket->commentPost($_POST['ticketId'], $_POST['comment'], $userId, 1);
            } catch (InvalidArgumentException $e) {
                exit('Argument Invalid');
            } catch (Exception $e) {
                exit('Server error');
            }
            $this->redirect("/Ask/index?ticket=$ticketId");
            exit;
        }
    }

    public function closeAction() {
        try {
            ZendX_Csrf::prevent($_SERVER['HTTP_REFERER']);
            $session = new Zend_Session_Namespace();
            if (!isset($session->customerEmail)) {
                throw new InvalidArgumentException('Missing required customerEmail');
            }
            if (!isset($_GET['ticket'])) {
                throw new InvalidArgumentException('Missing required ticket');
            }
            $ticketId = filter_var($_GET['ticket'], FILTER_VALIDATE_INT, array(
                'options' => array('min_range' => 1)
            ));
            if (!$ticketId) {
                throw new InvalidArgumentException('Ticket id is invalid');
            }
            $ticket = new ZendX_Ticket();
            $ticket->close($session->customerEmail, $ticketId);
        } catch (InvalidArgumentException $e) {
            exit($e->getMessage());
        } catch (Exception $e) {
            exit($e->getMessage());
        }
        $this->redirect("/Ask/index?ticket=$ticketId");
    }

}
