<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

    protected function _initControllerPath() {
        $front = Zend_Controller_Front::getInstance();
        $front->setControllerDirectory(array(
            'default'   => APPLICATION_PATH . '/controllers',
            'admin'     => APPLICATION_PATH . '/admin/controllers'
        ));
    }

}
