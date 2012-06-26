<?php

class Rabotal_Form_MakeNewPassword extends Zend_Form
{

    public function init()
    {
        $this
            ->setMethod('post')
            ->setName('restore-password')
            ->setAttrib('id', 'restore-password');
                
        $this->addElement('password', 'password', array(
            'label' => 'Новый пароль:',
            'required' => true,
            'validators' => array(
                array('NotEmpty', true, array()),
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
        
        $this->addElement('submit', 'submit', array(
            'label' => 'Сохранить'
        ));
    }
}
