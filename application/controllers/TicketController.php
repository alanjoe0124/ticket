<?php

class TicketController extends OurTicket_Action
{
    public function createAction()
    {
        try {
            $ticket = new OurTicket_Ticket();
            $ticket->create($_POST);
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
        $this->disableLayoutAndView();
    }
}
