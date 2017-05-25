<?php

class OurTicket_Action extends Zend_Controller_Action
{
    public function disableLayoutAndView()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
    }
}
