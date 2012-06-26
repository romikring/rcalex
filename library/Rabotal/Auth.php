<?php

class Rabotal_Auth extends Zend_Auth {
    const DAYS_15 = 1296000;
    
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
    
    static public function identityWrite($identity)
    {
        if ( $identity instanceof Zend_Db_Table_Row_Abstract ) {
            self::getInstance()->getStorage()->write((object) array(
                'id' => $identity->id,
                'username' => $identity->username,
                'email' => $identity->email
            ));
            return true;
        }
        return false;
    }
    
    public function clearIdentity() {
        $config = new Zend_Config_Ini(APPLICATION_PATH.DIRECTORY_SEPARATOR.'configs'.DIRECTORY_SEPARATOR.'application.ini', APPLICATION_ENV);
        
        if ( $this->hasIdentity() ) {
            $usersTable = new Rabotal_Model_Users;
            $user = $usersTable->find($this->getIdentity()->id)->current();
            if ( $user ) {
                $user->auto_signin_key = '';
                $user->save();
            }
            
            parent::clearIdentity();
        }
        
        setcookie('uid', -1, time()-self::DAYS_15, '/', $config->site->default->domain);
        setcookie('ask', -1, time()-self::DAYS_15, '/', $config->site->default->domain);
        unset($_COOKIE['uid'], $_COOKIE['ask']);
    }
    
    static public function remember($userData) {
        $users = new Rabotal_Model_Users;
        $options = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOption('site');
        
        $key = md5(microtime().$userData->id.rand());
        $user = $users->find($userData->id)->current();
        $user->auto_signin_key = $key;
        $user->save();

        setcookie('uid', $userData->id, time() + self::DAYS_15, '/', $options['default']['domain']);
        setcookie('ask', $key, time() + self::DAYS_15, '/', $options['default']['domain']);
    }
}
