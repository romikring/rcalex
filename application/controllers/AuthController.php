<?php

class AuthController extends Zend_Controller_Action
{

    const DAYS_15 = 1296000;

    public function signUpAction()
    {
    }

    public function signInAction()
    {
    }

    public function forgotAction()
    {
    }

    public function restoreAction()
    {
    }

    public function signOutAction()
    {
    }

    public function passwordDroppedAction()
    {
    }

    public function passwordSavedAction()
    {
    }

    private function identityWrite($identity)
    {
        if ( $identity instanceof Zend_Db_Table_Row_Abstract ) {
            Zend_Auth::getInstance()->getStorage()->write((object) array(
                'id' => $identity->id,
                'username' => $identity->username,
                'email' => $identity->email
            ));
            return true;
        }
        return false;
    }
}
