<?php

class TicketController extends OurTicket_Controller_Action
{
    public function createAction()
    {
        $this->disableLayoutAndView();
        try {
            $ticket = new OurTicket_Ticket();
            $ticket->create($this->getPost());
            $res = 'success';
        } catch (InvalidArgumentException $e) {
            $res = $e->getMessage();
        } catch (Exception $e) {
            $res = 'server error';
        }

        if ($res == 'INVALID_DOMAIN') {
            exit;
        }

        header('Access-Control-Allow-Origin: http://ourblog.dev');
        echo $res;
    }
}
