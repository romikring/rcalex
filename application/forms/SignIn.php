<?php

class Rabotal_Form_SignIn extends Zend_Form
{

    public function init()
    {
        $this->setAction('/auth/sign-in/')
            ->setMethod('post')
            ->setName('sign-in-form')
            ->setAttrib('id', 'sign-in-form');
        
        $this->addElement('text', 'login', array(
            'label' => 'Логин или E-mail:',
            'required'  => true,
            'validators'    => array('NotEmpty'),
            'filters'   => array('StringToLower', 'StringTrim')
        ));
        
        $this->addElement('password', 'password', array(
            'label' => 'Пароль:',
            'required'  => true,
            'validators' => array('NotEmpty')
        ));
        
        $this->addElement('checkbox', 'rememberme', array(
            'label' => 'Запомнить меня',
            'required' => false,
            'filters' => array('Boolean')
        ));
        
        $this->addElement('submit', 'submit', array(
            'label' => 'Войти'
        ));
    }
}
