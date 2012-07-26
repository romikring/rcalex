<?php

class ProjectController extends Zend_Controller_Action
{
    public function indexAction()
    {
    }

    public function addAction()
    {
        $request = $this->getRequest();
        $addProjectForm = new Rabotal_Form_AddProject;
        $categoriesTable = new Rabotal_Model_Categories;
        $auth = Rabotal_Auth::getInstance();
        
        $this->view->new_user = -1;
        
        // Fill form
        $categories = $categoriesTable->fetchAll(NULL, 'name ASC');
        if ( $categories->count() > 0 ) {
            $e = $addProjectForm->getElement('category');
            foreach ($categories as $row) {
                $e->addMultiOption($row->id, $row->name);
            }
        }
        
        if ( $request->isGet() ) {
            $addProjectForm->getElement('unique_lbl')->setValue(md5(microtime().rand()));
        }
        elseif ( $request->isPost() ) {
            $post = $request->getPost();
            
            // User authorized
            if ( $auth->hasIdentity() ) {
                $addProjectForm->removeElement('email');
                $addProjectForm->removeElement('password');
                $addProjectForm->removeElement('retype');
            }
            elseif ( isset($post['new_user']) ) {
                if ( (int)$post['new_user'] === 0 ) {
                    $this->view->new_user = 0;
                    $retype = $addProjectForm->getElement('retype');
                    $addProjectForm->removeElement('retype');
                } else {
                    $this->view->new_user = 1;
                    $addProjectForm->getElement('email')->addValidator('Db_NoRecordExists', false, array('table' => 'users', 'field' => 'email'));
                }
            } else {
                $addProjectForm->addErrorMessage('Вы должны указать являетесь ли Вы новым пользователем или уже регистрировались на сайте');
                $addProjectForm->markAsError();
            }

            if ( $addProjectForm->isValid($request->getPost()) ) {
                $values = $addProjectForm->getValues();
                
                if ( !$auth->hasIdentity() ) {
                    try {
                        if ( (int)$post['new_user'] === 0 ) $this->_signIn($values);
                        else                                $this->_signUp($values);
                    } catch ( Exception $e ) {
                        $addProjectForm->getElement('email')->addError($e->getMessage());
                        $addProjectForm->markAsError();
                    }
                }
                
                if ( !$addProjectForm->isErrors() ) {
                    $projectTable = new Rabotal_Model_Projects;
                    $projectFilesTable = new Rabotal_Model_ProjectsFiles;

                    $data = array(
                        'owner_id' => $auth->getIdentity()->id,
                        'title' => $values['name'],
                        'description' => $values['description'],
                        'status' => 'active',
                        'date' => time(),
                        'budget' => $values['budget'],
                        'demand_period' => 604800 * $values['period'] + time(),
                        'category_id' => $values['category'],
                        'sub_category_id' => $values['sub_category']
                    );
                    
                    if ( !empty($values['employee_place']) ) {
                        $placesTable = new Rabotal_Model_Places;
                        $data['performer_from_id'] = $placesTable->getIdByNameOrSave($values['employee_place']);
                    }
                    
                    $projectId = $projectTable->insert($data);

                    $projectFilesTable->update(
                        array('project_id' => $projectId),
                        $projectFilesTable->getAdapter()->quoteInto("label = ?", $values['unique_lbl'])
                    );
                }
            }
            
            if( !empty($retype) ) {
                $addProjectForm->addElement($retype);
            }
        }
        
        $this->view->addProjectForm = $addProjectForm;
        
        $this->view->headLink()->appendStylesheet('/css/fileuploader.css');
        $this->view->headScript()
            ->appendFile('/js/fileuploader.js')
            ->appendFile('/js/char.counter.js');
    }
    
    private function _signIn($formData)
    {
        $security = $this->getInvokeArg('bootstrap')->getOption('secure');

        $authAdapter = new Zend_Auth_Adapter_DbTable(
            $this->getInvokeArg('bootstrap')->getResource('DB'),
            'users',
            'email',
            'password',
            'sha1(?)'
        );

        $auth = Rabotal_Auth::getInstance();
        $authAdapter
            ->setIdentity($formData['email'])
            ->setCredential($security['salt'].$formData['password']);

        $result = $auth->authenticate($authAdapter);
        if ( $result->isValid() ) {
            Rabotal_Auth::identityWrite($authAdapter->getResultRowObject(array('id', 'username', 'email')));
        } else {
            throw new Zend_Exception("Неверное сочетание электронной почты и пароля");
        }
        
        return true;
    }
    
    private function _signUp($formData)
    {
        $security = $this->getInvokeArg('bootstrap')->getOption('secure');
        
        $usersTable = new Rabotal_Model_Users;
        $usersProfileTable = new Rabotal_Model_UsersProfile;
        
        $values = array(
            'email' => $formData['email'],
            'username' => $usersTable->mklogin(substr($formData['email'], 0, strpos($formData['email'], '@'))),
            'password' => sha1($security['salt'].$formData['password']),
            'avatar' => '',
            'role' => Rabotal_User_Enum_Roles::ROLE_DEFAULT,
            'date' => time(),
            'auto_signin_key' => '',
            'status' => Rabotal_User_Enum_Status::STATUS_DEFAULT
        );
        $userId = $usersTable->insert($values);
        
        $usersProfileTable->insert(array('user_id' => $userId));

        Rabotal_Auth::identityWrite(array('id' => $userId, 'username' => $values['username'], 'email' => $values['email']));
        
        return true;
    }

    public function uploadFileAction()
    {
    }
}
