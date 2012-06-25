<?php

class Rabotal_Controller_Plugin_FillHead extends Zend_Controller_Plugin_Abstract {
    /**
     * @param Zend_Controller_Request_Abstract $request 
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        parent::preDispatch($request);
        
        $action = $request->getActionName();
        $controller = $request->getControllerName();
        $view = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer')->view;
        $bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');
        $siteOptions = $bootstrap->getOption('site');
        
        $view->headScript()
            // Common JS functions
            ->appendFile('/js/library.js');
        
        $view->headTitle($siteOptions['default']['title'], 'SET');
        
        $view->action = $action;
        $view->controller = $controller;
    }
}
