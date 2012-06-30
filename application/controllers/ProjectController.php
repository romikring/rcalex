<?php

class ProjectController extends Zend_Controller_Action
{

    public function init()
    {
    }

    public function indexAction()
    {
    }

    public function addAction()
    {
        $request = $this->getRequest();
        $addProjectForm = new Rabotal_Form_AddProject;
        $auth = Rabotal_Auth::getInstance();
        
        if ( $auth->hasIdentity() ) {
            $addProjectForm->removeElement('email');
            $addProjectForm->removeElement('password');
            $addProjectForm->removeElement('retype');
        }
        
        if ( $request->isPost() && $addProjectForm->isValid($request->getPost()) ) {
            var_dump($request->getPost());
        }
        
        $this->view->addProjectForm = $addProjectForm;
        
        $this->view->headLink()->appendStylesheet('/css/fileuploader.css');
        $this->view->headScript()->appendFile('/js/fileuploader.js');
    }

    public function uploadFileAction()
    {
    }
}
