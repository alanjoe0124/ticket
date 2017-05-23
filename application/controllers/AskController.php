<?php

class AskController extends Zend_Controller_Action
{

    public function indexAction()
    {
        try {
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
            Zend_View_Helper_PaginationControl::setDefaultViewPartial('manage/controls.phtml');
            $paginator = Zend_Paginator::factory($select);
            $paginator->setCurrentPageNumber($this->_getParam('page', 1));
            $this->view->paginator = $paginator;

            $this->view->ticketId = $ticketId;
            $layout = Zend_Layout::getMvcInstance();
            $layout->setLayout('layout_index');
        } catch (InvalidArgumentException $e) {
            exit('Argument Invalid');
        } catch (Exception $e) {
            exit('Server error');
        }
    }

    public function commentPostAction()
    {
        if ($_POST) {
            try {
                MyLib_Csrf::prevent($_SERVER['HTTP_REFERER']);
                $session = new Zend_Session_Namespace;
                if (!isset($session->customerEmail)) {
                    throw new InvalidArgumentException('Missing required customerEmail');
                }
                $userId = Zend_Db_Table_Abstract::getDefaultAdapter()
                        ->fetchOne('SELECT id FROM customer WHERE email = ?', array($session->customerEmail));
                $ticket = new MyLib_Ticket($_POST['ticketId'], $userId, 1);
                $ticketId = $ticket->commentPost($_POST['comment']);
            } catch (InvalidArgumentException $e) {
                exit('Argument Invalid');
            } catch (Exception $e) {
                exit('Server error');
            }
            $this->redirect("/ask/index?ticket=$ticketId");
            exit;
        }
        exit;
    }

    public function closeAction()
    {
        try {
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
            $ticket = new MyLib_Ticket();
            $ticket->close($session->customerEmail,$ticketId);
        } catch (InvalidArgumentException $e) {
            exit('Argument Invalid');
        } catch (Exception $e) {
            exit('Server error');
        }
        $this->redirect("/ask/index?ticket=$ticketId");
    }

}
