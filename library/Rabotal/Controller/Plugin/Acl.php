<?php

class Rabotal_Controller_Plugin_Acl extends Zend_Controller_Plugin_Abstract {
    
    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        parent::preDispatch($request);

        $front = Zend_Controller_Front::getInstance();
        
        $dispatcher = $front->getDispatcher();
        $controllerClass = $dispatcher->loadClass($dispatcher->getControllerClass($request));
        $controller = new $controllerClass($request, $front->getResponse(), $front->getParams());
        
        $actionMethod = $dispatcher->getActionMethod($request);
        $actions = get_class_methods($controller);
        
        if ( !$dispatcher->isDispatchable($request) || !in_array($actionMethod, $actions) ) {
            return $controller->__call($request->getActionName(), $request->getParams());
        }
        
        $bootstrap = $front->getParam('bootstrap');
        $acl = $bootstrap->getResource('Acl');
        $user = $bootstrap->getResource('User');
        
        $role = (empty($user)) ? 'guest' : $user->role;
        
        $resources = $acl->getResources();
        if ( !in_array($request->getControllerName(), $resources) ) {
            trigger_error("Resource '{$request->getControllerName()}' doesn't match any configured resources.
                Please, add it into acl.xml configuration file", E_USER_ERROR);
        }
        
        if ( !$acl->isAllowed($role, $request->getControllerName(), $request->getActionName()) ) {
            throw new Rabotal_Exception_Access('Access denied!', 403);
        }
    }
}
