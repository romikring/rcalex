<?php

class Rabotal_Form_ForgotPassword extends Zend_Form
{
    public function init()
    {
        $this->setAction('/auth/forgot/')
            ->setMethod('post')
            ->setName('forgot-password')
            ->setAttrib('id', 'forgot-password');
        
        $this->addElement('text', 'name', array(
            'required' => true,
            'label' => 'Имя пользователя или E-mail:',
            'validators' => array(
                array('NotEmpty', false, array()),
            ),
            'filters' => array(
                'StringTrim'
            )
        ));
        
        $this->addElement('submit', 'submit', array(
            'label' => 'Ок'
        ));
    }
}
