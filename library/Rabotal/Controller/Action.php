<?php

class Rabotal_Controller_Action extends Zend_Controller_Action {
    public function preDispatch() {
        parent::preDispatch();
        
        
        $request = $this->getRequest();    
        parent::__call($request->getActionName(), array());
        if (!$this->checkActionAccess($request->getControllerName(), $request->getActionName())) {
            throw new Rabotal_Exception_Access('Access denied!', 403);
        }
    }
    
    private function checkActionAccess($controller, $action) {
        $boostrap = $this->getInvokeArg('bootstrap');
        $acl = $boostrap->getResource('Acl');
        $user = $boostrap->getResource('User');
        
        $role = (empty($user)) ? 'guest' : $user->role;
        
        $resources = $acl->getResources();
        if ( !in_array($controller, $resources) ) {
            trigger_error("Resource '$controller' doesn't match any configured resources", E_USER_WARNING);
            return false;
        }
        
        return $acl->isAllowed($role, $controller, $action);
    }
}
