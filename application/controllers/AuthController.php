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
    }

    public function restoreAction()
    {
    }

    public function signOutAction()
    {
        Rabotal_Auth::getInstance()->clearIdentity();
        $this->_redirect('/');
    }

    public function passwordDroppedAction()
    {
    }

    public function passwordSavedAction()
    {
    }
}
