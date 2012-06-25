<?php

class Rabotal_View_Helper_Avatar extends Zend_View_Helper_Abstract {
    public function avatar($user = NULL, $alt = NULL) {
        $options = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOption('site');
        $avatar = $options['default']['avatar'];
        $_alt = '';
        
        if ( !$user ) {
            return '';
        
        } else if ( $user instanceof Zend_Db_Table_Row && !empty($user->avatar) && FALSE === strpos($user->avatar, 'http') && 
                file_exists($options['avatar']['path'].$user->avatar) ) {
            $avatar = $options['avatar']['url'].$user->avatar;
            $_alt = ($alt) ? $alt : $this->view->escape($user->fullname);
            
        } else if ( $user instanceof Zend_Db_Table_Row && !empty($user->avatar) && 0 === strpos($user->avatar, 'http') ) {
            $avatar = $user->avatar;
            $_alt = ($alt) ? $alt : $this->view->escape($user->fullname);
            
        } else if ( is_string($user) && FALSE === strpos($user, 'http') && file_exists($options['avatar']['path'].$user) ) {
            $avatar = $options['avatar']['url'].$user;
            $_alt = $alt;
        } else if ( is_string($user) && 0 === strpos($user, 'http') ) {
            $avatar = $user;
            $_alt = $alt;
        }
        
        return '<img class="avatar" src="'.$avatar.'" alt="'.$_alt.'" />';
    }
}
