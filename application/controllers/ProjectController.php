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
        
        $projectTable = new Rabotal_Model_Projects;
        $uniq_id = $request->getParam('uniq_id', NULL);
        
        if ( empty($uniq_id) ) {
            // I want to sign project
            $uniq_id = md5(microtime().rand());
            $this->_redirect('/project/add/'.$uniq_id);
        } elseif( $projectTable->fetchRow($projectTable->getAdapter()->quoteInto('uniq_id = ?', $uniq_id)) !== NULL ) {
            // I want to create new project
            $uniq_id = md5(microtime().rand());
            $this->_redirect('/project/add/'.$uniq_id);
        } elseif( !preg_match('/^[a-f0-9]{32}$/', $uniq_id) ) {
            // validate project uniq_id
            $uniq_id = md5(microtime().rand());
            $this->_redirect('/project/add/'.$uniq_id);
        }
        
        $elUniqId = $addProjectForm->getElement('uniq_id');
        $elUniqId->setValue($uniq_id);
        $this->view->new_user = -1;
        
        // Fill form
        $categories = $categoriesTable->fetchAll(NULL, 'name ASC');
        if ( $categories->count() > 0 ) {
            $e = $addProjectForm->getElement('category');
            foreach ($categories as $row) {
                $e->addMultiOption($row->id, $row->name);
            }
        }
        
        if ( $request->isPost() ) {
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
                    $projectFilesTable = new Rabotal_Model_ProjectsFiles;

                    $data = array(
                        'owner_id' => $auth->getIdentity()->id,
                        'uniq_id' => $uniq_id,
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
                    
                    $projectTable->insert($data);
                    return $this->_redirect('/');
                }
            }
            
            if( !empty($retype) ) {
                $addProjectForm->addElement($retype);
            }
        }
        
        $projectFilesTable = new Rabotal_Model_ProjectsFiles;
        
        $this->view->addProjectForm = $addProjectForm;
        $this->view->projectFiles = $projectFilesTable->fetchAll($projectFilesTable->getAdapter()->quoteInto('project_uniq_id = ?', $uniq_id));
        
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
        $response = $this->getResponse();
        $request = $this->getRequest();
        Zend_Layout::getMvcInstance()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $options = $this->getInvokeArg('bootstrap')->getOption('site');
        
        $filename = $request->getParam('qqfile', NULL);
        $project_uid = $request->getParam('project_uid', NULL);
        
        if ( empty($filename) || empty($project_uid) ) {
            $response->setBody(json_encode(array('result' => false, 'message' => 'Ошибка запроса.')));
            return;
        }
        
        if ( !$request->isPost() ) {
            $response->setBody(json_encode(array('result' => false, 'message' => 'Доступ запрещен.')));
            return;
        }
        
        if ( FALSE === ($fileRawData = $request->getRawBody()) && !isset($_FILES['qqfile']) ) {
            return $response->setBody(json_encode(array("result" => false, "message" => "Ошибка загрузки файла.")));
        } elseif ( isset($_FILES['qqfile']) && is_uploaded_file($_FILES['qqfile']['tmp_name']) ) {
            if ( $_FILES['qqfile']['error'] !== UPLOAD_ERR_OK ) {
                return $response->setBody(json_encode(array("result" => false, "message" => "Ошибка загрузки файла.")));
            }
            $fileRawData = file_get_contents($_FILES['qqfile']['tmp_name']);
        }
        
        if ( empty($fileRawData) ) {
            return $response->setBody(json_encode(array("result" => false, "message" => "Ошибка загрузки файла.")));
        }
        
        $hash = md5($fileRawData);
        $dest = $options['project']['upload_path'].DIRECTORY_SEPARATOR.substr($hash, 0, 5).DIRECTORY_SEPARATOR;
        
        if ( !file_exists($dest) && FALSE === @mkdir($dest, 0755, true) ) {
            return $response->setBody(json_encode(array("result" => false, "message" => "Ошибка сервера: файл не сохранен.")));
        }
        
        if ( !is_writable($dest) ) {
            return $response->setBody(json_encode(array("result" => false, "message" => "Ошибка сервера: файл не сохранен.")));
        }
        
        if ( !file_put_contents($dest.$hash, $fileRawData) ) {
            return $response->setBody(json_encode(array("result" => false, "message" => "Ошибка сервера: файл не сохранен.")));
        }
        
        $projectFilesTable = new Rabotal_Model_ProjectsFiles;
        if ( $projectFilesTable->find($hash, $project_uid)->current() === NULL ) {
            $projectFilesTable->insert(array(
                'id' => $hash,
                'project_uniq_id' => $project_uid,
                'date' => time(),
                'name' => $filename,
                'size' => strlen($fileRawData)
            ));
        }

        return $response->setBody(json_encode(array("result" => true, 'message' => '', 'filename' => $filename)));
    }
}
