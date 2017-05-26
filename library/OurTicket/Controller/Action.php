<?php

class OurTicket_Controller_Action extends Zend_Controller_Action
{
    public function disableLayoutAndView()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
    }

    public function setLayout($name)
    {
        $this->_helper->layout->setLayout($name);
    }

    public function getQuery($key = null, $default = null)
    {
        return $this->getRequest()->getQuery($key, $default);
    }

    public function getPost($key = null, $default = null)
    {
        return $this->getRequest()->getPost($key, $default);
    }
}
