<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initPlugins()
    {
        // Plug-ins
        $front = $this->bootstrap('FrontController')->getResource('FrontController');
        $front->registerPlugin(new Rabotal_Controller_Plugin_Acl);
        $front->registerPlugin(new Rabotal_Controller_Plugin_FillHead);
        
        // Helpers
        Zend_Controller_Action_HelperBroker::addPrefix('Rabotal_Controller_Action_Helpers');
        
        return NULL;
    }
    
    protected function _initView()
    {
        $this->bootstrap('DB');
        
        $view = new Zend_View;
        $view->addHelperPath('Rabotal/View/Helper', 'Rabotal_View_Helper_');
        $view->doctype('HTML5');
        $view->headMeta()->setCharset('utf-8');
        
        // Initialize common variables
        $view->user = $this->bootstrap('User')->getResource('User');
        $view->role = $this->bootstrap('Role')->getResource('Role');
        $view->acl  = $this->bootstrap('Acl')->getResource('Acl');
        $view->options = $this->getOption('site');
        
        // Set view
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
        $viewRenderer->setView($view);
        
        return $view;
    }
    
    protected function _initTraslator() {
        $siteOptions = $this->getOption('site');
        
        $translator = new Zend_Translate(
            'array',
            APPLICATION_PATH.DIRECTORY_SEPARATOR.'languages',
            $siteOptions['default']['language'],
            array('scan' => Zend_Translate::LOCALE_DIRECTORY)
        );

        Zend_Validate_Abstract::setDefaultTranslator($translator);
        
        return $translator;
    }
    
    protected function _initRotes() {
        $router = $this->bootstrap('FrontController')->getResource('FrontController')->getRouter();
        
        $route = new Zend_Controller_Router_Route(
            'project/add/:uniq_id',
            array('controller' => 'project', 'action' => 'add')
        );
        $router->addRoute('project-add', $route);
        
        return $router;
    }

    protected function _initUser()
    {
        $this->bootstrap('Db');
        
        $auth = Rabotal_Auth::getInstance();
        $userTable = new Rabotal_Model_Users;
        $user = NULL;
        
        if ( !$auth->hasIdentity() && !empty($_COOKIE['uid']) && !empty($_COOKIE['ask']) ) {
            $_user = $userTable->find((int)$_COOKIE['uid'])->current();
            if ( $_user && $_user->auto_signin_key === $_COOKIE['ask'] ) {
                Rabotal_Auth::identityWrite($_user);
            } else {
                $auth->clearIdentity();
            }
            unset($_user);
        }
        
        if ( $auth->hasIdentity() ) {
            $user = $userTable->find($auth->getIdentity()->id)->current();
            
            if ( !$user ) {
                $auth->clearIdentity();
                
            } else {   
                $userProfile = $user->findDependentRowset('Rabotal_Model_UsersProfile', 'User')->current();

                if ( $userProfile && $userProfile->forgot_key !== '' ) {
                    $userProfile->forgot_key = NULL;
                    $userProfile->save();
                }

                $user->id = (int)$user->id;
            }
        }

        return $user;
    }
    
    protected function _initRole()
    {
        $user = $this->bootstrap('User')->getResource('User');
        
        if ( $user && !empty($user->role) ) {
            return $user->role;
        }
        
        return 'guest';
    }
    
    protected function _initAcl() {
        $acl = new Rabotal_Acl();
        
        $acl->addRole(new Zend_Acl_Role('guest'))
            ->addRole(new Zend_Acl_Role('member'), 'guest')
            ->addRole(new Zend_Acl_Role('expert'), 'member')
            ->addRole(new Zend_Acl_Role('moderator'), 'expert')
            ->addRole(new Zend_Acl_Role('administrator'));
        
        $acl->deny();
        $acl->allow('administrator');
        
        $resources = array();
        $roles = array('guest', 'member', 'expert', 'moderator');
        $aclConfig = new Zend_Config_Xml(APPLICATION_PATH.'/configs/acl.xml');
        
        foreach ( $roles as &$role ) {
            if ( !empty($aclConfig->{$role}) ) {
                if ( $aclConfig->{$role} ) {
                    foreach ( array('allow', 'deny') as $state ) {
                        if ( empty($aclConfig->{$role}->{$state}) )
                            continue;
                        foreach ( $aclConfig->{$role}->{$state} as $resource => $actions ) {
                            if ( !in_array($resource, $resources) ) {
                                $acl->add(new Zend_Acl_Resource($resource));
                                $resources[] = $resource;
                            }
                            $actions = $actions->toArray();
                            $acl->$state($role, $resource, $actions['action']);
                        }
                    }
                }
            }
        }
        
        return $acl;
    }
    
    protected function _initMailer()
    {
        $siteOptions = $this->getOption('site');
        $transport  = $this->bootstrap('Mail')->getResource('Mail');
        
        $mail = new Zend_Mail($siteOptions['default']['charset']);
        $mail->setDefaultTransport($transport);
        
        return $mail;
    }    
}
