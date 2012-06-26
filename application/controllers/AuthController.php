<?php

class AuthController extends Zend_Controller_Action
{   
    public function indexAction() {
        $this->_forward('sign-up');
    }

    public function signUpAction()
    {
        $request = $this->getRequest();
        $signUpForm = new Rabotal_Form_SignUp();
        $bootstrap = $this->getInvokeArg('bootstrap');
        $options = $bootstrap->getOption('site');
        $security = $bootstrap->getOption('secure');
        
        if ( $request->isPost() && $signUpForm->isValid($request->getPost()) ) {
            $file = $signUpForm->getElement('avatar');
            $file->setValueDisabled(true);
            $file->addFilter(new Rabotal_Filter_File_RenameImage($options['avatar']['path']));

            $values = $signUpForm->getValues();
            $values['avatar'] = '';
            unset($values['retype']);
            
            if ( $file->isUploaded() && $file->receive() ) {
                $values['avatar'] = substr($file->getFileName(), strlen($options['avatar']['path']));
            }
            
            $values['password'] = sha1($security['salt'].$values['password']);
            
            $users = new Rabotal_Model_Users;
            $usersProfile = new Rabotal_Model_UsersProfile;
            
            $values['date'] = time();
            $values['role'] = Rabotal_User_Enum_Roles::ROLE_DEFAULT;
            $values['status'] = Rabotal_User_Enum_Status::STATUS_DEFAULT;
            $values['auto_signin_key'] = '';
            
            $profile = array('fullname' => $values['fullname']);
            unset($values['fullname']);
            
            $uid = $users->insert($values);
            $profile['user_id'] = $uid;
            $usersProfile->insert($profile);
            
            $user = $users->find($uid)->current();
            Rabotal_Auth::identityWrite($user);
            
            $this->_redirect('/');
        }
        
        $this->view->signUpForm = $signUpForm;
    }

    public function signInAction()
    {
        $request = $this->getRequest();
        $signInForm = new Rabotal_Form_SignIn();
        
        if ( $request->isPost() && $signInForm->isValid($request->getPost()) ) {
            $values = $signInForm->getValues();
            $options = $this->getInvokeArg('bootstrap')->getOption('secure');
            
            $authAdapter = new Zend_Auth_Adapter_DbTable(
                $this->getInvokeArg('bootstrap')->getResource('DB'),
                'users',
                ( strpos($values['login'], '@') !== FALSE ) ? 'email' : 'username',
                'password',
                'sha1(?)'
            );
            
            $auth = Rabotal_Auth::getInstance();
            $authAdapter
                ->setIdentity($values['login'])
                ->setCredential($options['salt'].$values['password']);
            
            if ( $auth->authenticate($authAdapter)->isValid() ) {
                $userData = $authAdapter->getResultRowObject(array('id', 'username', 'email'));
                $auth->getStorage()->write($userData);
                if ( $values['rememberme'] ) {
                    Rabotal_Auth::remember($userData);
                }
                
                $this->_redirect('/');
            }
            $signInForm->addErrorMessage('Имя пользователя или пароль введены не верно. Пожалуйста, повторите попытку.');
        }
        
        $this->view->signInForm = $signInForm;
    }

    public function forgotAction()
    {
        $request = $this->getRequest();
        $forgotForm = new Rabotal_Form_ForgotPassword;
        
        if ( $request->isPost() && $forgotForm->isValid($request->getPost()) ) {
            $usersTable = new Rabotal_Model_Users;
            $user = $usersTable->getByEmailOrLogin($forgotForm->getValue('name'));
            
            if ( !$user ) {
                $forgotForm->getElement('name')->addError('Пользователь не найден');
            } else {
                $key = md5(microtime().$user->id.rand());
                $profile = $user->findDependentRowset('Rabotal_Model_UsersProfile', 'User')->current();
                
                
                $profile->forgot_key = $key;
                $profile->save();
                
                $this->view->token = $key;
                $this->view->baseUrl = Zend_Controller_Front::getInstance();
                $this->view->user = $user;
                $this->view->profile = $profile;    
                
                $mail = $this->getInvokeArg('bootstrap')->getResource('Mailer');
                $mail->setBodyHtml($this->view->render('mail/html/restore-password.phtml'));
                $mail->setBodyText($this->view->render('mail/txt/restore-password.phtml'));
                $mail->setSubject($this->view->render('mail/subject/restore-password.phtml'));
                $mail->addTo($user->email, $profile->fullname ? $profile->fullname : $user->username);
                $mail->send();
                
                $this->_helper->FlashMessenger->addMessage('pass-dropped');
                $this->_redirect('/auth/password-dropped');
            }
        }
        
        $this->view->forgotForm = $forgotForm;
    }

    public function restoreAction()
    {
        $request = $this->getRequest();
        $token = $request->getParam('token');
        $options = $this->getInvokeArg('bootstrap')->getOption('secure');
        
        if ( !$token ) $this->_redirect ('/');
        
        $userProfileTable = new Rabotal_Model_UsersProfile;
        
        $profile = $userProfileTable->fetchRow(array('forgot_key = ?' => $token));
        if ( !$profile ) {
            $this->view->invalidToken = true;
        } else {
            $this->view->invalidToken = false;
            
            $makeNewPasswordForm = new Rabotal_Form_MakeNewPassword(
                    array('action' => "/auth/restore/token/$token"));
            
            if ( $request->isPost() && $makeNewPasswordForm->isValid($request->getPost()) ) {
                $user = $profile->findParentRow('Rabotal_Model_Users', 'User');
                $user->password = sha1($options['salt'].$makeNewPasswordForm->getValue('password'));
                $user->save();
                
                $profile->forgot_password = NULL;
                $profile->save();
                
                $this->_helper->FlashMessenger->addMessage('pass-saved');
                $this->_redirect('/auth/password-saved');
            }
            
            $this->view->makeNewPasswordForm = $makeNewPasswordForm;
        }        
    }

    public function signOutAction()
    {
        Rabotal_Auth::getInstance()->clearIdentity();
        $this->_redirect('/');
    }

    public function passwordDroppedAction()
    {
        $messages = $this->_helper->FlashMessenger->getMessages();
        if ( empty($messages) || $messages[0] !== 'pass-dropped' )
            $this->_redirect ('/');
    }

    public function passwordSavedAction()
    {
        $messages = $this->_helper->FlashMessenger->getMessages();
        if ( empty($messages) || $messages[0] !== 'pass-saved' )
            $this->_redirect ('/');
    }
}
