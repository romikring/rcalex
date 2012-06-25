<?php

class Rabotal_Form_SignUp extends Zend_Form
{
    public function init()
    {
        $options = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOption('site');
        
        $this->setAction('/auth/sign-up/')
            ->setMethod('post')
            ->setName('sign-up')
            ->setAttrib('id', 'sign-up');
        
        $this->addElement('text', 'username', array(
            'label' => 'Логин:',
            'required' => true,
            'validators' => array(
                array('NotEmpty', false, array()),
                array('StringLength', false, array('min' => 3, 'max' => 20, 'encoding' => 'utf-8')),
                array('Regex', false, array('pattern' => '/^[a-z][0-9a-z_.]{2,19}$/i')),
                array('Db_NoRecordExists', false, array('table' => 'users', 'field' => 'username')),
            ),
            'filters' => array('StringTrim')
        ));
        
        $this->addElement('text', 'email', array(
            'label' => 'E-mail:',
            'required' => true,
            'validators' => array(
                array('NotEmpty', false, array()),
                array('EmailAddress', false, array('domain' => false)),
                array('Db_NoRecordExists', false, array('table' => 'users', 'field' => 'email')),
            ),
            'filters' => array('StringTrim')
        ));
        
        $this->addElement('password', 'password', array(
            'label' => 'Пароль:',
            'required' => true,
            'validators' => array(
                array('NotEmpty', false, array()),
                array('StringLength', false, array('min' => 5))
            ),
            'filters' => array('StringTrim')
        ));
        
        $this->addElement('password', 'retype', array(
            'label' => 'Повторите пароль:',
            'required' => true,
            'validators' => array(
                array('Identical', false, array('strict' => true, 'token' => 'password'))
            )
        ));
        
        $this->addElement('file', 'avatar', array(
            'required' => false,
            'label' => 'Изображение:',
            'validators' => array(
                array('Count', false, 1),
                array('Size', false, $options['avatar']['size']),
                array('Extension', false, $options['avatar']['ext'])
            )
        ));
        
        $this->addElement('text', 'fullname', array(
            'label' => 'Полное имя:',
            'required' => false,
            'validators' => array(
                array('StringLength', false, array('min' => 3, 'max' => 100, 'encoding' => 'utf-8'))
            ),
            'filters' => array('StringTrim')
        ));
        
        $this->addElement('submit', 'submit', array(
            'label' => 'Регистрация'
        ));
    }
}

