<?php

class TicketController extends Zend_Controller_Action
{

    public function createAction()
    {
        header('Access-Control-Allow-Origin:http://ourblog.dev');
        if ($_POST) {
            try {
                MyLib_Csrf::prevent($_SERVER['HTTP_REFERER']);
                $ticket = new MyLib_Ticket();
                $ticket->create($_POST);
                $layout = $this->_helper->layout();
                $layout->disableLayout();
                $this->_helper->viewRenderer->setNoRender();
                echo 'success';
            } catch (InvalidArgumentException $e) {
                echo 'invalid argument';
            } catch (Exception $e) {
                echo 'server error';
            }
        }
        exit;
    }

}
